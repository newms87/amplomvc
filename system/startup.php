<?php

// Version
define('AMPLO_VERSION', '0.1.0');
define('AMPLO_DEFAULT_THEME', 'amplo');

//Urls
if (!defined('DOMAIN')) {
	define('DOMAIN', !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost');
}

//Default server values in case they are not set.
$_SERVER += array(
	'HTTP_HOST'      => DOMAIN,
	'REQUEST_METHOD' => 'GET',
	'REMOTE_ADDR'    => '::1',
	'QUERY_STRING'   => '',
);

//This is the path to Amplo MVC from the site's root directory. If it is in the root make this '/'
if (!defined('SITE_BASE')) {
	define('SITE_BASE', '/');
}

if (!defined('URL_SITE')) {
	define('URL_SITE', '//' . DOMAIN . SITE_BASE);
}

if (!defined('HTTP_SITE')) {
	define('HTTP_SITE', 'http://' . DOMAIN . SITE_BASE);
}

if (!defined('HTTPS_SITE')) {
	define('HTTPS_SITE', 'https://' . DOMAIN . SITE_BASE);
}

define('URL_THEMES', URL_SITE . 'app/view/theme/');
define('URL_RESOURCES', URL_SITE . 'system/resources/');

//TODO: Remove URL_AJAX after removing ckeditor
define('URL_AJAX', URL_SITE . 'ajax/');


//Directories
define('DIR_SYSTEM', DIR_SITE . 'system/');
define('DIR_DATABASE', DIR_SITE . 'system/database/');
define('DIR_PLUGIN', DIR_SITE . 'plugin/');
define('DIR_FORM', DIR_SITE . 'app/view/form/');
define('DIR_THEMES', DIR_SITE . 'app/view/theme/');
define('DIR_RESOURCES', DIR_SITE . 'system/resources/');
define('DIR_EXCEL_TEMPLATE', DIR_SITE . 'system/php-excel/templates/');
define('DIR_EXCEL_FPO', DIR_SITE . 'upload/fpo/');
define('DIR_CRON', DIR_SITE . 'system/cron/');
define('DIR_MOD_FILES', DIR_SITE . 'system/mods/');

if (!defined('DIR_RESOURCES')) {
	define('DIR_RESOURCES', DIR_SITE . 'system/resources/');
}

if (!defined('DIR_IMAGE')) {
	define('DIR_IMAGE', DIR_SITE . 'image/');
}

if (!defined('DIR_LOGS')) {
	define('DIR_LOGS', DIR_SITE . 'system/logs/');
}

if (!defined('DIR_DATABASE_BACKUP')) {
	define('DIR_DATABASE_BACKUP', DIR_SITE . 'system/database/backups/');
}

// Check Version
if (version_compare(phpversion(), '5.3.0', '<') == true) {
	exit('PHP5.3+ Required');
}

//Date & Time
if (!defined('DEFAULT_TIMEZONE')) {
	define('DEFAULT_TIMEZONE', 'America/Denver');
}

if (!defined('MYSQL_TIMEZONE')) {
	define('MYSQL_TIMEZONE', '-6:00');
}

date_default_timezone_set(DEFAULT_TIMEZONE);

//Date Constants
define('DATETIME_ZERO', '0000-00-00 00:00:00');
define("AC_DATE_STRING", 1);
define("AC_DATE_OBJECT", 2);
define("AC_DATE_TIMESTAMP", 3);

//DB
if (!defined('DB_PROFILE')) {
	define("DB_PROFILE", false);
}

if (!defined('DB_PROFILE_NO_CACHE')) {
	define("DB_PROFILE_NO_CACHE", false);
}

//COOKIES
$domain = parse_url(URL_SITE, PHP_URL_HOST);

if (!$domain || $domain === 'localhost') {
	define('COOKIE_DOMAIN', '');
} else {
	define('COOKIE_DOMAIN', '.' . $domain);
}

//This allows for cross store sessions
if (!defined('AMPLO_SESSION')) {
	define("AMPLO_SESSION", "cross-store-session");
}

//set session timeout to 2 hours
if (!defined('AMPLO_SESSION_TIMEOUT')) {
	define('AMPLO_SESSION_TIMEOUT', 3600 * 2);
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

//Simulate POST request for post_redirect()
if (isset($_SESSION['__post_data__'])) {
	$_POST                     = $_SESSION['__post_data__'];
	$_SERVER['REQUEST_METHOD'] = 'POST';
	unset($_SESSION['__post_data__']);
}

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
require_once(_mod(DIR_DATABASE . 'db.php'));

// Engine
require_once(_mod(DIR_SYSTEM . 'engine/action.php'));
require_once(_mod(DIR_SYSTEM . 'engine/controller.php'));
require_once(_mod(DIR_SYSTEM . 'engine/model.php'));
require_once(_mod(DIR_SYSTEM . 'engine/router.php'));
require_once(_mod(DIR_SYSTEM . 'engine/library.php'));
require_once(_mod(DIR_SYSTEM . 'engine/registry.php'));
require_once(_mod(DIR_SYSTEM . 'engine/cache.php'));

// Common
require_once(_mod(DIR_SYSTEM . 'library/config.php'));
require_once(_mod(DIR_SYSTEM . 'library/mod.php'));
require_once(_mod(DIR_SYSTEM . 'library/log.php'));
require_once(_mod(DIR_SYSTEM . 'library/plugin.php'));
require_once(_mod(DIR_SYSTEM . 'library/request.php'));
require_once(_mod(DIR_SYSTEM . 'library/response.php'));
require_once(_mod(DIR_SYSTEM . 'library/session.php'));
require_once(_mod(DIR_SYSTEM . 'library/theme.php'));
require_once(_mod(DIR_SYSTEM . 'library/url.php'));

//Helpers
$handle = opendir(DIR_SYSTEM . 'helper/');
while (($helper = readdir($handle))) {
	if (strpos($helper, '.') === 0) {
		continue;
	}

	if (is_file(DIR_SYSTEM . 'helper/' . $helper)) {
		require_once(_mod(DIR_SYSTEM . 'helper/' . $helper));
	}
}

