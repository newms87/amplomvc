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
$db->query("SET time_zone='" . MYSQL_TIMEZONE . "'");

// Cache
$cache = new Cache($registry);
$registry->set('cache', $cache);

//Config is self assigning to registry.
$config = new Config($registry);

//Setup Cache ignore list
$cache->ignore($config->get('config_cache_ignore'));

//System Logs
$error_log = new Log($config->get('config_error_filename'), $config->get('config_name'));
$registry->set('error_log', $error_log);

$log = new Log($config->get('config_log_filename'), $config->get('config_name'));
$registry->set('log', $log);

//Error Callbacks allow customization of error display / messages
$error_callbacks = array();

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

	global $error_callbacks;

	if (!empty($error_callbacks)) {
		foreach ($error_callbacks as $cb) {
			$cb($error, $errno, $errstr, $errfile, $errline);
		}
	}

	if ($error) {
		if ($config->get('config_error_display')) {
			echo '<b>' . $error . '</b>: ' . $errstr . ' in <b>' . $errfile . '</b> on line <b>' . $errline . '</b><br /><br />';
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

// Session
$registry->set('session', new Session($registry));

//Mod Files
$registry->set('mod', new Mod($registry));

// Url
$url = new Url($registry);
$registry->set('url', $url);

//Images
$registry->set('image', new Image($registry));

//Database Structure Validation
$row = $db->queryRow("SHOW GLOBAL STATUS WHERE Variable_name = 'com_alter_table' AND Value > '" . (int)$cache->get('db_last_update') . "'");

if ($row) {
	$cache->delete('model');
	$cache->set('db_last_update', $row['Value']);
}

// Response
$response = new Response();
$response->addHeader('Content-Type: text/html; charset=utf-8');
$response->setCompression($config->get('config_compression'));
$registry->set('response', $response);

// Language
$registry->set('language', new Language($registry));

//Plugins (self assigning to registry)
$plugin = new Plugin($registry);

// Document
$registry->set('document', new Document($registry));

//Affiliate Tracking
if (isset($_GET['tracking']) && !isset($_COOKIE['tracking'])) {
	setcookie('tracking', $_GET['tracking'], time() + 3600 * 24 * 1000, '/');
}

//Theme
$registry->set('theme', new Theme($registry));

//Resolve Layout ID
$layout = $db->queryRow("SELECT layout_id FROM " . DB_PREFIX . "layout_route WHERE '" . $db->escape($url->getPath()) . "' LIKE CONCAT(route, '%') AND store_id = '" . $config->get('config_store_id') . "' ORDER BY route ASC LIMIT 1");

if ($layout) {
	$config->set('config_layout_id', $layout['layout_id']);
} else {
	$config->set('config_layout_id', $config->get('config_default_layout_id'));
}

// Front Controller
$controller = new Front($registry);
$controller->routeFront();

// Dispatch
$controller->dispatch();

// Output
$response->output();

//TODO: try to move this so it is valid HTML
//Performance Logging
if ($config->get('config_performance_log')) {
	global $__start;
	$stats = array(
		'peak_memory' => $registry->get('tool')->bytes2str(memory_get_peak_usage(true)),
		'count_included_files' => count(get_included_files()),
		'execution_time' => microtime(true) - $__start,
	);

	foreach ($stats as $key => $s) {
		echo "$key = $s<br>";
	}
}
