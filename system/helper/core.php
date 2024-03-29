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

//Amplo Performance Logging
//TODO: Implement full system profile
function _profile($key, array $data = array())
{
	global $_profile, $__start;
	static $sort_order = 0;

	$mb        = 1024 * 1024;
	$memory    = round(memory_get_peak_usage() / $mb, 2) . " MB";
	$allocated = round(memory_get_peak_usage(true) / $mb, 2) . " MB";
	$time      = round(microtime(true) - $__start, 6);

	$data += array(
		'key'       => $key,
		'time'      => $time,
		'memory'    => $memory,
		'allocated' => $allocated,
	);

	$_profile[$sort_order++] = $data;
}

if (AMPLO_PROFILE) {
	_profile('Profiling Started');
}

if (!function_exists('apache_request_headers')) {
	function apache_request_headers()
	{
		$headers = array();
		foreach ($_SERVER as $k => $v) {
			if (substr($k, 0, 5) == "HTTP_") {
				$k           = str_replace('_', ' ', substr($k, 5));
				$k           = str_replace(' ', '-', ucwords(strtolower($k)));
				$headers[$k] = $v;
			}
		}

		return $headers;
	}
}

//Request Headers
$_headers = apache_request_headers();
function _header($key = null, $default = null)
{
	global $_headers;
	if ($key) {
		return isset($_headers[$key]) ? $_headers[$key] : $default;
	}

	return $_headers;
}

define("REQUEST_ACCEPT", _header('Accept'));

function request_accepts($type)
{
	return strpos(REQUEST_ACCEPT, $type) !== false;
}

define("IS_ADMIN", strpos(rtrim($_SERVER['REQUEST_URI'], '/'), SITE_BASE . 'admin') === 0);
define('IS_WINDOWS', strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
define("IS_SSL", !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

define("IS_AJAX", isset($_GET['ajax']) ? true : isset($_headers['X-Requested-With']));
define("IS_POST", $_SERVER['REQUEST_METHOD'] === 'POST');
define("IS_GET", $_SERVER['REQUEST_METHOD'] === 'GET');

function _get($key, $default = null)
{
	return isset($_GET[$key]) ? $_GET[$key] : $default;
}

function _get_exclude($exclude)
{
	if (!is_array($exclude)) {
		$exclude = func_get_args();
	}

	$get = $_GET;

	foreach ($exclude as $ex) {
		unset($get[$ex]);
	}

	return $get;
}

function _post($key, $default = null)
{
	return isset($_POST[$key]) ? $_POST[$key] : $default;
}

function _request($key, $default = null)
{
	return isset($_REQUEST[$key]) ? $_REQUEST[$key] : $default;
}

function _files($index = null)
{
	$files = !empty($_FILES['file']) ? array($_FILES['file']) : $_FILES;

	if ($index !== null) {
		return isset($files[$index]) ? $files[$index] : null;
	}

	return $files;
}

function _session($key, $default = null)
{
	return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
}

function _cookie($name, $default = null)
{
	if (isset($_COOKIE[COOKIE_PREFIX . $name])) {
		return $_COOKIE[COOKIE_PREFIX . $name];
	} elseif (isset($_COOKIE[$name])) {
		return $_COOKIE[$name];
	}

	return $default;
}

function set_cookie($name, $value, $expire = 31536000, $prefix = true)
{
	if (!headers_sent()) {
		$expire = $expire ? time() + $expire : 0;

		return setcookie(($prefix ? COOKIE_PREFIX : '') . $name, $value, $expire, '/', COOKIE_DOMAIN);
	}

	return false;
}

function delete_cookie($name)
{
	set_cookie($name, '', 0);
}

/* jQuery plugin autoloading */
global $js_autoload;

$js_autoload = array(
	'codemirror'        => array(
		'app/view/js/jquery/plugin/codemirror/codemirror.js',
		'app/view/js/jquery/plugin/codemirror/wrapper.js',
	),
	'ampListing'        => 'app/view/js/jquery/plugin/listings.js',
	'ampSelect'         => 'app/view/js/jquery/plugin/ampSelect.js',
	'listview'          => 'app/view/js/jquery/plugin/listings.js',
	'ampFilter'         => 'app/view/js/jquery/plugin/listings.js',
	'ampUpload'         => 'app/view/js/jquery/plugin/ampUpload.js',
	'ampManager'        => 'app/view/js/jquery/plugin/ampManager.js',
	'ampFileManager'    => 'app/view/js/jquery/plugin/ampFileManager.js',
	'ampContactManager' => 'app/view/js/jquery/plugin/ampContactManager.js',
	'ampPageBreak'      => 'app/view/js/jquery/plugin/ampPageBreak.js',
	'ac_template'       => 'app/view/js/jquery/plugin/ac_template.js',
	'ampSlider'         => 'app/view/js/jquery/plugin/ampSlider.js',
	'flexselect'        => 'app/view/js/jquery/plugin/flexselect.js',
	'spectrum'          => 'app/view/js/jquery/plugin/colorpicker/spectrum.js',
	'jqzoom'            => 'app/view/js/jquery/plugin/jqzoom/jqzoom.js',
	'ac_imageinput'     => 'app/view/js/jquery/plugin/image_manager.js',
	'ac_filemanager'    => 'app/view/js/jquery/plugin/image_manager.js',
	'renderChart'       => array(
		'system/resources/js/chartjs/chart.js',
		'system/resources/js/chartjs/barext.js'
	),
);


/**************************************
 * System Language Translation Engine *
 **************************************/

global $language_group, $_translations;
$language_group = "Load";

/**
 * Translate a string to the current requested language
 *
 * @param $message
 * @return mixed|string
 */
function _l($message)
{
	//TODO: Set translations based on language group
	global $language_group, $_translations;

	if (isset($_translations[$message])) {
		$message = $_translations[$message];
	}

	$values = func_get_args();

	array_shift($values);

	//TODO: See bitbucket issue https://bitbucket.org/newms87/dopencart/issue/20/language-translation-engine
	if (empty($values)) {
		return _($message);
	}

	return vsprintf(_($message), $values);
}

function _translate($message, $translation)
{
	global $_translations;

	$_translations[$message] = $translation;
}

/**
 * Change Language Group just for this message, then revert back if $message is given.
 * If $message is null, then the language group is changed permanently.
 *
 * @param $group   - The language group to change to.
 * @param $message - The Message
 * @param $var1    , $var2, etc.. The variables to pass to vsprintf() with the message.
 *
 * @return null | String with the translated message
 */

function _lg($group, $message = null)
{
	global $language_group;

	//Permanently change Group.
	if ($message === null) {
		$language_group = $group;

		return;
	}

	//Temporarily Change Group
	$temp           = $language_group;
	$language_group = $group;

	$params = func_get_args();
	array_shift($params);

	$return = call_user_func_array('_l', $params);

	$language_group = $temp;

	return $return;
}

//TODO: Maybe make this our main handler for loading (move out of registry)??
if (!function_exists('amplo_autoload')) {
	function amplo_autoload($class)
	{
		global $registry;
		$registry->get($class, false);
	}
}

spl_autoload_register('amplo_autoload');

function register_routing_hook($name, $callable, $sort_order = 0)
{
	global $registry;

	//In case called when system not booted (eg: install.php)
	if ($registry) {
		return $registry->get('router')->registerHook($name, $callable, $sort_order);
	}
}

/**
 * Customized routing for special cases. Set a new $path to change the controller / method to call.
 * Or use $registry->get('router')->setPath($path) to emulate the browser calling the controller / method.
 *
 * To register your own routing hook use $this->router->registerRoutingHook('my-hook-name', 'my_routing_hook');
 * in your plugin's setup.php install() method.
 *
 * @param string $path      - The current path that points to the controller and method to call
 * @param        $segments  - The path segments broken up into an array.
 * @param string $orig_path - The original path (in case $path has been modified by another hook).
 *                          NOTE: If a hook has used Route::setPath(), $orig_path will be modified (consider setting
 *                          $sort_order for your hook to avoid conflicts).
 *
 * @return bool | null - if the return value is false no other hooks will be called.
 */
function amplo_routing_hook($router)
{
	global $registry;

	//Cron Called from system
	if (option('cron_status', true)) {
		if (defined('RUN_CRON')) {
			die($registry->get('cron')->run());
		} elseif (isset($_GET['run_cron'])) {
			die(nl2br($registry->get('cron')->run()));
		} elseif (option('cron_check')) {
			//Run the cron every 60 seconds (Poor man's cron)
			if (microtime(true) - (int)option('cron_last_run') > 60) {
				$registry->get('cron')->run();
			}
		}
	}

	$nodes = $router->getNode();

	if (IS_ADMIN) {
		//Initialize site configurations
		$config = $registry->get('config');
		$config->runSiteConfig();

		if (!IS_AJAX && option('config_maintenance')) {
			if (isset($_GET['hide_maintenance_msg'])) {
				$_SESSION['hide_maintenance_msg'] = 1;
			} elseif (!isset($_SESSION['hide_maintenance_msg'])) {
				$hide = $registry->get('url')->here('hide_maintenance_msg=1');
				message('notify', _l("Site is in maintenance mode. You may still access the site when signed in as an administrator. <a href=\"%s\">(hide message)</a> ", $hide));
			}
		}

		if (empty($nodes[1])) {
			$admin_path = option('admin_path', 'admin/index');
			$router->setPath(($admin_path && $admin_path !== 'admin') ? $admin_path : 'admin/index');
		}
	} else {
		if (option('config_maintenance')) {
			return $router->setPath('common/maintenance');
		}

		$terms_agreement_date = option('terms_agreement_date');

		if ($terms_agreement_date) {
			$date = $registry->get('date');

			$cookie_date   = _cookie('terms_agreed_date');
			$customer_date = customer_meta('terms_agreed_date');

			if ($customer_date && $cookie_date) {
				$customer_agreed_date = $date->isAfter($cookie_date, $customer_date) ? $cookie_date : $customer_date;
			} else {
				$customer_agreed_date = $cookie_date ? $cookie_date : $customer_date;
			}

			set_option('show_terms_agreement', $terms_agreement_date && (!$customer_agreed_date || $date->isAfter($terms_agreement_date, $customer_agreed_date)));
		}

		if (is_logged() && !option('show_terms_agreement') && option('confirm_customer_email', true)) {
			if (customer_meta('confirmed_email') !== customer_info('email') && empty($_SESSION['message']['confirm-email'])) {
				message('confirm-email', _l("Please confirm your email in the message we sent to <b>%s</b>. <a href=\"%s\">Resend Email</a>", customer_info('email'), site_url('account/resend-confirmation')), 'confirm');
			}
		}

		//Path Rerouting
		switch ($nodes[0]) {
			case 'page':
				if (!empty($nodes[1]) && $nodes[1] !== 'preview') {
					$router->setPath('page', $nodes, $router->getSegment());
				}
				break;
		}
	}
}

//Register the core routing hook
register_routing_hook('amplo', 'amplo_routing_hook');

if (!function_exists('array_column')) {
	/**
	 * PHP < 5.5 backwards Compatibility
	 *
	 * Returns an array of elements from the column of an array
	 *
	 * @param array      array - A multi-dimensional array (record set) from which to pull a column of values.
	 * @param column_key string - The column of values to return. This value may be the integer key of the column you wish to retrieve, or it may be the string key name for an associative array. It may also be NULL to return complete arrays (useful together with index_key to reindex the array).
	 * @param index_key  bool - The column to use as the index/keys for the returned array. This value may be the integer key of the column, or it may be the string key name.
	 *
	 * @return array - Returns an array of values representing a single column from the input array.
	 */
	function array_column($array, $column_key, $index_key = null)
	{
		$values = array();

		foreach ($array as $row) {
			$value = isset($row[$column_key]) ? $row[$column_key] : null;

			if ($index_key === null) {
				$values[] = $value;
			} elseif (isset($row[$index_key])) {
				$values[$row[$index_key]] = $value;
			} else {
				trigger_error(_l("%s: The index key should be set for all rows in the array.", __FUNCTION__));

				return array();
			}
		}

		return $values;
	}
}

if (!function_exists('array_column_recursive')) {
	/**
	 * PHP < 5.5 backwards Compatibility
	 *
	 * Returns an array of elements from the column of an array
	 *
	 * @param array  array - An associative array of arrays
	 * @param column string - The key column of the $array to get elements for
	 *
	 * @return array - an array of values of the column requested
	 */
	function array_column_recursive($array, $column)
	{
		$values = array();

		if (is_array($array) && !empty($array)) {
			foreach ($array as $row) {
				if (!is_array($row)) {
					continue;
				}

				if (!isset($row[$column])) {
					$values += array_column_recursive($row, $column);
				} else {
					$values[] = $row[$column];
				}
			}
		}

		return $values;
	}
}

if (!function_exists('array_search_key')) {
	/**
	 * Searches for an element in a multidimensional array for an element key that matches search_key and
	 * value that matches needle.
	 * It will return the array that contains the search_key => needle pair.
	 *
	 * @param search_key mixed - Either a string or int to search by the array key
	 * @param needle     mixed - The searched value. If needle is a string, the comparison is done in a case-sensitive manner.
	 * @param haystack   array - The array.
	 * @param strict     bool[optional] - If the third parameter strict is set to true then the array_search function will search for identical elements in the haystack.
	 *                   This means it will also check the types of the needle in the haystack, and objects must be the same instance.
	 *
	 * @return mixed the key for needle if it is found in the array, false otherwise.
	 */

	function array_search_key($search_key, $needle, array $haystack, $strict = false)
	{
		foreach ($haystack as $key => $value) {
			if (is_array($value)) {
				$result = array_search_key($search_key, $needle, $value, $strict);

				if ($result !== null) {
					return $result;
				}
			}

			if ($key === $search_key && $value == $needle) {
				return $haystack;
			}
		}
	}
}

if (!function_exists('array_unique_keys')) {
	/**
	 * Searches for a duplicate elements in a multidimensional array by a list of keys
	 *
	 * @param array array - The array to filter duplicate values from
	 * @param key1  string - the first key to filter by
	 * @param key2  ... string (optional) - the second key to filter by
	 *
	 * @return array An array of arrays with unique elements based on specified keys
	 */

	function array_unique_keys($array)
	{
		$keys = func_get_args();
		array_shift($keys);

		foreach ($array as $index => $ele) {
			foreach ($keys as $key) {
				if (isset($ele[$key])) {
					foreach ($array as $index2 => $ele2) {
						if ($index !== $index2 && isset($ele2) && $ele2[$key] == $ele[$key]) {
							unset($array[$index]);
							continue 2;
						}
					}
				}
			}
		}

		return $array;
	}
}

if (!function_exists('array_walk_children')) {
	/**
	 * Applies a callback function on every node element of an array tree
	 *
	 * @param array    $array_tree - The array Tree to walk recursively
	 * @param string   $children   - The array key id for the child nodes
	 * @param callback $callback   - The Callback function to apply on every node of the array
	 * @param          mixed       arg1 - The first parameter to pass to each callback call
	 * @params mixed arg2 - The 2nd parameter...etc.
	 *
	 * @return void
	 */

	function array_walk_children(&$array_tree, $children, $callback)
	{
		reset($array_tree);

		if (!is_array($array_tree)) {
			$array_tree = array($array_tree);
		}

		foreach ($array_tree as $key => &$node) {
			$args = func_get_args();
			array_splice($args, 0, 3);

			$return = call_user_func_array($callback, array_merge(array(
				&$node,
				$key,
			), $args));

			//Cancel the walk
			if ($return === false) {
				return false;
			}

			if (!empty($node[$children])) {
				$return = call_user_func_array('array_walk_children', array_merge(array(
					&$node[$children],
					$children,
					$callback,
				), $args));

				//Cancel the walk
				if ($return === false) {
					return false;
				}
			}
		}
		unset($node);
	}
}

if (!function_exists('html2text')) {
	/**
	 * Converts HTML break tags (eg: <br />) to new lines, and removes all other HTML tags
	 *
	 * @param string $html - The HTML to replace all the
	 *
	 * @return String of plain text
	 */
	function html2text($html)
	{
		return strip_tags(preg_replace("/<br\s*\/?>/", "\r\n", $html));
	}
}

if (!function_exists('json_last_error_msg')) {
	function json_last_error_msg()
	{
		static $errors = array(
			JSON_ERROR_NONE           => null,
			JSON_ERROR_DEPTH          => 'Maximum stack depth exceeded',
			JSON_ERROR_STATE_MISMATCH => 'Underflow or the modes mismatch',
			JSON_ERROR_CTRL_CHAR      => 'Unexpected control character found',
			JSON_ERROR_SYNTAX         => 'Syntax error, malformed JSON',
			JSON_ERROR_UTF8           => 'Malformed UTF-8 characters, possibly incorrectly encoded',
		);
		$error = json_last_error();

		return array_key_exists($error, $errors) ? $errors[$error] : "Unknown error ({$error})";
	}
}

if (!function_exists('imagepalettetotruecolor')) {
	function imagepalettetotruecolor(&$src)
	{
		if (imageistruecolor($src)) {
			return (true);
		}

		$dst = imagecreatetruecolor(imagesx($src), imagesy($src));

		imagecopy($dst, $src, 0, 0, 0, 0, imagesx($src), imagesy($src));
		imagedestroy($src);

		$src = $dst;

		return true;
	}
}

/**
 * This is a wrapper for is_link() to check for windows .lnk files
 * TODO: This is here temporarily, testing to ensure no issues with links on windows (dont mind the .lnk issue)
 *
 * @param $filename
 * @return bool
 */
function _is_link($filename)
{
	if ($filename) {
		clearstatcache();

		return is_file($filename) && (is_link($filename) || readlink($filename) !== $filename);
	}

	return false;
}

if (!function_exists('sort_by')) {
	function sort_by(&$array, $key, $reverse = false, $assoc = true, $limit = null)
	{
		$sort = function ($a, $b) use ($key, $reverse) {
			if (!isset($a[$key]) || !isset($b[$key])) {
				return 0;
			}

			return $reverse ? $a[$key] < $b[$key] : $a[$key] > $b[$key];
		};

		if ($assoc) {
			uasort($array, $sort);
		} else {
			usort($array, $sort);
		}

		if ($limit) {
			$array = array_slice($array, 0, $limit);
		}
	}
}

if (!defined('PASSWORD_DEFAULT')) {
	require_once(DIR_RESOURCES . 'password_compat.php');
}

function _set_site($site, $refresh_settings = false)
{
	global $registry;

	if (!is_array($site)) {
		$site = $registry->get('Model_Site')->getRecord($site);
	}

	if ($site) {
		$registry->get('router')->setSite($site, $refresh_settings);

		return true;
	}

	return false;
}

function _set_prefix($prefix)
{
	global $registry;
	$registry->get('db')->setPrefix($prefix);
	$registry->get('cache')->setDir(DIR_CACHE . $prefix);
}

function get_caller($offset = 0, $limit = 20)
{
	$calls = debug_backtrace(false);

	$html = '';

	$limit += $offset;

	while ($offset < $limit && $offset < count($calls)) {
		$caller = $calls[$offset];

		if (isset($caller['file'])) {
			$msg = "Called from <b style=\"color:red\">$caller[file]</b> on line <b style=\"color:red\">$caller[line]</b>";
		} elseif (isset($caller['class'])) {
			$msg = "Called from <b style=\"color:red\">$caller[class]::$caller[function]</b>";
		} else {
			$msg = "Called from <b style=\"color:red\">$caller[function]()</b>";
		}

		$html = "<div style=\"margin-top:5px\"><b>&#187;</b> $msg</div>" . $html;

		$offset++;
	}

	return "<div style=\"margin-top: 8px; margin-bottom: 8px; margin-left: 15px\">$html</div>";
}

function write_log($type, $msg)
{
	global $registry;

	if (!$registry->has('log_' . $type)) {
		$registry->set('log_' . $type, new Log($type));
	}

	return $registry->get('log_' . $type)->write($msg);
}

//Error Callbacks allow customization of error display / messages
$error_callbacks = array();

if (!function_exists('amplo_error_handler')) {
	function amplo_error_handler($errno, $errstr, $errfile, $errline, $errcontext)
	{
		// error was suppressed with the @-operator
		if (!error_reporting()) {
			return false;
		}

		switch ($errno) {
			case E_NOTICE:
			case E_USER_NOTICE:
				$error = 'Notice';
				break;
			case E_WARNING:
			case E_USER_WARNING:
				$error = 'Warning';
				break;
			case E_ERROR:
			case E_USER_ERROR:
				$error = 'Fatal Error';
				break;
			default:
				$error = 'Unknown';
				break;
		}

		$error .= ' (' . $errno . ')';

		global $error_callbacks, $_options;

		if (!empty($error_callbacks)) {
			foreach ($error_callbacks as $cb) {
				$cb($error, $errno, $errstr, $errfile, $errline);
			}
		}

		if ($error) {
			$display_error = ini_get('display_errors');
			$send_email    = (!isset($_options['error_notification_email']) || $_options['error_notification_email']) && !empty($_options['site_email_error']);

			if ($send_email || $display_error) {
				$stack = get_caller(1);

				$html_error = <<<HTML
			<style>
				.error-display {
					position: relative;
					text-align: left;
					z-index: 10000;
					padding: 10px;
					border-radius: 5px;
					background: white;
					color: black;
					font-size: 14px;
					border: 1px solid black;
				}
				.error-display .label {
					width: 70px;
					display:inline-block;
					font-weight: bold;
				}

				.error-display a {
					color: blue;
				}
			</style>
			<div class="error-display">
				<div class="type"><span class="label">Type:</span> <span class="value">$error</span></div>
				<div class="msg"><span class="label">Message:</span> <span class="value">$errstr</span></div>
				<div class="file"><span class="label">File:</span> <span class="value">$errfile</span></div>
				<div class="line"><span class="label">Line:</span> <span class="value">$errline</span></div>
				<div class="stack">$stack</div>
			</div>
HTML;
			}

			if ($display_error) {
				echo $html_error;
				flush(); //Flush the error to block any redirects that may execute, this ensures errors are seen!
			}

			if (!isset($_options['error_logging']) || $_options['error_logging']) {
				$log_id = write_log('error', 'PHP ' . $error . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
			}

			if ($send_email) {
				$log_url = '';

				if (!empty($log_id)) {
					$filter = array(
						'filter' => array(
							'log_id' => array(
								'gte' => $log_id,
								'lte' => $log_id + 10,
							),
						),
					);

					$log_url = "<a href=\"" . HTTP_SITE . 'admin/logs?' . http_build_query($filter) . "\">View Logs</a><br><br>";
				}

				$email_error = DOMAIN . $_SERVER['REQUEST_URI'] . "<br><br>" . $log_url . $html_error;

				send_mail(array(
					'to'      => $_options['site_email_error'],
					'subject' => "Amplo Error on " . DOMAIN . ': ' . $error,
					'html'    => $email_error,
				));
			}
		}

		return true;
	}
}

//Register Error Handler
set_error_handler('amplo_error_handler');

//Amplo Time handling
function timelog($name)
{
	global $__start;
	file_put_contents(DIR_LOGS . 'timelog.txt', '[' . date('Y-m-d H:i:s') . '] ' . $name . ' - ' . (microtime(true) - $__start) . "\n", FILE_APPEND);
}

//In preparation for time manipulation. (see the dev plugin)
function _time()
{
	global $ac_time_offset;

	return time() + $ac_time_offset;
}

function _filemtime($file)
{
	global $ac_time_offset;

	return filemtime($file) + ($ac_time_offset * 1000);
}

//File Return Types for get_files()
define("FILELIST_STRING", 1);
define("FILELIST_SPLFILEINFO", 2);
define("FILELIST_RELATIVE", 3);

/**
 * Retrieves files in a specified directory recursively
 *
 * @param $dir         - the directory to recursively search for files
 * @param $exts        - the file extensions to search for. Use false to include all file extensions.
 * @param $return_type - can by FILELIST_STRING, FILELIST_RELATIVE (Relative path from $dir) or FILELIST_SPLFILEINFO
 *                     (for an SPLFileInfo Object)
 *
 * @return array - Each value in the array will be determined by the $return_type param.
 */
function get_files($dir, $exts = null, $return_type = FILELIST_SPLFILEINFO, $filter_preg = null)
{
	if ($exts === null) {
		$exts = array(
			'php',
			'tpl',
			'css',
			'js',
		);
	} elseif (is_string($exts)) {
		$exts = explode(',', $exts);
	}

	if (!is_dir($dir)) {
		return array();
	}

	$dir_iterator = new RecursiveDirectoryIterator($dir);
	$iterator     = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::CHILD_FIRST);

	$files = array();

	foreach ($iterator as $file) {
		$ext = pathinfo($file->getFileName(), PATHINFO_EXTENSION);
		if ($file->isFile() && (!$exts || in_array($ext, $exts)) && (!$filter_preg || preg_match("/" . $filter_preg . "/", $file->getPathName()))) {
			switch ($return_type) {
				case FILELIST_STRING:
					$files[] = str_replace('\\', '/', $file->getPathName());
					break;
				case FILELIST_RELATIVE:
					$files[] = substr(str_replace('\\', '/', $file->getPathName()), strlen($dir));
					break;
				case FILELIST_SPLFILEINFO:
					$files[] = $file;
					break;
				default:
					trigger_error(__FUNCTION__ . ": invalid return type requested! Options are FILELIST_SPLFILEINFO or FILELIST_STRING. SplFileInfo type was returned.");
					$files[] = $file;
					break;
			}
		}
	}

	return $files;
}

function get_unique_file($file, $dir = '')
{
	if (!is_file($dir . $file)) {
		return $file;
	}

	$pathinfo = pathinfo($file);
	$count    = 1;

	do {
		$file = $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '-' . $count++ . '.' . $pathinfo['extension'];
	} while (is_file($dir . $file));

	return $file;
}

/**
 * Parses PHPDoc comments for Directives in the form: Directive: String information
 *
 * @param string $content - Can be a file or a string containing the comment directives
 *
 * @return array - An associative array with key as the Comment Directive, and value of the String following the ':'
 */
function get_comment_directives($content, $trim = true)
{
	if (is_file($content)) {
		$cache      = 'cd.' . slug($content);
		$directives = cache($cache, null, false, filemtime($content));

		if ($directives) {
			return $directives;
		}

		$content = file_get_contents($content);
	}

	$directives = array();

	if ($content) {
		$tokens = token_get_all($content);

		foreach ($tokens as $token) {
			if ($token[0] === T_DOC_COMMENT) {
				if (preg_match_all("/(.*?)([a-z0-9_-]*?):(.*?)\\*/is", $token[1], $matches)) {
					$directives = array_change_key_case(array_combine($matches[2], $matches[3]));
				}
			}
		}

		if ($trim) {
			array_walk($directives, function (&$a) {
				$a = trim($a);
			});
		}
	}

	if (!empty($cache)) {
		cache($cache, $directives);
	}

	return $directives;
}

function rrmdir($dir)
{
	if (is_dir($dir)) {
		foreach (glob($dir . '/{,.}*', GLOB_BRACE) as $file) {
			if (preg_match("/\\/(\\.|\\.\\.)$/", $file)) {
				continue;
			}

			if (is_dir($file)) {
				rrmdir($file);
			} else {
				@unlink($file);
			}
		}

		return @rmdir($dir);
	}
}

function _is_object($o)
{
	return is_array($o) || is_object($o) || is_resource($o);
}

function _2camel($str, $lower = false)
{
	$parts = explode('_', $str);

	//capitalize each component of the class name
	array_walk($parts, function (&$e) {
		$e = ucfirst($e);
	});

	$str = implode('', $parts);

	return $lower ? lcfirst($str) : $str;
}

function camel2_($str)
{
	$matches = null;
	preg_match_all("/([A-Z][a-z0-9]*)/", ucfirst($str), $matches);

	return strtolower(implode("_", $matches[1]));
}

function path2class($path)
{
	$replace = array(
		DIR_SITE => '',
		"\\"     => '/',
		'_'      => '',
		'/'      => '_',
	);

	return str_replace(array_keys($replace), $replace, $path);
}

function path_format($path, $cast_underscore = true)
{
	$path = strtolower(trim(str_replace('\\', '/', $path), '/ '));

	if ($cast_underscore) {
		$path = str_replace('-', '_', $path);
	}

	return $path;
}

function insertables($insertables, $text, $start = '%', $end = '%')
{
	$patterns     = array();
	$replacements = array();

	foreach ($insertables as $key => $value) {
		$patterns[]     = "/$start" . $key . "$end/";
		$replacements[] = $value;
	}

	return preg_replace($patterns, $replacements, $text);
}

function _strip_tags($text)
{
	return strip_tags(preg_replace("/<br\\s*\\/?>/", ' ', $text));
}

function bytes2str($size, $decimals = 2, $unit = null)
{
	$unit_sizes = array(
		'TB' => 1099511627776,
		'GB' => 1073741824,
		'MB' => 1048576,
		'KB' => 1024,
		'B'  => 1,
	);

	if ($unit && isset($unit_sizes[$unit])) {
		$divisor = $unit_sizes[$unit];
	} else {
		foreach ($unit_sizes as $key => $unit_size) {
			if ($size > $unit_size) {
				$divisor = $unit_size;
				$unit    = $key;
				break;
			}
		}
	}

	if ($unit == 'B') {
		$decimals = 0;
	}

	return sprintf("%." . $decimals . "f $unit", ($size / $divisor));
}

function str2bytes($str)
{
	$str  = trim($str);
	$last = strtolower($str[strlen($str) - 1]);

	if ($last === 'b') {
		$last = strtolower($str[strlen($str) - 2]);
	}

	switch ($last) {
		case 'g':
			return $str * 1073741824;
		case 'm':
			return $str * 1048576;
		case 'k':
			return $str * 1024;
	}
}

function str_width($str, $font = null, $size = 16, $angle = 0)
{
	if (!$font) {
		$font = theme_dir('font/opensans/opensans-regular-webfont.ttf');
	}

	$points = imagettfbbox($size * 3 / 4, $angle, $font, $str);

	if ($points) {
		return abs($points[2] - $points[0]);
	}

	return false;
}

function parse_xml_to_array($xml)
{
	$return = array();

	foreach ($xml->children() as $parent => $child) {
		$the_link = false;
		foreach ($child->attributes() as $attr => $value) {
			if ($attr == 'href') {
				$the_link = $value;
			}
		}

		$return["$parent"][] = $child->children() ? parse_xml_to_array($child) : ($the_link ? "$the_link" : "$child");
	}

	return $return;
}

/**
 * @param      $key        - Unique key identifying cache file.
 * @param      $value      - If set, the cache value will be set, otherwise retreive contents of cache file
 * @param bool $as_file    - Return the cache file instead of its contents
 * @param int  $invalidate - If the cache file was created before $invalidate, then return null. compares as unix
 *                         timestamps.
 * @return mixed - if $value is set, then return a bool indicating successfully setting cache file, else return cache
 *                         file / data if it exists, otherwise return null
 */
function cache($key, $value = null, $as_file = false, $invalidate = false)
{
	global $registry;

	if ($value === null) {
		return $registry->get('cache')->get($key, $as_file, $invalidate);
	} else {
		return $registry->get('cache')->set($key, $value, $as_file);
	}
}

function clear_cache($key = null)
{
	global $registry;
	$registry->get('cache')->delete($key);
}

function clear_cache_all()
{
	rrmdir(DIR_CACHE);
}

function crypto_rand($min, $max)
{
	$range = $max - $min;
	if ($range < 0) {
		return $min;
	} // not so random...
	$log    = log($range, 2);
	$bytes  = (int)($log / 8) + 1; // length in bytes
	$bits   = (int)$log + 1; // length in bits
	$filter = (int)(1 << $bits) - 1; // set all lower bits to 1
	do {
		$rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
		$rnd = $rnd & $filter; // discard irrelevant bits
	} while ($rnd >= $range);

	return $min + $rnd;
}

function tokengen($length)
{
	$token        = '';
	$codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
	$codeAlphabet .= "0123456789";

	for ($i = 0; $i < $length; $i++) {
		$token .= $codeAlphabet[crypto_rand(0, strlen($codeAlphabet))];
	}

	return $token;
}
