<?php
//TODO: Move to Dev plugin
//Virtual time (for simulating time progression)
$ac_time_offset = (int)@file_get_contents(DIR_SYSTEM . 'timeoffset');

function _time()
{
	global $ac_time_offset;
	return time() + $ac_time_offset;
}

function _filemtime($file)
{
	global $ac_time_offset;
	return filemtime($file) + ($ac_time_offset * 1000);
}

//Sim time
if (!empty($_GET['sim_time'])) {
	global $ac_time_offset;

	$time_file = DIR_SYSTEM . 'timeoffset';
	if ($_GET['sim_time'] === 'reset') {
		$ac_time_offset = 0;
	} else {
		$ac_time_offset = (int)@file_get_contents($time_file) + (int)$_GET['sim_time'];
	}

	file_put_contents($time_file, $ac_time_offset);
}

// Registry
$registry = new Registry();

//TODO: Maybe make this our main handler for loading (move out of registry)??
spl_autoload_register(function ($class) use ($registry) {
	$registry->loadClass($class, false);
});

//Language
global $language_group;
$language_group = "Load";

/**
 * Translate a string to the current requested language
 *
 * @param $message
 * @return mixed|string
 */
function _l($message)
{
	//TODO: Set translations based on language group
	global $language_group;

	$values = func_get_args();

	array_shift($values);

	//TODO: See bitbucket issue https://bitbucket.org/newms87/dopencart/issue/20/language-translation-engine
	if (empty($values)) {
		return _($message);
	}

	return vsprintf(_($message), $values);
}

/**
 * Change Language Group just for this message, then revert back if $message is given.
 * If $message is null, then the language group is changed permanently.
 *
 * @param $group - The language group to change to.
 * @param $message - The Message
 * @param $var1 , $var2, etc.. The variables to pass to vsprintf() with the message.
 *
 * @return null | String with the translated message
 */

function _lg($group, $message = null)
{
	global $language_group;

	//Permanently change Group.
	if ($message === null) {
		$language_group = $group;
		return;
	}

	//Temporarily Change Group
	$temp           = $language_group;
	$language_group = $group;

	$params = func_get_args();
	array_shift($params);

	$return = call_user_func_array('_l', $params);

	$language_group = $temp;

	return $return;
}

// Request (cleans globals)
$registry->set('request', new Request($registry));

// Database
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$registry->set('db', $db);
$db->query("SET time_zone='" . MYSQL_TIMEZONE . "'");

// Cache
$cache = new Cache($registry);
$registry->set('cache', $cache);

//TODO: WE NEED TO SEPARATE OUT ADMIN CONFIG FROM FRONT END CONFIGS (and common in both front / back and front only)!!

//config is self assigning to registry.
$config = new Config($registry);

//Setup Cache ignore list
$cache->ignore($config->get('config_cache_ignore'));

if (!defined("DB_PROFILE")) {
	define("DB_PROFILE", $config->get('config_db_profile'));
	define("DB_PROFILE_CACHE", $config->get('config_db_profile_cache'));
}

//Database Structure Validation
$row = $db->queryRow("SHOW GLOBAL STATUS WHERE Variable_name = 'com_alter_table' AND Value > '" . (int)$cache->get('db_last_update') . "'");

if ($row) {
	$cache->delete('model');
	$cache->set('db_last_update', $row['Value']);
}

//System Logs
$error_log = new Log(AC_LOG_ERROR_FILE, $config->get('config_store_id'));
$registry->set('error_log', $error_log);

$log = new Log(AC_LOG_FILE, $config->get('config_store_id'));
$registry->set('log', $log);

//Error Callbacks allow customization of error display / messages
$error_callbacks = array();

$error_handler = function ($errno, $errstr, $errfile, $errline, $errcontext) use ($error_log, $config) {
	switch ($errno) {
		case E_NOTICE:
		case E_USER_NOTICE:
			$error = 'Notice';
			break;
		case E_WARNING:
		case E_USER_WARNING:
			$error = 'Warning';
			break;
		case E_ERROR:
		case E_USER_ERROR:
			$error = 'Fatal Error';
			break;
		default:
			$error = 'Unknown';
			break;
	}

	global $error_callbacks;

	if (!empty($error_callbacks)) {
		foreach ($error_callbacks as $cb) {
			$cb($error, $errno, $errstr, $errfile, $errline);
		}
	}

	if ($error) {
		if ($config->get('config_error_display')) {
			echo '<b>' . $error . '</b>: ' . _l($errstr) . ' in <b>' . $errfile . '</b> ' . _l('on line') . ' <b>' . $errline . '</b><br /><br />';
			flush(); //Flush the error to block any redirects that may execute, this ensures errors are seen!
		}

		if ($config->get('config_error_log')) {
			$error_log->write('PHP ' . $error . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
		}
	}

	return true;
};

set_error_handler($error_handler);

//Verify the necessary directories are writable
_is_writable(DIR_IMAGE, $config->get('config_image_dir_mode'));
_is_writable(DIR_IMAGE . 'cache/', $config->get('config_image_dir_mode'));
_is_writable(DIR_DOWNLOAD, $config->get('config_default_dir_mode'));
_is_writable(DIR_LOGS, $config->get('config_default_dir_mode'));

//Customer Override (alternative logins)
if (!defined("AC_CUSTOMER_OVERRIDE")) {
	define("AC_CUSTOMER_OVERRIDE", substr(str_shuffle(md5(microtime())), 0, (int)rand(15, 20)));
}

// Session
$registry->set('session', new Session($registry));

//Mod Files
$registry->set('mod', new Mod($registry));

//Theme
$registry->set('theme', new Theme($registry));

// Url
$registry->set('url', new Url($registry));

// Response
$response = new Response($registry);
$response->addHeader('Content-Type: text/html; charset=utf-8');
$response->setCompression($config->get('config_compression'));
$registry->set('response', $response);

//Plugins (self assigning to registry)
$plugin = new Plugin($registry);

//Cron
if (isset($_GET['run_cron'])) {
	echo nl2br($registry->get('cron')->run());
	exit;
} elseif ($config->get('config_cron_status')) {
	$registry->get('cron')->check();
}

//PHP Info
if (isset($_GET['phpinfo']) && $registry->get('user')->isAdmin()) {
	phpinfo();
	exit;
}

//Router
$router = new Router($registry);
$router->route();
$router->dispatch();

// Output
$response->output();
