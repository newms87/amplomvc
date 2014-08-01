<?php
// Configuration
if (is_file('ac_config.php')) {
	include_once('ac_config.php');
}

$_SERVER += array(
	'HTTP_HOST'      => DOMAIN,
	'REQUEST_METHOD' => 'GET',
	'REMOTE_ADDR'    => '::1',
);

// Install
if (!defined('DOMAIN') || defined("AMPLO_INSTALL_USER")) {
	define("AMPLO_INSTALL", true);
	require_once('system/install/install.php');
	exit;
}

$__start = microtime(true);

//System / URL Paths
require_once('path_config.php');
require_once(DIR_SYSTEM . 'functions.php');

//File Modifications
require_once(DIR_SYSTEM . 'ac_mod_file.php');

// System Startup
require_once(_ac_mod_file(DIR_SYSTEM . 'startup.php'));

// Load
require_once(_ac_mod_file(DIR_SYSTEM . 'load.php'));
