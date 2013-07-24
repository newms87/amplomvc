<?php
// Configuration
if (is_file('oc_config.php')) {
	require_once('oc_config.php');
}

// Install
if (!defined('SITE_URL') || defined("AMPLOCART_INSTALL_USER")) {
	define("AMPLOCART_INSTALL", true);
	require_once('system/install/install.php');
}

if (isset($_GET['phpinfo'])) {
	phpinfo();
	exit;
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


//File Merge for plugins
require_once(DIR_SYSTEM . 'file_merge.php');

// System Bootstrap
_require(DIR_SYSTEM . 'startup.php');

if (isset($_GET['_ajax_'])) {
	//Load Ajax Front End
	_require(SITE_DIR . 'ajax.php');
} else {
	//Load Front End
	_require(SITE_DIR . 'load.php');
}
