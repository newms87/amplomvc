<?php
//TODO: Remove this eventually...
if (is_file(dirname(__FILE__) . '/ac_config.php')) {
	rename(dirname(__FILE__) . '/ac_config.php', dirname(__FILE__) . '/config.php');
}

// Configuration
if (is_file(dirname(__FILE__) . '/config.php')) {
	include_once(dirname(__FILE__) . '/config.php');
}

// Install
if (!defined('DOMAIN') || defined("AMPLO_INSTALL_USER")) {
	define("AMPLO_INSTALL", true);
	require_once('system/install/install.php');
	exit;
}

//Default server values in case they are not set.
$_SERVER += array(
	'HTTP_HOST'      => DOMAIN,
	'REQUEST_METHOD' => 'GET',
	'REMOTE_ADDR'    => '::1',
	'QUERY_STRING'   => '',
);

//Timer for full system response
$__start = microtime(true);

//File Modifications
require_once(DIR_SITE . 'system/ac_mod_file.php');

// System Startup
require_once(_ac_mod_file(DIR_SITE . 'system/startup.php'));

// Load
require_once(_ac_mod_file(DIR_SYSTEM . 'load.php'));

if (AMPLO_TIME_LOG) {
	timelog('finish');
}
