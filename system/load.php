<?php
// Registry
$registry = new Registry();

//TODO: Maybe make this our main handler for loading (move out of registry)??
spl_autoload_register(function($class) use($registry){
	$registry->get($class);
});

//Language
function _l($message)
{
	$values = func_get_args();

	array_shift($values);

	if (empty($values)) {
		return _($message);
	}

	return vsprintf(_($message), $values);
}

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

//TODO: WE NEED TO SEPARATE OUT ADMIN CONFIG FROM FRONT END CONFIGS (and common in both front / back and front only)!!

//config is self assigning to registry.
$config = new Config($registry);

//Setup Cache ignore list
$cache->ignore($config->get('config_cache_ignore'));

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

$error_handler = function($errno, $errstr, $errfile, $errline, $errcontext) use($error_log, $config){
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
			flush(); //Flush the error to block any redirects that may execute, this ensure errors are seen!
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

//Theme
$registry->set('theme', new Theme($registry));

// Url
$registry->set('url', new Url($registry));

// Response
$response = new Response();
$response->addHeader('Content-Type: text/html; charset=utf-8');
$response->setCompression($config->get('config_compression'));
$registry->set('response', $response);

//Plugins (self assigning to registry)
$plugin = new Plugin($registry);

//Cron
if (isset($_GET['run_cron'])) {
	echo nl2br($registry->get('cron')->run());
	exit;
}
elseif ($config->get('cron_status')) {
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

//TODO: try to move this so it is valid HTML
//Performance Logging
if ($config->get('config_performance_log')) {
	global $__start;
	$stats = array(
		'peak_memory' => $registry->get('tool')->bytes2str(memory_get_peak_usage(true)),
		'count_included_files' => count(get_included_files()),
		'execution_time' => microtime(true) - $__start,
	);

	$html = "<div class=\"title\">" . _l("System Performance:") . "</div>";

	foreach ($stats as $key => $s) {
		$html .= "<div>$key = $s</div>";
	}

	$html = "<div class=\"performance\">$html</div>";

	echo "<script>show_msg('success', '$html')</script>";
}
