<?php
//SIM TIME
//TODO: Move to Dev plugin
//Virtual time (for simulating time progression)
$ac_time_offset = !empty($_COOKIE['ac_time_offset']) ? (int)$_COOKIE['ac_time_offset'] : 0;

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

//Only allow logged in users to sim time.
if (empty($_SESSION['user_id'])) {
	$ac_time_offset = 0;
} elseif (!empty($_GET['sim_time'])) {
	if ($_GET['sim_time'] === 'reset') {
		$ac_time_offset = 0;
	} else {
		$ac_time_offset += (int)$_GET['sim_time'];
	}
}



// Registry
$registry = new Registry();

//TODO: Maybe make this our main handler for loading (move out of registry)??
spl_autoload_register(function ($class) use ($registry) {
	$registry->loadClass($class, false);
});

// Request (cleans globals)
$registry->set('request', new Request($registry));

// Database
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$registry->set('db', $db);

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
}

if (!defined("DB_PROFILE_CACHE")) {
	define("DB_PROFILE_CACHE", $config->get('config_db_profile_cache'));
}

$db->query("SET time_zone='" . MYSQL_TIMEZONE . "'");

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
	// error was suppressed with the @-operator
	if (0 === error_reporting()) { return false;}

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
			$stack = get_caller(1,10);

			echo <<<HTML
			<style>
				.error_display {
					padding: 10px;
					border-radius: 5px;
					background: white;
					color: black;
					font-size: 14px;
					border: 1px solid black;
				}
				.error_display .label {
					width: 70px;
					display:inline-block;
					font-weight: bold;
				}

				.error_display a {
					color: blue;
				}
			</style>
			<div class="error_display">
				<div class="type"><span class="label">Type:</span> <span class="value">$error</span></div>
				<div class="msg"><span class="label">Message:</span> <span class="value">$errstr</span></div>
				<div class="file"><span class="label">File:</span> <span class="value">$errfile</span></div>
				<div class="line"><span class="label">Line:</span> <span class="value">$errline</span></div>
				<div class="stack">$stack</div>
			</div>
HTML;

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
$dir_error = null;
if (!_is_writable(DIR_IMAGE, $dir_error, $config->get('config_image_dir_mode'))) {
	trigger_error("%s", $dir_error);
}
if (!_is_writable(DIR_IMAGE . 'cache/', $dir_error, $config->get('config_image_dir_mode'))) {
	trigger_error("%s", $dir_error);
}
if (!_is_writable(DIR_DOWNLOAD, $dir_error, $config->get('config_default_dir_mode'))) {
	trigger_error("%s", $dir_error);
}
if (!_is_writable(DIR_LOGS, $dir_error, $config->get('config_default_dir_mode'))) {
	trigger_error("%s", $dir_error);
}

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
$registry->set('route', $router);
$router->route();
$router->dispatch();

// Output
$response->output();
