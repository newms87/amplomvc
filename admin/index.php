<?php
//TODO: This is a hack to allow config file to be found from elfinder imagemanager system (and possibly other systems)
if (is_file('../ac_config.php')) {
	require_once('../ac_config.php');
} elseif (is_file('../../ac_config.php')) {
	require_once('../../ac_config.php');
} elseif (is_file('../../../ac_config.php')) {
	require_once('../../../ac_config.php');
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
require_once(DIR_SYSTEM . 'ac_mod_file.php');

//Bootstrap
require_once(_ac_mod_file(DIR_SYSTEM . 'startup.php'));

if (isset($_GET['_ajax_'])) {
	//Load Ajax Admin
	require_once(_ac_mod_file(DIR_APPLICATION . 'ajax.php'));
} else {
	//Load Admin
	require_once(_ac_mod_file(DIR_APPLICATION . 'load.php'));
}