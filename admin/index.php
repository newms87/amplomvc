<?php
// Version
define('VERSION', '1.5.2.1');

//data
define('DATETIME_ZERO','0000-00-00 00:00:00');

// Configuration
require_once('config.php');

include('../functions.php');

// Install
if (!defined('DIR_APPLICATION')) {
	header('Location: ../install/index.php');
	exit;
}

//File Merge for plugins
require_once(DIR_SYSTEM . 'file_merge.php');

//System Bootstrap
_require_once(DIR_SYSTEM . 'startup.php');

// Registry
$registry = new Registry();

// Loader
$loader = new Loader($registry);
$registry->set('load', $loader);

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
foreach(explode(',',$config->get('config_cache_ignore')) as $ci)
	$cache->ignore($ci);


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
$session = new Session($registry);
$registry->set('session', $session);

//Messages
$registry->set('message', new Message($session));

// Request
$request = new Request();
$registry->set('request', $request);

// Url
$url = new Url($registry, SITE_URL, $config->get('config_use_ssl') ? SITE_SSL : '');
if($config->get('config_seo_url'))
	$url->getSeoUrl();
$registry->set('url', $url);

if(!isset($_GET['route'])){
	$_GET['route'] = 'common/home';
}

//Database Structure Validation
$db_last_update = $cache->get('db_last_update');
if(!$db_last_update){
	$db_last_update = 0;
}
$query = $db->query("SHOW GLOBAL STATUS WHERE Variable_name = 'com_alter_table' AND Value > '$db_last_update'");
if($query->num_rows){
	$cache->delete('model');
	$cache->set('db_last_update', $query->row['Value']);
}

//Images
$registry->set('image', new Image($registry));

// Response
$response = new Response();
$response->addHeader('Content-Type: text/html; charset=utf-8');
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

//Theme
$registry->set('theme', new Theme($registry));

//Initialize site configurations
$config->run_site_config();

// Front Controller
$controller = new Front($registry);

//Router
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

if(!$registry->get('user')->isLogged()){
	$allow_access = false;
	
	if($route){
		$allowed = array(
			'common/forgotten',
			'common/reset',
			'common/login',
		);
		
		if(!in_array($route, $allowed)){
			$action = new Action('common/login');
		}
	}
}
elseif($route){
	$ignore = array(
		'common/home',
		'common/login',
		'common/logout',
		'common/forgotten',
		'common/reset',
		'error/not_found',
		'error/permission'
	);
	
	if(!in_array($route, $ignore) && !$registry->get('user')->hasPermission('access', $route)){
		$action = new Action('error/permission');
	}
}
else{
	$action = new Action('common/home');
}

if (!$action) {
	$action = new Action($_GET['route']);
}

// Dispatch
$controller->dispatch($action, new Action('error/not_found'));

// Output
$response->output();
