<?php

// Registry
$registry = new Registry();

// Database
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$registry->set('db', $db);

//TODO: Maybe make this our main handler for loading (move out of registry)??
spl_autoload_register(function ($class) {
	global $registry;
	$registry->loadClass($class, false);
});

//Initialize Router
$router = new Router();
$registry->set('route', $router);

// Request (cleans globals)
$registry->set('request', new Request());

// Cache
$cache = new Cache();
$registry->set('cache', $cache);

//TODO: WE NEED TO SEPARATE OUT ADMIN CONFIG FROM FRONT END CONFIGS (and common in both front / back and front only)!!

//config is self assigning to registry.
$config = new Config();

//Setup Cache ignore list
$cache->ignore(option('config_cache_ignore'));

//Database Structure Validation
$last_update = $db->queryRow("SHOW GLOBAL STATUS WHERE Variable_name = 'com_alter_table' AND Value > '" . (int)$cache->get('db_last_update') . "'");

if ($last_update) {
	$cache->delete('model');
	$cache->set('db_last_update', $last_update['Value']);
}

//System Logs
$error_log = new Log('error', option('store_id'));
$registry->set('error_log', $error_log);

$log = new Log('default', option('store_id'));
$registry->set('log', $log);

//Error Callbacks allow customization of error display / messages
$error_callbacks = array();

$error_handler = function ($errno, $errstr, $errfile, $errline, $errcontext) use ($error_log, $config) {
	// error was suppressed with the @-operator
	if (!ini_get('display_errors') || 0 === error_reporting()) {
		return false;
	}

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
		if (option('config_error_display')) {
			$stack = get_caller(1, 10);

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

		if (option('config_error_log')) {
			$error_log->write('PHP ' . $error . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
		}
	}

	return true;
};

set_error_handler($error_handler);

//Verify the necessary directories are writable
$dir_error = null;
if (!_is_writable(DIR_IMAGE, $dir_error, option('config_image_dir_mode'))) {
	trigger_error($dir_error);
	die ($dir_error);
}
if (!_is_writable(DIR_IMAGE . 'cache/', $dir_error, option('config_image_dir_mode'))) {
	trigger_error($dir_error);
	die ($dir_error);
}
if (!_is_writable(DIR_DOWNLOAD, $dir_error, option('config_default_dir_mode'))) {
	trigger_error($dir_error);
	die ($dir_error);
}

//Customer Override (alternative logins)
if (!defined("AC_CUSTOMER_OVERRIDE")) {
	define("AC_CUSTOMER_OVERRIDE", substr(str_shuffle(md5(microtime())), 0, (int)rand(15, 20)));
}

// Session
$registry->set('session', new Session());

//Mod Files
$registry->set('mod', new Mod());

//Theme
$registry->set('theme', new Theme());

// Url
$registry->set('url', new Url());

// Response
$response = new Response();
$response->addHeader('Content-Type', 'text/html; charset=UTF-8');
$response->setCompression(option('config_compression'));
$registry->set('response', $response);

//Plugins (self assigning to registry)
$plugin = new Plugin();

//Cron Called from system
if (option('config_cron_status')) {
	if (defined("RUN_CRON")) {
		echo $registry->get('cron')->run();
		exit;
	} //Cron Called from browser
	elseif (isset($_GET['run_cron'])) {
		$result = $registry->get('cron')->run();
		echo nl2br($result);
		exit;
	} //Check if poor man's cron should run
	elseif (option('config_cron_check')) {
		$registry->get('cron')->check();
	}
}

//PHP Info
if (isset($_GET['phpinfo']) && $registry->get('user')->isTopAdmin()) {
	phpinfo();
	exit;
}

//Router
$router->route();
$router->dispatch();

// Output
$response->output();
