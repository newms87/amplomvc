<?php
// Version
define('VERSION', '1.5.2.1');

//data
define('DATETIME_ZERO','0000-00-00 00:00:00');

$__start = microtime(true);

if(isset($_GET['phpinfo'])){
	phpinfo();
	exit;
}

//DN CUSTOM FUNCTIONS
include('functions.php');

// Configuration
require_once('oc_config.php');

//System / URL Paths
require_once('path_config.php');

/*  PRETTY LANGUAGE TESTING 
echo 'testing pretty language';
require_once(DIR_SYSTEM . 'library/pretty_language.php');
new PrettyLanguage();
echo 'pretty_language_done';
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

// Config
$config = new Config();
$registry->set('config', $config);

// Database 
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$registry->set('db', $db);
$db->query("SET time_zone='" . MYSQL_TIMEZONE . "'");

// Store
if (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) {
	$store_query = $db->query("SELECT * FROM " . DB_PREFIX . "store WHERE REPLACE(`ssl`, 'www.', '') = '" . $db->escape('https://' . str_replace('www.', '', $_SERVER['HTTP_HOST']) . rtrim(dirname($_SERVER['PHP_SELF']), '/.\\') . '/') . "'");
} else {
	$store_query = $db->query("SELECT * FROM " . DB_PREFIX . "store WHERE REPLACE(`url`, 'www.', '') = '" . $db->escape('http://' . str_replace('www.', '', $_SERVER['HTTP_HOST']) . rtrim(dirname($_SERVER['PHP_SELF']), '/.\\') . '/') . "'");
}

//Resolve Store ID
if ($store_query->num_rows) {
	$config->set('config_store_id', $store_query->row['store_id']);
} else {
	$config->set('config_store_id', 0);
}

// Settings
$query = $db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '0' OR store_id = '" . (int)$config->get('config_store_id') . "' ORDER BY store_id ASC");

foreach ($query->rows as $setting) {
	if (!$setting['serialized']) {
		$config->set($setting['key'], $setting['value']);
	} else {
		$config->set($setting['key'], unserialize($setting['value']));
	}
}

// Check image directory is writable
make_test_dir(DIR_IMAGE, $config->get('config_image_dir_mode'));

//Check image cache directory is writable
make_test_dir(DIR_IMAGE . 'cache/', $config->get('config_image_dir_mode'));

//Check cache directory is writable
make_test_dir(DIR_CACHE, $config->get('config_default_dir_mode'));

//Check download directory is writable
make_test_dir(DIR_DOWNLOAD, $config->get('config_default_dir_mode'));

//Check logs directory is writable
make_test_dir(DIR_LOGS, $config->get('config_default_dir_mode'));

if (!$store_query->num_rows) {
	$config->set('config_url', HTTP_SERVER);
	$config->set('config_ssl', HTTPS_SERVER);
}


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

if(!isset($_GET['route'])){
   $_GET['route'] = 'common/home';
}

// Log 
$error_log = new Log($config->get('config_error_filename'), $config->get('config_name'));
$registry->set('error_log', $error_log);

$log = new Log($config->get('config_log_filename'), $config->get('config_name'));
$registry->set('log', $log);

function error_handler($errno, $errstr, $errfile, $errline) {
	global $error_log, $config;
	
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
}
	
// Error Handler
set_error_handler('error_handler');

// Cache
$cache = new Cache($registry);
foreach(explode(',',$config->get('config_cache_ignore')) as $ci)
   $cache->ignore($ci);
$registry->set('cache', $cache); 

//Images
$registry->set('image', new Image($registry));

//Plugins
$plugin_handler = new pluginHandler($registry,$config->get('config_store_id'),0, $merge_registry);
$registry->set('plugin_handler', $plugin_handler);

// Response
$response = new Response();
$response->addHeader('Content-Type: text/html; charset=utf-8');
$response->setCompression($config->get('config_compression'));
$registry->set('response', $response); 

// Language Detection
$languages = array();

$query = $db->query("SELECT * FROM " . DB_PREFIX . "language WHERE status = '1'"); 

foreach ($query->rows as $result) {
	$languages[$result['code']] = $result;
}

$detect = '';

if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && ($_SERVER['HTTP_ACCEPT_LANGUAGE'])) { 
	$browser_languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
	
	foreach ($browser_languages as $browser_language) {
		foreach ($languages as $key => $value) {
			if ($value['status']) {
				$locale = explode(',', $value['locale']);

				if (in_array($browser_language, $locale)) {
					$detect = $key;
				}
			}
		}
	}
}

if (isset($session->data['language']) && array_key_exists($session->data['language'], $languages) && $languages[$session->data['language']]['status']) {
	$code = $session->data['language'];
} elseif (isset($_COOKIE['language']) && array_key_exists($_COOKIE['language'], $languages) && $languages[$_COOKIE['language']]['status']) {
	$code = $_COOKIE['language'];
} elseif ($detect) {
	$code = $detect;
} else {
	$code = $config->get('config_language');
}

if (!isset($session->data['language']) || $session->data['language'] != $code) {
	$session->data['language'] = $code;
}

if (!isset($_COOKIE['language']) || $_COOKIE['language'] != $code) {	  
	setcookie('language', $code, time() + 60 * 60 * 24 * 30, '/', $_SERVER['HTTP_HOST']);
}			

$config->set('config_language_id', $languages[$code]['language_id']);
$config->set('config_language', $languages[$code]['code']);

// Language	
$language = new Language($languages[$code], $plugin_handler);
$registry->set('language', $language); 

// Document
$document = new Document($registry);
$document->setCanonicalLink($url->get_pretty_url());
$registry->set('document', $document);

if (isset($_GET['tracking']) && !isset($_COOKIE['tracking'])) {
	setcookie('tracking', $_GET['tracking'], time() + 3600 * 24 * 1000, '/');
}

// Front Controller 
$controller = new Front($registry);

// Router
$route = '';
$action = '';

if(isset($_GET['route'])){
   $part = explode('/', $_GET['route']);
   
   if (isset($part[0])) {
      $route .= $part[0];
   }
   
   if (isset($part[1])) {
      $route .= '/' . $part[1];
   }
}

if($config->get('config_maintenance')){
   if((!$registry->get('user')->isLogged() || !$registry->get('user')->isAdmin()) && strpos($route, 'payment') !== 0){
      //$action = new Action('common/maintenance');
		$_GET['route'] = 'common/maintenance';
   }
}
elseif(!$route){
	$_GET['route'] = 'common/home';
}

$action = new Action($_GET['route']);

//Resolve Layout ID
$layout_query = $db->query("SELECT layout_id FROM " . DB_PREFIX . "layout_route WHERE '" . $db->escape($_GET['route']) . "' LIKE CONCAT(route, '%') AND store_id = '" . $config->get('config_store_id') . "' ORDER BY route ASC LIMIT 1");

if($layout_query->num_rows){
	$config->set('config_layout_id', $layout_query->row['layout_id']);
}else{
	$config->set('config_layout_id', 0);
}


// Dispatch
$controller->dispatch($action, new Action('error/not_found'));

// Output
$response->output();

//if($config->trace_execution_time){
   
//}
//echo 'execution time: ' . (microtime(true) - $__start);
