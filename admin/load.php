<?php
// Registry
$registry = new Registry();

// Request
$registry->set('request', new Request());

// Loader
$registry->set('load', new Loader($registry));

// Database
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$registry->set('db', $db);

// Cache
$cache = new Cache();
$registry->set('cache', $cache);

//TODO: WE NEED TO SEPARATE OUT ADMIN CONFIG FROM FRONT END CONFIGS (and common in both front / back and front only)!!

//config is self assigning to registry in order to use immediately!
$config = new Config($registry);

//Setup Cache ignore list
$cache->ignore($config->get('config_cache_ignore'));

//System Logging
$error_log = new Log($config->get('config_error_filename'), 'Admin');
$registry->set('error_log', $error_log);

$log = new Log($config->get('config_log_filename'), 'Admin');
$registry->set('log', $log);

$error_handler = function($errno, $errstr, $errfile, $errline) use($error_log, $config){
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
		
	if ($config->get('config_error_display')) {
		echo '<b>' . $error . '</b>: ' . $errstr . ' in <b>' . $errfile . '</b> on line <b>' . $errline . '</b><br /><br />';
		flush(); //Flush the error to block any redirects that may execute, this ensure errors are seen!
	}
	
	if ($config->get('config_error_log')) {
		$error_log->write('PHP ' . $error . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
	}

	return true;
};

set_error_handler($error_handler);

//Validate the necessary directories are writable
_is_writable(DIR_IMAGE, $config->get('config_image_dir_mode'));
_is_writable(DIR_IMAGE . 'cache/', $config->get('config_image_dir_mode'));
_is_writable(DIR_DOWNLOAD, $config->get('config_default_dir_mode'));
_is_writable(DIR_LOGS, $config->get('config_default_dir_mode'));

// Session
$registry->set('session', new Session($registry));

// Url
$registry->set('url', new Url($registry));

//Database Structure Validation
$row = $db->queryRow("SHOW GLOBAL STATUS WHERE Variable_name = 'com_alter_table' AND Value > '" . (int)$cache->get('db_last_update') . "'");

if ($row) {
	$cache->delete('model');
	$cache->set('db_last_update', $row['Value']);
}

//Images
$registry->set('image', new Image($registry));

// Response
$response = new Response();
$response->addHeader('Content-Type: text/html; charset=utf-8');
$registry->set('response', $response);

// Language
$registry->set('language', new Language($registry));

//Plugins (self assigning to registry)
$plugin = new Plugin($registry);

// Document
$registry->set('document', new Document($registry));

//Theme
$registry->set('theme', new Theme($registry));

//Initialize site configurations
$config->run_site_config();

// Front Controller
$controller = new Front($registry);
$controller->routeAdmin();

// Dispatch
$controller->dispatch();

// Output
$response->output();