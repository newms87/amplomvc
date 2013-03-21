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

// Config
$config = new Config();
$registry->set('config', $config);

// Database
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$registry->set('db', $db);

// Settings
$query = $db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '0'");

if(!$query->num_rows){
	$msg = "There were no entries in the system configuration database! Please resolve this before continuing!";
	echo $msg;
	exit;
}

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

// Session
$session = new Session($registry);
$registry->set('session', $session);

//Messages
$registry->set('message', new Message($session));

// Request
$request = new Request();
$registry->set('request', $request);

// Url
$url = new Url($registry, HTTP_SERVER, $config->get('config_use_ssl') ? HTTPS_SERVER : '');
if($config->get('config_seo_url'))
   $url->getSeoUrl();
$registry->set('url', $url);

if(!isset($_GET['route'])){
   $_GET['route'] = 'common/home';
}

// Log 
$error_log = new Log($config->get('config_error_filename'), 'Admin');
$registry->set('error_log', $error_log);

$log = new Log($config->get('config_log_filename'), 'Admin');
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
$plugin_handler = new pluginHandler($registry, 0, 1, $merge_registry);
$registry->set('plugin_handler', $plugin_handler);

// Response
$response = new Response();
$response->addHeader('Content-Type: text/html; charset=utf-8');
$registry->set('response', $response);

// Language
$languages = array();

$query = $db->query("SELECT * FROM " . DB_PREFIX . "language"); 

if(!$query->rows){
	$msg = "There were no languages in the database! Please resolve this before continuing!";
	echo "There were no languages in the database! Please resolve this before continuing!";
	trigger_error("There were no languages in the database! Please resolve this before continuing!");
	exit();
}

foreach ($query->rows as $result) {
	$languages[$result['code']] = $result;
}

$config->set('config_language_id', $languages[$config->get('config_admin_language')]['language_id']);

// Language	
$language = new Language($languages[$config->get('config_admin_language')], $plugin_handler);
$registry->set('language', $language); 		

// Document
$document = new Document($registry);
$document->setCanonicalLink($url->get_pretty_url());
$registry->set('document', $document);

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
