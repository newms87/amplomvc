<?php
//TODO: This is a hack to allow config file to be found from elfinder imagemanager system (and possibly other systems)
if (is_file('../oc_config.php')) {
	require_once('../oc_config.php');
} elseif (is_file('../../oc_config.php')) {
	require_once('../../oc_config.php');
} elseif (is_file('../../../oc_config.php')) {
	require_once('../../../oc_config.php');
}

// Install
if (!defined('SITE_URL')) {
	header("Location: ../index.php");
	exit;
}

// Configuration
require_once('path_config.php');

require_once(DIR_SYSTEM . 'functions.php');

//File Merge for plugins
require_once(DIR_SYSTEM . 'file_merge.php');

//Bootstrap
_require(DIR_SYSTEM . 'startup.php');

if (isset($_GET['_ajax_'])) {
	//Load Ajax Admin
	_require(DIR_APPLICATION . 'ajax.php');
} else {
	//Load Admin
	_require(DIR_APPLICATION . 'load.php');
}