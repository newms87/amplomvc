<?php
//TODO: Maybe make this our main handler for loading (move out of registry)??
if (!function_exists('amplo_autoload')) {
	function amplo_autoload($class) {
		global $registry;
		$registry->loadClass($class, false);
	}
}

spl_autoload_register('amplo_autoload');

if (!function_exists('array_column')) {
	/**
	 * PHP < 5.5 backwards Compatibility
	 *
	 * Returns an array of elements from the column of an array
	 *
	 * @param array array - An associative array of arrays
	 * @param column string - The key column of the $array to get elements from
	 * @param assoc bool - Return an associative array with the key the same as the value (all values will be unique!)
	 *
	 * @return array - an array of values of the column requested
	 */
	function array_column($array, $column, $index_key = null)
	{
		$values = array();

		foreach ($array as $row) {
			$value = isset($row[$column]) ? $row[$column] : null;

			if (is_null($index_key)) {
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
	 * @param array array - An associative array of arrays
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
	 * @param needle mixed - The searched value. If needle is a string, the comparison is done in a case-sensitive manner.
	 * @param haystack array - The array.
	 * @param strict bool[optional] - If the third parameter strict is set to true then the array_search function will search for identical elements in the haystack.
	 * This means it will also check the types of the needle in the haystack, and objects must be the same instance.
	 *
	 * @return mixed the key for needle if it is found in the array, false otherwise.
	 */

	function array_search_key($search_key, $needle, $haystack, $strict = false)
	{
		foreach ($haystack as $key => $value) {
			if (is_array($value)) {
				$result = array_search_key($search_key, $needle, $value, $strict);

				if (!is_null($result)) {
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
	 * @param key1 string - the first key to filter by
	 * @param key2 ... string (optional) - the second key to filter by
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
	 * @param array $array_tree - The array Tree to walk recursively
	 * @param string $children - The array key id for the child nodes
	 * @param callback $callback - The Callback function to apply on every node of the array
	 * @param mixed arg1 - The first parameter to pass to each callback call
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
				$key
			), $args));

			//Cancel the walk
			if ($return === false) {
				return false;
			}

			if (!empty($node[$children])) {
				$return = call_user_func_array('array_walk_children', array_merge(array(
					&$node[$children],
					$children,
					$callback
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

/**
 * This is a wrapper for is_link() to check for windows .lnk files
 * TODO: This is here temporarily, testing to ensure no issues with links on windows (dont mind the .lnk issue)
 *
 * @param $filename
 * @return bool
 */
function _is_link($filename)
{
	clearstatcache();

	return is_link($filename) || readlink($filename) !== $filename;
}

if (!function_exists('sort_by')) {
	function sort_by(&$array, $key, $reverse = false, $assoc = true, $limit = null)
	{
		$sort = function ($a, $b) use ($key, $reverse) {
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

function get_caller($offset = 0, $limit = 10)
{
	$calls = debug_backtrace(false);

	$html = "";

	$limit += $offset;

	while ($offset < $limit && $offset < (count($calls) - 1)) {
		$caller = $calls[$offset + 1];

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
		$registry->set('log_' . $type, new Log($type, option('store_id')));
	}

	return $registry->get('log_' . $type)->write($msg);
}

//Error Callbacks allow customization of error display / messages
$error_callbacks = array();

if (!function_exists('amplo_error_handler')) {
	function amplo_error_handler($errno, $errstr, $errfile, $errline, $errcontext)
	{
		// error was suppressed with the @-operator
		if (!ini_get('display_errors') || 0 === error_reporting()) {
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

		global $error_callbacks;

		if (!empty($error_callbacks)) {
			foreach ($error_callbacks as $cb) {
				$cb($error, $errno, $errstr, $errfile, $errline);
			}
		}

		if ($error) {
			if (option('config_error_display')) {
				$stack = get_caller(1, 10);

				echo <<<HTML
			<style>
				.error_display {
					padding: 10px;
					border-radius: 5px;
					background: white;
					color: black;
					font-size: 14px;
					border: 1px solid black;
				}
				.error_display .label {
					width: 70px;
					display:inline-block;
					font-weight: bold;
				}

				.error_display a {
					color: blue;
				}
			</style>
			<div class="error_display">
				<div class="type"><span class="label">Type:</span> <span class="value">$error</span></div>
				<div class="msg"><span class="label">Message:</span> <span class="value">$errstr</span></div>
				<div class="file"><span class="label">File:</span> <span class="value">$errfile</span></div>
				<div class="line"><span class="label">Line:</span> <span class="value">$errline</span></div>
				<div class="stack">$stack</div>
			</div>
HTML;

				flush(); //Flush the error to block any redirects that may execute, this ensures errors are seen!
			}

			if (option('config_error_log')) {
				write_log('error', 'PHP ' . $error . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
			}
		}

		return true;
	}
}

//Register Error Handler
set_error_handler('amplo_error_handler');

//Amplo Time handling
if (!defined("AMPLO_TIME_LOG")) {
	define("AMPLO_TIME_LOG", false);
}

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
