<?php
// Version
define('VERSION', '0.0.5');

// Error Reporting
error_reporting(E_ALL);

// Check Version
if (version_compare(phpversion(), '5.3.0', '<') == true) {
	exit('PHP5.3+ Required');
}

//Date Constants
define('DATETIME_ZERO','0000-00-00 00:00:00');
define("AC_DATE_STRING", 1);
define("AC_DATE_OBJECT", 2);
define("AC_DATE_TIMESTAMP", 3);

//COOKIES
$domain = parse_url(SITE_URL, PHP_URL_HOST);

if ($domain === 'localhost') {
	define('COOKIE_DOMAIN', '');
} else {
	define('COOKIE_DOMAIN', '.' . parse_url(SITE_URL, PHP_URL_HOST));
}

// Register Globals
if (ini_get('register_globals')) {
	ini_set('session.use_cookies', 'On');
	ini_set('session.use_trans_sid', 'Off');
		
	session_set_cookie_params(0, '/');
	session_start();
	
	$globals = array($_REQUEST, $_SESSION, $_SERVER, $_FILES);

	foreach ($globals as $global) {
		foreach (array_keys($global) as $key) {
			unset(${$key});
		}
	}
}

// Magic Quotes Fix
if (ini_get('magic_quotes_gpc')) {
	function clean($data)
	{
			if (is_array($data)) {
  			foreach ($data as $key => $value) {
				$data[clean($key)] = clean($value);
  			}
		} else {
  			$data = stripslashes($data);
		}
	
		return $data;
	}
	
	$_GET = clean($_GET);
	$_POST = clean($_POST);
	$_REQUEST = clean($_REQUEST);
	$_COOKIE = clean($_COOKIE);
}

if (!ini_get('date.timezone')) {
	date_default_timezone_set(DEFAULT_TIMEZONE);
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
	}
}

if (!isset($_SERVER['DOCUMENT_ROOT'])) {
	if (isset($_SERVER['PATH_TRANSLATED'])) {
		$_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', substr(str_replace('\\\\', '\\', $_SERVER['PATH_TRANSLATED']), 0, 0 - strlen($_SERVER['PHP_SELF'])));
	}
}

if (!isset($_SERVER['REQUEST_URI'])) {
	$_SERVER['REQUEST_URI'] = substr($_SERVER['PHP_SELF'], 1);
	
	if (isset($_SERVER['QUERY_STRING'])) {
		$_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
	}
}

// Check install directory exists
if (is_dir(dirname(DIR_APPLICATION) . '/install')) {
	$this->error['error_install'] = $this->_('error_install');
}

// Helper
_require(DIR_SYSTEM . 'helper/json.php');

// Engine
_require(DIR_SYSTEM . 'engine/action.php');
_require(DIR_SYSTEM . 'engine/controller.php');
_require(DIR_SYSTEM . 'engine/front.php');
_require(DIR_SYSTEM . 'engine/library.php');
_require(DIR_SYSTEM . 'engine/loader.php');
_require(DIR_SYSTEM . 'engine/model.php');
_require(DIR_SYSTEM . 'engine/registry.php');

// Common
_require(DIR_SYSTEM . 'library/cache.php');
_require(DIR_SYSTEM . 'library/config.php');
_require(DIR_SYSTEM . 'library/db.php');
_require(DIR_SYSTEM . 'library/document.php');
_require(DIR_SYSTEM . 'library/file_merge.php');
_require(DIR_SYSTEM . 'library/image.php');
_require(DIR_SYSTEM . 'library/language.php');
_require(DIR_SYSTEM . 'library/log.php');
_require(DIR_SYSTEM . 'library/plugin.php');
_require(DIR_SYSTEM . 'library/request.php');
_require(DIR_SYSTEM . 'library/response.php');
_require(DIR_SYSTEM . 'library/session.php');
_require(DIR_SYSTEM . 'library/theme.php');
_require(DIR_SYSTEM . 'library/template.php');
_require(DIR_SYSTEM . 'library/url.php');

