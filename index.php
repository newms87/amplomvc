<?php
// Configuration
if (is_file(dirname(__FILE__) . '/config.php')) {
	include_once(dirname(__FILE__) . '/config.php');
} //TODO: Deprecated - Change config file name to config.php
elseif (is_file(dirname(__FILE__) . '/ac_config.php')) {
	include_once(dirname(__FILE__) . '/ac_config.php');
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

$__start = microtime(true);

//System / URL Paths
require_once('path_config.php');

//File Modifications
require_once(DIR_SYSTEM . 'ac_mod_file.php');

// System Startup
require_once(_ac_mod_file(DIR_SYSTEM . 'startup.php'));

// Load
require_once(_ac_mod_file(DIR_SYSTEM . 'load.php'));
