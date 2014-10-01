<?php

if (!defined("AMPLO_INSTALL")) {
	echo "Please call the Amplo MVC index.php in your installation root directory.";
	exit;
}

$error_msg   = '';
$success_msg = '';

$action = !empty($_POST['action']) ? $_POST['action'] : false;

$root = str_replace('system/install', '', rtrim(str_replace('\\', '/', dirname(__FILE__)), '/'));

if ($action === 'user_setup' && !is_file($root . 'config.php')) {
	$action    = false;
	$error_msg = "Unable to load config file. Please attempt installation again.";
}

if ($action === 'user_setup') {
	require_once($root . 'config.php');
} elseif (is_file($root . 'config.php')) {
	echo "There is a problem with your config.php file. Please fix it or remove it to reinstall Amplo MVC";
	exit;
} else {
	define("DIR_SITE", $root);
}

$uri_path = preg_replace("/\\?.*/", '', $_SERVER['REQUEST_URI']);

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

if (!defined("SITE_BASE")) {
	define("SITE_BASE", $uri_path);
}

define("DIR_DATABASE", DIR_SITE . 'system/database/');

require_once(DIR_SITE . 'system/helper/functions.php');
require_once(DIR_SITE . 'system/helper/shortcuts.php');

$template = _get('page', 'db');

if ($action) {
	switch ($action) {
		case 'db_setup':
			$result = setup_db();

			if ($result === true) {
				$success_msg = _l("You have successfully installed the database!");
				$template    = 'user';
			} else {
				$error_msg = $result;
			}
			break;

		case 'user_setup':
			$template = 'user';
			$result   = setup_user();

			if ($result === true) {
				$success_msg = _l("Admin User account setup successfully!");
			} else {
				$error_msg = $result;
			}
			break;

	}
}

switch ($template) {
	case 'db':
		$defaults = array(
			'db_driver'   => 'mysqlidb',
			'db_host'     => '',
			'db_name'     => '',
			'db_username' => '',
			'db_password' => '',
			'db_prefix'   => 'ac_',
		);
		break;

	case 'user':
		$defaults = array(
			'username' => '',
			'email'    => '',
			'password' => '',
			'confirm'  => '',
		);
		break;

	default:
		echo "Unknown installation page!";
		exit;
}

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

require_once("system/install/template/$template.tpl");

function setup_db()
{
	define("DB_PREFIX", $_POST['db_prefix']);
	define('DEFAULT_TIMEZONE', 'America/New_York');
	define('MYSQL_TIMEZONE', '-4:00');

	require_once(DIR_SITE . 'system/engine/model.php');
	require_once(DIR_SITE . "system/engine/library.php");
	require_once(DIR_SITE . "system/database/db.php");

	$db = new DB($_POST['db_driver'], $_POST['db_host'], $_POST['db_username'], $_POST['db_password'], $_POST['db_name']);

	$error = $db->getError();

	if (!$error) {
		$db_sql = DIR_SITE . 'system/install/db.sql';

		$contents = file_get_contents($db_sql);

		if (!$db->multiquery($contents)) {
			$error = $db->getError();
		} elseif (!$db->setPrefix(DB_PREFIX)) {
			$error = $db->getError();
		}
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
		'AMPLO_INSTALL_USER' => 1,
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

	return true;
}

function setup_user()
{
	if ($_POST['password'] !== $_POST['confirm']) {
		$_POST['password'] = $_POST['confirm'] = '';

		return _l("The password and confirmation do not match!");
	}

	require_once(DIR_SITE . 'system/engine/model.php');
	require_once(DIR_SITE . "system/engine/library.php");
	require_once(DIR_SITE . "system/database/database.php");
	require_once(DIR_SITE . "system/database/db.php");

	$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

	$username = $db->escape($_POST['username']);
	$email    = $db->escape($_POST['email']);
	$password = $db->escape(password_hash($_POST['password'], PASSWORD_DEFAULT, array('cost' => PASSWORD_COST)));

	$db->query("DELETE FROM " . DB_PREFIX . "user WHERE email = '$email' OR username = '$username'");
	$db->query("INSERT INTO " . DB_PREFIX . "user SET user_role_id = '1', firstname = 'Admin', username = '$username', email = '$email', password = '$password', status = '1', date_added = 'NOW()'");

	if ($db->hasError()) {
		return $db->getError();
	}

	//remove user install configuration
	$config   = DIR_SITE . 'config.php';
	$contents = set_define(file_get_contents($config), "AMPLO_INSTALL_USER");
	file_put_contents($config, $contents);

	//Start the session so we can send a message for the new user
	$domain = parse_url(URL_SITE, PHP_URL_HOST);

	if (!$domain || $domain === 'localhost') {
		define('COOKIE_DOMAIN', '');
	} else {
		define('COOKIE_DOMAIN', '.' . $domain);
	}

	ini_set('session.use_cookies', 'On');
	ini_set('session.use_trans_sid', 'Off');

	session_name(AMPLO_SESSION);

	session_set_cookie_params(0, '/', COOKIE_DOMAIN);
	session_start();

	$_SESSION['message'] = array(
		'success' => array(_l("Admin User account setup successfully!")),
	);

	header("Location: " . URL_SITE . 'admin');
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
