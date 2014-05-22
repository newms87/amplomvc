<?php

if (!defined("AMPLO_INSTALL")) {
	echo "Please call the Amplo MVC index.php in your installation root directory.";
	exit;
}

$root = str_replace('system/install', '', rtrim(str_replace('\\', '/', dirname(__FILE__)), '/'));
if (!is_file($root . 'ac_config.php')) {
	define("DIR_SITE", $root);
}
else {
	require_once($root . 'ac_config.php');
}

define("DIR_DATABASE", DIR_SITE . 'system/database/');

require_once(DIR_SITE . 'system/functions.php');

$template    = !empty($_GET['page']) ? $_GET['page'] : 'db';
$error_msg   = '';
$success_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['action'])) {
	switch ($_POST['action']) {
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
			'db_type'     => 'mysqlidb',
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

foreach ($defaults as $key => $default) {
	if (isset($_POST[$key])) {
		$$key = $_POST[$key];
	} else {
		$$key = $default;
	}
}


$logo = "image/data/ac_logo.png";

$db_types = array(
	'mysqlidb' => "MySQL",
	'mmsql'    => "MMSQL",
	'odbc'     => "ODBC",
	'postgre'  => "Postgre",
	'sqlite'   => "SQLite",
);

require_once("system/install/install_{$template}.tpl");

function setup_db()
{
	define("DB_PREFIX", $_POST['db_prefix']);

	require_once(DIR_SITE . 'system/engine/model.php');
	require_once(DIR_SITE . "system/engine/library.php");
	require_once(DIR_SITE . "system/database/db.php");

	$db = new DB($_POST['db_type'], $_POST['db_host'], $_POST['db_username'], $_POST['db_password'], $_POST['db_name']);

	$error = $db->getError();

	if ($error) {
		return $error;
	}

	$db_prefix = DB_PREFIX;

	$db_sql = DIR_SITE . 'system/install/db.sql';

	$contents = file_get_contents($db_sql);

	$contents = str_replace("%__TABLE_PREFIX__%", DB_PREFIX, $contents);

	if (!$db->multiquery($contents)) {
		return $db->getError();
	}

	$config_template = DIR_SITE . 'system/install/config_template.php';
	$ac_config       = DIR_SITE . 'ac_config.php';

	$contents = file_get_contents($config_template);

	$url = $_SERVER["SERVER_NAME"];

	if ($_SERVER["SERVER_PORT"] !== "80") {
		$url .= ":" . $_SERVER["SERVER_PORT"];
	}

	if (strpos($_SERVER['REQUEST_URI'], 'index.php')) {
		$uri = dirname($_SERVER["REQUEST_URI"]);
	} else {
		$uri = $_SERVER['REQUEST_URI'];
	}

	$url .= rtrim($uri, '/');

	$patterns = array(
		"/%domain%/"       => $url,
		'/%db_type%/'        => $_POST['db_type'],
		'/%db_name%/'        => $_POST['db_name'],
		'/%db_host%/'        => $_POST['db_host'],
		'/%db_username%/'    => $_POST['db_username'],
		'/%db_password%/'    => $_POST['db_password'],
		'/%db_prefix%/'      => $_POST['db_prefix'],
		'/%time_zone_name%/' => "America/New_York",
		'/%time_zone%/'      => "-4:00",
		'/%password_cost%/'  => getCostBenchmark(),
	);

	$contents = preg_replace(array_keys($patterns), array_values($patterns), $contents);

	//Allows for user installation (will be removed after user installation
	$contents .= "\r\n\r\ndefine(\"AMPLO_INSTALL_USER\", 1);";

	file_put_contents($ac_config, $contents);

	//Setup .htaccess file
	$htaccess_template = DIR_SITE . 'system/install/template.htaccess';
	$htaccess          = DIR_SITE . '.htaccess';

	$contents = file_get_contents($htaccess_template);

	$contents = preg_replace("/%base%/", $uri, $contents);

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

	$username   = $db->escape($_POST['username']);
	$email      = $db->escape($_POST['email']);
	$password   = $db->escape(password_hash($_POST['password'], PASSWORD_DEFAULT, array('cost' => PASSWORD_COST)));
	$ip         = $_SERVER['REMOTE_ADDR'];
	$date_added = date('Y-m-d H:i:s', time());

	$db->query("DELETE FROM " . DB_PREFIX . "user WHERE email = '$email' OR username = '$username'");
	$db->query("INSERT INTO " . DB_PREFIX . "user SET user_group_id = '1', firstname = 'Admin', username = '$username', email = '$email', password = '$password', ip = '$ip', status = '1', date_added = '$date_added'");

	if ($db->getError()) {
		return $db->getError();
	}

	$ac_config = DIR_SITE . 'ac_config.php';

	//remove user install configuration
	$contents = file_get_contents($ac_config);

	$contents = str_replace("\r\n\r\ndefine(\"AMPLO_INSTALL_USER\", 1);", '', $contents);

	file_put_contents($ac_config, $contents);

	//Start the session so we can send a message for the new user
	ini_set('session.use_cookies', 'On');
	ini_set('session.use_trans_sid', 'Off');
	session_name(AMPLO_SESSION);
	session_set_cookie_params(0, '/', COOKIE_DOMAIN);
	session_start();

	$_SESSION['messages'] = array(
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

exit;
