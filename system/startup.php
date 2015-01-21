<?php

// Version
define('AMPLO_VERSION', '0.2.1');
define('AMPLO_DEFAULT_THEME', 'amplo');

//Setup Base URL
if (!defined('DOMAIN')) {
	define('DOMAIN', !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost');
}

if (!defined('URL_SITE')) {
	define('URL_SITE', '//' . DOMAIN . SITE_BASE);
}

//Default server values in case they are not set.
$_SERVER += array(
	'HTTP_HOST'      => DOMAIN,
	'REQUEST_METHOD' => 'GET',
	'REMOTE_ADDR'    => '::1',
	'QUERY_STRING'   => '',
);

//TODO: Remove URL_AJAX after removing ckeditor
define('URL_AJAX', URL_SITE . 'ajax/');


//Directories
define('DIR_SYSTEM', DIR_SITE . 'system/');
define('DIR_DATABASE', DIR_SITE . 'system/database/');
define('DIR_PLUGIN', DIR_SITE . 'plugin/');
define('DIR_FORM', DIR_SITE . 'app/view/form/');
define('DIR_THEMES', DIR_SITE . 'app/view/theme/');
define('DIR_EXCEL_TEMPLATE', DIR_SITE . 'system/php-excel/templates/');
define('DIR_EXCEL_FPO', DIR_SITE . 'upload/fpo/');
define('DIR_CRON', DIR_SITE . 'system/cron/');
define('DIR_MOD_FILES', DIR_SITE . 'system/mods/');

//Conditional defines for AmlpoMVC config.php to override.
$config_defines = array(
	'SITE_BASE'             => '//' . DOMAIN . SITE_BASE,
	'HTTP_SITE'             => 'http://' . DOMAIN . SITE_BASE,
	'HTTPS_SITE'            => 'https://' . DOMAIN . SITE_BASE,
	'URL_IMAGE'             => URL_SITE . 'image/',
	'URL_DOWNLOAD'          => URL_SITE . 'download/',
	'URL_RESOURCES'         => URL_SITE . 'system/resources/',
	'URL_THEMES'            => URL_SITE . 'app/view/theme/',
	'DIR_IMAGE'             => DIR_SITE . 'image/',
	'DIR_DOWNLOAD'          => DIR_SITE . 'download/',
	'DIR_RESOURCES'         => DIR_SITE . 'system/resources/',
	'DIR_LOGS'              => DIR_SITE . 'system/logs/',
	'DIR_DATABASE_BACKUP'   => DIR_SITE . 'system/database/backups/',
	'DEFAULT_TIMEZONE'      => 'America/Denver',
	'MYSQL_TIMEZONE'        => '-6:00',
	'DB_PROFILE'            => false,
	'DB_PROFILE_NO_CACHE'   => false,
	'AMPLO_TIME_LOG'        => false,
	'AMPLO_SESSION'         => 'cross-store-session',
	'AMPLO_SESSION_TIMEOUT' => 3600 * 2,
	'CACHE_FILE_EXPIRATION' => 3600,
);

foreach ($config_defines as $def_key => $def_value) {
	if (!defined($def_key)) {
		define($def_key, $def_value);
	}
}

// Check Version
if (version_compare(phpversion(), '5.3.0', '<') == true) {
	exit('PHP5.3+ Required');
}

date_default_timezone_set(DEFAULT_TIMEZONE);

//Date Constants
define('DATETIME_ZERO', '0000-00-00 00:00:00');
define('DATE_ZERO', '0000-00-00');
define("AC_DATE_STRING", 1);
define("AC_DATE_OBJECT", 2);
define("AC_DATE_TIMESTAMP", 3);

//COOKIES
if (!defined('COOKIE_DOMAIN')) {
	$domain = parse_url(URL_SITE, PHP_URL_HOST);

	if (!$domain || $domain === 'localhost') {
		define('COOKIE_DOMAIN', '');
	} else {
		define('COOKIE_DOMAIN', '.' . $domain);
	}
}

//Start Session
ini_set('session.use_cookies', 'On');
ini_set('session.use_trans_sid', 'Off');

session_name(AMPLO_SESSION);

ini_set("session.cookie_domain", COOKIE_DOMAIN);
session_set_cookie_params(0, '/', COOKIE_DOMAIN, false, false);
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

//Tracking
if (isset($_GET['tracking']) && !isset($_COOKIE['tracking'])) {
	setcookie('tracking', $_GET['tracking'], _time() + 3600 * 24 * 1000, '/');
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

