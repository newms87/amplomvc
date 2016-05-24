<?php
/**
 * @author  Daniel Newman
 * @date    3/20/2013
 * @package Amplo MVC
 * @link    http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYING.txt and LICENSE.txt files in the root directory.
 */

// Check PHP Version
if (version_compare(phpversion(), '5.3.0', '<') == true) {
	exit('PHP5.3+ Required');
}

//Directories
define('DIR_SYSTEM', DIR_SITE . 'system/');
define('DIR_DATABASE', DIR_SITE . 'system/database/');
define('DIR_PLUGIN', DIR_SITE . 'plugin/');
define('DIR_EXCEL_TEMPLATE', DIR_SITE . 'system/php-excel/templates/');
define('DIR_EXCEL_FPO', DIR_SITE . 'upload/fpo/');
define('DIR_CRON', DIR_SITE . 'system/cron/');
define('DIR_MOD_FILES', DIR_SITE . 'system/mods/');

/************************************************************
 * Conditional defines for AmploMVC config.php to override. *
 ************************************************************/

//Setup Base URL
defined('DOMAIN') || define('DOMAIN', !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost');
defined('SITE_BASE') || define('SITE_BASE', '/');
defined('URL_SITE') || define('URL_SITE', '//' . DOMAIN . SITE_BASE);

//Attempt to filter bad domain requests (presumably hack attempts)
if (preg_match('/[\\s~!@#$%\\^&\\*()_+=\'";:\\/\\\\\\[\\]]/', DOMAIN)) {
	header('HTTP/1.1 400 Invalid Domain Format');
	exit;
}

//Cookie Prefix prevents cookie conflicts across top level domain to sub domain (ex: .example.com and .sub.example.com)
// and for different sites on same domain with different in different directories (ex: example.com/site-a and example.com/site-b)
defined('COOKIE_PREFIX') || define('COOKIE_PREFIX', preg_replace("/[^a-z0-9_]/", '', str_replace('/', '_', DOMAIN . SITE_BASE)));

defined('HTTP_SITE') || define('HTTP_SITE', 'http://' . DOMAIN . SITE_BASE);
defined('HTTPS_SITE') || define('HTTPS_SITE', 'https://' . DOMAIN . SITE_BASE);

defined('DIR_LOGS') || define('DIR_LOGS', DIR_SITE . 'system/logs/');
defined('DIR_IMAGE') || define('DIR_IMAGE', DIR_SITE . 'image/');
defined('URL_IMAGE') || define('URL_IMAGE', URL_SITE . 'image/');
defined('DIR_DOWNLOAD') || define('DIR_DOWNLOAD', DIR_SITE . 'download/');
defined('URL_DOWNLOAD') || define('URL_DOWNLOAD', URL_SITE . 'download/');
defined('DIR_RESOURCES') || define('DIR_RESOURCES', DIR_SITE . 'system/resources/');
defined('URL_RESOURCES') || define('URL_RESOURCES', URL_SITE . 'system/resources/');
defined('DIR_VIEW') || define('DIR_VIEW', DIR_SITE . 'app/view/');
defined('URL_VIEW') || define('URL_VIEW', URL_SITE . 'app/view/');
defined('DIR_THEMES') || define('DIR_THEMES', DIR_VIEW . 'theme/');
defined('URL_THEMES') || define('URL_THEMES', URL_VIEW . 'theme/');
defined('DIR_JS') || define('DIR_JS', DIR_VIEW . 'js/');
defined('URL_JS') || define('URL_JS', URL_VIEW . 'js/');
defined('URL_STYLE') || define('URL_STYLE', URL_VIEW . 'style/');
defined('DIR_STYLE') || define('DIR_STYLE', DIR_VIEW . 'style/');
defined('DIR_DATABASE_BACKUP') || define('DIR_DATABASE_BACKUP', DIR_SITE . 'system/database/backups/');

defined('DEFAULT_TIMEZONE') || define('DEFAULT_TIMEZONE', 'America/Denver');
defined('MYSQL_TIMEZONE') || define('MYSQL_TIMEZONE', '-6:00');
defined('AMPLO_SESSION_HANDLER') || define('AMPLO_SESSION_HANDLER', false);
defined('AMPLO_PROFILE') || define('AMPLO_PROFILE', false);
defined('AMPLO_PROFILE_NO_CACHE') || define('AMPLO_PROFILE_NO_CACHE', false);
defined('AMPLO_DEFAULT_THEME') || define('AMPLO_DEFAULT_THEME', 'amplo');
defined('AMPLO_TIME_LOG') || define('AMPLO_TIME_LOG', false);
defined('AMPLO_ACCESS_LOG') || define('AMPLO_ACCESS_LOG', 0);
defined('AMPLO_API_LOG') || define('AMPLO_API_LOG', 0);
defined('AMPLO_API_LOG_MAX_SIZE') || define('AMPLO_API_LOG_MAX_SIZE', 1024);
defined('AMPLO_SESSION') || define('AMPLO_SESSION', COOKIE_PREFIX . 'amplo-session');
defined('AMPLO_SESSION_TIMEOUT') || define('AMPLO_SESSION_TIMEOUT', 3600 * 2);
defined('AMPLO_USER_EXPIRE') || define('AMPLO_USER_EXPIRE', 3600 * 24 * 365);
defined('AMPLO_HEADERS_DEBUG') || define('AMPLO_HEADERS_DEBUG', false);
defined('CACHE_FILE_EXPIRATION') || define('CACHE_FILE_EXPIRATION', 3600);
//Rewrites <?= PHP tags to <?php echo. Only set this to true if your server does not allow <?= short tags.
defined('AMPLO_REWRITE_SHORT_TAGS') || define('AMPLO_REWRITE_SHORT_TAGS', false);
defined('AMPLO_AUTO_UPDATE') || define('AMPLO_AUTO_UPDATE', true);
defined('AMPLO_PRODUCTION') || define('AMPLO_PRODUCTION', true);

//Default server values in case they are not set.
$_SERVER += array(
	'HTTP_HOST'      => DOMAIN,
	'REQUEST_URI'    => '',
	'REQUEST_METHOD' => 'GET',
	'REMOTE_ADDR'    => '::1',
	'QUERY_STRING'   => '',
);

if ($_SERVER['REQUEST_URI'] === SITE_BASE . 'favicon.ico') {
	header('HTTP/1.1 404 No Favicon');
	exit;
}

if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
	$_SERVER['HTTPS'] = 'on';
}

date_default_timezone_set(DEFAULT_TIMEZONE);

//Date Constants
define('DATETIME_ZERO', '0000-00-00 00:00:00');
define('DATE_ZERO', '0000-00-00');

//COOKIES
if (!defined('COOKIE_DOMAIN')) {
	if (!DOMAIN || DOMAIN === 'localhost') {
		define('COOKIE_DOMAIN', '');
	} elseif (preg_match("/[\\d]+\\.[\\d]+\\.[\\d]+\\.[\\d]+/", DOMAIN)) {
		define('COOKIE_DOMAIN', DOMAIN);
	} else {
		define('COOKIE_DOMAIN', '.' . DOMAIN);
	}
}

//Start Session
session_name(AMPLO_SESSION);
ini_set('session.use_cookies', 'On');
ini_set('session.use_trans_sid', 'Off');
ini_set("session.cookie_domain", COOKIE_DOMAIN);
session_set_cookie_params(0, '/', COOKIE_DOMAIN, false, false);

if (AMPLO_SESSION_HANDLER) {
	if (!interface_exists('SessionHandlerInterface')) {
		require_once(DIR_SYSTEM . 'resources/SessionHandlerInterface.php');
	}

	require_once(_mod(DIR_SYSTEM . 'engine/session.php'));

	$session_handler = new AmploSessionHandler();

	if (version_compare(phpversion(), '5.4.0', '>=') == true) {
		session_set_save_handler($session_handler, true);
	} else {
		session_set_save_handler(
			array(
				$session_handler,
				'open'
			),
			array(
				$session_handler,
				'close'
			),
			array(
				$session_handler,
				'read'
			),
			array(
				$session_handler,
				'write'
			),
			array(
				$session_handler,
				'destroy'
			),
			array(
				$session_handler,
				'gc'
			)
		);
	}
}

session_start();

//Clean Globals
function _clean_global(&$value)
{
	$value = html_entity_decode($value, ENT_COMPAT);

	if (get_magic_quotes_gpc()) {
		$value = stripslashes($value);
	}
}

array_walk_recursive($_GET, '_clean_global');
array_walk_recursive($_POST, '_clean_global');
array_walk_recursive($_REQUEST, '_clean_global');
array_walk_recursive($_COOKIE, '_clean_global');
array_walk_recursive($_SERVER, '_clean_global');

// Unregister Globals
if (ini_get('register_globals')) {
	$globals = array(
		$_REQUEST,
		$_SESSION,
		$_SERVER,
		$_FILES,
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
	$_REQUEST                  = $_POST + $_REQUEST;
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

