<?php
// Configuration
if (is_file('ac_config.php')) {
	include_once('ac_config.php');
}

// Install
if (!defined('DOMAIN') || defined("AMPLOCART_INSTALL_USER")) {
	define("AMPLOCART_INSTALL", true);
	require_once('system/install/install.php');
	exit;
}

//System / URL Paths
require_once('path_config.php');
require_once(DIR_SYSTEM . 'functions.php');

$__start = microtime(true);

//File Modifications
require_once(DIR_SYSTEM . 'ac_mod_file.php');

// System Startup
require_once(_ac_mod_file(DIR_SYSTEM . 'startup.php'));

// Load
require_once(_ac_mod_file(DIR_SYSTEM . 'load.php'));
