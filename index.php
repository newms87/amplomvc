<?php
// Configuration
if (is_file(dirname(__FILE__) . '/config.php')) {
	include_once(dirname(__FILE__) . '/config.php');
}

// Install
if (!defined('SITE_BASE') || defined("AMPLO_INSTALL_USER")) {
	define("AMPLO_INSTALL", true);
	require_once('system/install/install.php');
	exit;
}

//Timer for full system performance profiling
$__start = microtime(true);

//DIR_CACHE only required define for _mod.php
if (!defined('DIR_CACHE')) {
	define('DIR_CACHE', DIR_SITE . 'system/cache/');
}

//File Modifications
require_once(DIR_SITE . 'system/_mod.php');

// System Startup
require_once(_mod(DIR_SITE . 'system/startup.php'));

if (AMPLO_TIME_LOG) {
	timelog('startup');
}

// Load
require_once(_mod(DIR_SYSTEM . 'load.php'));

if (AMPLO_TIME_LOG) {
	timelog('finish');
}
