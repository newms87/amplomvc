<?php
// Version
define('VERSION', '1.5.2.222');

//data
define('DATETIME_ZERO','0000-00-00 00:00:00');

$__start = microtime(true);

if (isset($_GET['phpinfo'])) {
	phpinfo();
	exit;
}

//DN CUSTOM FUNCTIONS
include('functions.php');

// Configuration
require_once('oc_config.php');

//System / URL Paths
require_once('path_config.php');

/*  PRETTY LANGUAGE TESTING */
echo 'testing pretty language<br /><br />';
require_once(DIR_SYSTEM . 'library/pretty_language.php');
new PrettyLanguage();
echo '<br /><br />pretty_language_done';
exit;
//*/


// Install
if (!defined('DIR_APPLICATION')) {
	$install = SITE_URL . 'oc/install/index.php';
	if(file_exists($install))
		header('Location: ' . $install);
	else
		echo "Could not find installation file";
	exit;
}

//File Merge for plugins
require_once(DIR_SYSTEM . 'file_merge.php');

// System Bootstrap
_require_once(DIR_SYSTEM . 'startup.php');

// Registry
$registry = new Registry();

// Loader
$loader = new Loader($registry);
$registry->set('load', $loader);

// Database
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$registry->set('db', $db);
$db->query("SET time_zone='" . MYSQL_TIMEZONE . "'");

// Cache
$cache = new Cache($registry);
$registry->set('cache', $cache);

//Resolve Store ID
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
	$scheme = 'https://';
	$field = 'ssl';
}
else {
	$scheme = 'http://';
	$field = 'url';
}

$url = $scheme . str_replace('www.', '', $_SERVER['HTTP_HOST']) . rtrim(dirname($_SERVER['PHP_SELF']), '/.\\') . '/';

$store = $db->query_row("SELECT * FROM " . DB_PREFIX . "store WHERE `$field` = '" . $db->escape($url) . "'");

$store_id = $store ? (int)$store['store_id'] : null;

// Config
$config = new Config($registry, $store_id);

//Setup Cache ignore list
foreach(explode(',',$config->get('config_cache_ignore')) as $ci)
	$cache->ignore($ci);

//System Logs
$error_log = new Log($config->get('config_error_filename'), $config->get('config_name'));
$registry->set('error_log', $error_log);

$log = new Log($config->get('config_log_filename'), $config->get('config_name'));
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
	}
	
	if ($config->get('config_error_log')) {
		$error_log->write('PHP ' . $error . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
	}

	return true;
};

set_error_handler($error_handler);

//Verify the necessary directories are writable
_is_writable(DIR_IMAGE, $config->get('config_image_dir_mode'));
_is_writable(DIR_IMAGE . 'cache/', $config->get('config_image_dir_mode'));
_is_writable(DIR_DOWNLOAD, $config->get('config_default_dir_mode'));
_is_writable(DIR_LOGS, $config->get('config_default_dir_mode'));


// Request
$request = new Request();
$registry->set('request', $request);

// Session
$session = new Session($registry);
$registry->set('session', $session);

//Messages
$registry->set('message', new Message($session));

// Url
$url = new Url($registry, $config->get('config_url'), $config->get('config_use_ssl') ? $config->get('config_ssl') : '');
if($config->get('config_seo_url'))
	$url->getSeoUrl();
$registry->set('url', $url);

if (!isset($_GET['route'])) {
	$_GET['route'] = 'common/home';
}

//Images
$registry->set('image', new Image($registry));

//Database Structure Validation
$db_last_update = $cache->get('db_last_update');
if (!$db_last_update) {
	$db_last_update = 0;
}
$query = $db->query("SHOW GLOBAL STATUS WHERE Variable_name = 'com_alter_table' AND Value > '$db_last_update'");
if ($query->num_rows) {
	$cache->delete('model');
	$cache->set('db_last_update', $query->row['Value']);
}

// Response
$response = new Response();
$response->addHeader('Content-Type: text/html; charset=utf-8');
$response->setCompression($config->get('config_compression'));
$registry->set('response', $response);

// Language
$registry->set('language', new Language($registry));

//Plugins
$plugin_handler = new pluginHandler($registry, $merge_registry);
$registry->set('plugin_handler', $plugin_handler);

// Document
$document = new Document($registry);
$document->setCanonicalLink($url->get_pretty_url());
$registry->set('document', $document);

if (isset($_GET['tracking']) && !isset($_COOKIE['tracking'])) {
	setcookie('tracking', $_GET['tracking'], time() + 3600 * 24 * 1000, '/');
}

//Theme
$registry->set('theme', new Theme($registry));

// Front Controller
$controller = new Front($registry);

// Router
$route = '';
$action = '';

if (isset($_GET['route'])) {
	$part = explode('/', $_GET['route']);
	
	if (isset($part[0])) {
		$route .= $part[0];
	}
	
	if (isset($part[1])) {
		$route .= '/' . $part[1];
	}
}

if ($config->get('config_maintenance')) {
	if ((!$registry->get('user')->isLogged() || !$registry->get('user')->isAdmin()) && strpos($route, 'payment') !== 0) {
		//$action = new Action('common/maintenance');
		$_GET['route'] = 'common/maintenance';
	}
}
elseif (!$route) {
	$_GET['route'] = 'common/home';
}

$action = new Action($_GET['route']);

//Resolve Layout ID
$layout_query = $db->query("SELECT layout_id FROM " . DB_PREFIX . "layout_route WHERE '" . $db->escape($_GET['route']) . "' LIKE CONCAT(route, '%') AND store_id = '" . $config->get('config_store_id') . "' ORDER BY route ASC LIMIT 1");

if ($layout_query->num_rows) {
	$config->set('config_layout_id', $layout_query->row['layout_id']);
} else {
	$config->set('config_layout_id', 0);
}


// Dispatch
$controller->dispatch($action, new Action('error/not_found'));

// Output
$response->output();


if ($config->get('config_performance_log')) {
	$stats = array(
		'peak_memory' => $registry->get('tool')->bytes2str(memory_get_peak_usage(true)),
		'count_included_files' => count(get_included_files()),
		'execution_time' => microtime(true) - $__start,
	);
	
	foreach ($stats as $key => $s) {
		echo "$key = $s<br>";
	}
}

