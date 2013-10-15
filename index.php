<?php
//Check PHP Info if requested
//TODO: This is a security issue. Must hide PHPINFO unless logged into admin panel!!
if (isset($_GET['phpinfo'])) {
	phpinfo();
	exit;
}

// Configuration
if (is_file('ac_config.php')) {
	require_once('ac_config.php');
}

// Install
if (!defined('SITE_URL') || defined("AMPLOCART_INSTALL_USER")) {
	define("AMPLOCART_INSTALL", true);
	require_once('system/install/install.php');
}

$__start = microtime(true);

//System / URL Paths
require_once('path_config.php');

require_once(DIR_SYSTEM . 'functions.php');

/*  PRETTY LANGUAGE TESTING
echo 'testing pretty language<br /><br />';
require_once(DIR_SYSTEM . 'library/pretty_language.php');
new PrettyLanguage();
echo '<br /><br />pretty_language_done';
exit;
//*/


//File Modifications
require_once(DIR_SYSTEM . 'ac_mod_file.php');

// System Startup
require_once(_ac_mod_file(DIR_SYSTEM . 'startup.php'));

if (isset($_GET['_ajax_'])) {
	//Load Ajax Front End
	require_once(_ac_mod_file(SITE_DIR . 'ajax.php'));
} else {
	//Load Front End
	require_once(_ac_mod_file(SITE_DIR . 'load.php'));
}
