<?php
// Version
define('AMPLO_VERSION', '0.1.0');

// Error Reporting
error_reporting(E_ALL);

// Check Version
if (version_compare(phpversion(), '5.3.0', '<') == true) {
	exit('PHP5.3+ Required');
}

date_default_timezone_set(DEFAULT_TIMEZONE);

//Date Constants
define('DATETIME_ZERO', '0000-00-00 00:00:00');
define("AC_DATE_STRING", 1);
define("AC_DATE_OBJECT", 2);
define("AC_DATE_TIMESTAMP", 3);

//COOKIES
$domain = parse_url(URL_SITE, PHP_URL_HOST);

if (!$domain || $domain === 'localhost') {
	define('COOKIE_DOMAIN', '');
} else {
	define('COOKIE_DOMAIN', '.' . $domain);
}

//Start Session
ini_set('session.use_cookies', 'On');
ini_set('session.use_trans_sid', 'Off');

session_name(AMPLO_SESSION);

session_set_cookie_params(0, '/', COOKIE_DOMAIN);
session_start();

// Unregister Globals
if (ini_get('register_globals')) {
	$globals = array(
		$_REQUEST,
		$_SESSION,
		$_SERVER,
		$_FILES
	);

	foreach ($globals as $global) {
		foreach (array_keys($global) as $key) {
			unset(${$key});
		}
	}
}

//Database Escaping Entries
define('DB_ESCAPE', 0);
define('DB_NO_ESCAPE', 1);
define('DB_IMAGE', 2);
define('DB_INTEGER', 3);
define('DB_FLOAT', 4);
define('DB_DATETIME', 5);
define('DB_PRIMARY_KEY_INTEGER', 8);
define('DB_AUTO_INCREMENT', 9);
define('DB_AUTO_INCREMENT_PK', 10);

// Windows IIS Compatibility
if (!isset($_SERVER['DOCUMENT_ROOT'])) {
	if (isset($_SERVER['SCRIPT_FILENAME'])) {
		$_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', substr($_SERVER['SCRIPT_FILENAME'], 0, 0 - strlen($_SERVER['PHP_SELF'])));
	} elseif (isset($_SERVER['PATH_TRANSLATED'])) {
		$_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', substr(str_replace('\\\\', '\\', $_SERVER['PATH_TRANSLATED']), 0, 0 - strlen($_SERVER['PHP_SELF'])));
	}
}

if (!isset($_SERVER['REQUEST_URI'])) {
	$_SERVER['REQUEST_URI'] = substr($_SERVER['PHP_SELF'], 1);

	if (isset($_SERVER['QUERY_STRING'])) {
		$_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
	}
}

//Core
require_once(_ac_mod_file(DIR_DATABASE . 'db.php'));

// Engine
require_once(_ac_mod_file(DIR_SYSTEM . 'engine/action.php'));
require_once(_ac_mod_file(DIR_SYSTEM . 'engine/controller.php'));
require_once(_ac_mod_file(DIR_SYSTEM . 'engine/model.php'));
require_once(_ac_mod_file(DIR_SYSTEM . 'engine/router.php'));
require_once(_ac_mod_file(DIR_SYSTEM . 'engine/library.php'));
require_once(_ac_mod_file(DIR_SYSTEM . 'engine/registry.php'));
require_once(_ac_mod_file(DIR_SYSTEM . 'engine/cache.php'));

// Common
require_once(_ac_mod_file(DIR_SYSTEM . 'library/config.php'));
require_once(_ac_mod_file(DIR_SYSTEM . 'library/mod.php'));
require_once(_ac_mod_file(DIR_SYSTEM . 'library/log.php'));
require_once(_ac_mod_file(DIR_SYSTEM . 'library/plugin.php'));
require_once(_ac_mod_file(DIR_SYSTEM . 'library/request.php'));
require_once(_ac_mod_file(DIR_SYSTEM . 'library/response.php'));
require_once(_ac_mod_file(DIR_SYSTEM . 'library/session.php'));
require_once(_ac_mod_file(DIR_SYSTEM . 'library/theme.php'));
require_once(_ac_mod_file(DIR_SYSTEM . 'library/url.php'));

//Helpers
require_once(_ac_mod_file(DIR_SYSTEM . 'helper/caller.php'));
