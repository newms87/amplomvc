<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!defined("AMPLO_INSTALL")) {
	echo "Please call the Amplo MVC index.php in your installation root directory.";
	exit;
}

if (!defined('DIR_SITE')) {
	define("DIR_SITE", str_replace('system/install', '', rtrim(str_replace('\\', '/', dirname(__FILE__)), '/')));
}

$uri_path = preg_replace("/\\?.*/", '', $_SERVER['REQUEST_URI']);

if (!defined("SITE_BASE")) {
	define("SITE_BASE", $uri_path);
}

//Redirect to site base
if (strpos(DIR_SITE, $uri_path) === false) {
	while (strpos(DIR_SITE, $uri_path) === false) {
		$uri_path = dirname($uri_path);

		if (!$uri_path || $uri_path === '/') {
			echo "UNABLE TO LOCATE SERVER ROOT. Please point your browser to the root directory of your Amplo MVC installation";
			exit;
		}
	}

	header("Location: " . $uri_path);
	exit;
}

//No Mod files allowed during site install!
function _mod($file) {
	return $file;
}

require_once(DIR_SITE . 'system/startup.php');

$msg = array(
	'error' => '',
   'success' => '',
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	//This method will redirect and exit, or return an error message
	$msg['error'] = amplo_mvc_install();
}

amplo_mvc_setup_form($msg);

function amplo_mvc_setup_form($msg)
{
	$defaults = array(
		'db_driver'   => 'mysqlidb',
		'db_host'     => '',
		'db_name'     => '',
		'db_username' => '',
		'db_password' => '',
		'db_prefix'   => 'am_',
		'username' => '',
		'email'    => '',
		'password' => '',
		'confirm'  => '',
	);

	$data = $_POST + $defaults;

	extract($data);

	$name = "Amplo MVC";
	$logo = "app/view/theme/admin/image/logo.png";

	$db_drivers = array(
		'mysqlidb' => "MySQL",
		'mmsql'    => "MMSQL",
		'postgre'  => "Postgre",
		'sqlite'   => "SQLite",
	);

	require_once("system/install/install.tpl");
}

function amplo_mvc_install()
{
	if (empty($_POST['username'])) {
		return _l("You must provide a username.");
	}

	if ($_POST['password'] !== $_POST['confirm']) {
		$_POST['password'] = $_POST['confirm'] = '';

		return _l("The password and confirmation do not match!");
	}

	define('DB_PREFIX', $_POST['db_prefix']);

	$db = new DB($_POST['db_driver'], $_POST['db_host'], $_POST['db_username'], $_POST['db_password'], $_POST['db_name']);

	$error = $db->getError();

	if (!$error) {
		$db_sql = DIR_SITE . 'system/install/db.sql';

		$contents = file_get_contents($db_sql);

		if (!$db->multiquery($contents)) {
			$error = $db->getError();
		} elseif (!$db->setPrefix($_POST['db_prefix'])) {
			$error = $db->getError();
		}
	}

	$username = $db->escape($_POST['username']);
	$email    = $db->escape($_POST['email']);
	$password = $db->escape(password_hash($_POST['password'], PASSWORD_DEFAULT, array('cost' => PASSWORD_COST)));

	$db->query("DELETE FROM " . DB_PREFIX . "user WHERE email = '$email' OR username = '$username'");
	$db->query("INSERT INTO " . DB_PREFIX . "user SET user_role_id = '1', firstname = 'Admin', username = '$username', email = '$email', password = '$password', status = '1', date_added = 'NOW()'");

	if ($db->hasError()) {
		$error = $db->getError();
	}

	if ($error) {
		if (is_array($error)) {
			$error = implode("<br>", $error);
		}
		return $error;
	}

	$config_template = DIR_SITE . 'example-config.php';
	$config          = DIR_SITE . 'config.php';

	$contents = file_get_contents($config_template);

	$defines = array(
		'SITE_BASE'          => SITE_BASE,
		'DB_DRIVER'          => $_POST['db_driver'],
		'DB_DATABASE'        => $_POST['db_name'],
		'DB_HOSTNAME'        => $_POST['db_host'],
		'DB_USERNAME'        => $_POST['db_username'],
		'DB_PASSWORD'        => $_POST['db_password'],
		'DB_PREFIX'          => $_POST['db_prefix'],
		'PASSWORD_COST'      => getCostBenchmark(),
	);

	foreach ($defines as $key => $value) {
		$contents = set_define($contents, $key, $value);
	}

	file_put_contents($config, $contents);

	//Setup .htaccess file
	$htaccess_template = DIR_SITE . 'example.htaccess';
	$htaccess          = DIR_SITE . '.htaccess';

	$contents = file_get_contents($htaccess_template);

	$contents = str_replace("RewriteBase /", "RewriteBase " . SITE_BASE, $contents);

	file_put_contents($htaccess, $contents);

	$_SESSION['message'] = array(
		'success' => array(_l("Amplo MVC has been installed successfully!")),
	);

	header("Location: " . URL_SITE . 'admin');

	exit;
}

function getCostBenchmark()
{
	$timeTarget = 0.2;
	$cost       = 9;

	do {
		$cost++;
		$start = microtime(true);
		password_hash("test", PASSWORD_DEFAULT, array("cost" => $cost));
		$end = microtime(true);
	} while (($end - $start) < $timeTarget);

	return $cost;
}

function set_define($string, $key, $value = null, $quotes = true)
{
	if (!is_null($value)) {
		$define = "define(\"$key\", " . ($quotes ? "\"$value\"" : $value) . ");";

		$count  = 0;
		$string = preg_replace("/define\\(\\s*['\"]{$key}['\"]\\s*,[^)]+?\\);/", $define, $string, 1, $count);

		if (!$count) {
			$string .= "\r\n\r\n" . $define;
		}

		return $string;
	}

	//Remove this entry
	return preg_replace("/define\\(['\"]{$key}['\"]\\s*,[^)]+?\\);\\s*/", '', $string);
}

exit;
