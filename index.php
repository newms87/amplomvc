<?php
//Amplo MVC Version
define('AMPLO_VERSION', '0.2.2');

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

//File Modifications
require_once(DIR_SITE . 'system/_mod.php');

// System Startup
require_once(_mod(DIR_SITE . 'system/startup.php'));

// Load
require_once(_mod(DIR_SYSTEM . 'load.php'));

