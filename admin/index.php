<?php
require_once('../ac_config.php');

// Install
if (!defined('DOMAIN') || defined("AMPLOCART_INSTALL_USER")) {
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

//Load
require_once(_ac_mod_file(DIR_SYSTEM . 'load.php'));
