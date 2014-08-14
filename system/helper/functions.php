<?php

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
	function array_column($array, $column, $assoc = false)
	{
		$values = array();

		foreach ($array as $row) {
			$value = isset($row[$column]) ? $row[$column] : null;

			if ($assoc) {
				$values[is_null($value) ? '' : $value] = $value;
			} else {
				$values[] = $value;
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

			$return = call_user_func_array($callback, array_merge(array(&$node, $key), $args));

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
	require_once(DIR_SITE . 'system/helper/password_compat.php');
}

function get_caller($offset = 0, $limit = 10)
{
	$calls = debug_backtrace(false);

	$html = "";

	$limit += $offset;

	while ($offset < $limit && $offset < (count($calls)-1)) {
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

//custom var dump
function html_dump($var, $label = "HTML Dump", $level = 0, $max = -1, $print = true)
{
	static $first = true, $count = 0;

	$id = uniqid('html_dump-' . $count++);

	if (!$print) {
		ob_start();
	}

	if ($first) {
		echo <<<HTML
	<style>
		.html_dump {
			display: inline-block;
			margin-bottom: 15px;
			margin-left: 20px
		}
	</style>
HTML;
	}

	echo <<<HTML
	<a id="$id" class='html_dump' onclick="open_html_dump('$id')">
		<style>
			.html_dump_label {
				cursor: pointer;
				color: blue;
				text-decoration: underline;
			}

			.dump_output {
				margin: 15px;
			}

			.dump_output .key_value_pair {
				position: relative;
				height: 20px;
				overflow: visible;
			}

			.dump_output .type_label {
				background: #EF99A8;
			}

			.dump_output .key_value_pair > .key {
				word-wrap: break-word;
				max-width: 200px;
				background: #82E182;
				padding: 3px 5px
			}

			.dump_output .key_value_pair > .value {
				background: #92ADE3;
				max-width: 800px;
				word-wrap: break-word
			}
		</style>
		<span class='html_dump_label'>$label</span>

		<div class='dump_output' id='$id-output' style='display:none'>
HTML;
	html_dump_r($var, $level, $max);
	echo <<<HTML
		</div>
	</a>
HTML;

	if ($first) {
		echo <<<HTML
	<script type='text/javascript'>
		function open_html_dump(id) {
			var w = window.open(null, 'newwindow', 'resizable=1,scrollbars=1, width=800, height=800');
			document.getElementById(id + '-output').setAttribute('style', 'display:block');
			w.document.body.innerHTML = document.getElementById(id).innerHTML;
			document.getElementById(id + '-output').setAttribute('style', 'display:none');
			w.focus();
		}
	</script>
HTML;
	}

	$first = false;

	if (!$print) {
		return ob_get_clean();
	}
}

function html_dump_r($var, $level, $max)
{
	if (is_array($var) || is_object($var)) {
		$left_offset = $level * 20 . "px";
		$type        = is_array($var) ? "Array" : "Object";
		$type .= " (" . count($var) . ")";
		echo "<table><tr><td class ='type_label' colspan='2'>$type</td></tr>";
		foreach ($var as $key => $v) {
			echo "<tr class ='key_value_pair'>";
			echo "<td valign='top' class='key'>[$key]</td>";

			if ((is_array($v) || is_object($v)) && !($max >= 0 && $level >= ($max - 1))) {
				echo "<td class ='value'>";
				html_dump_r($v, $level + 1, $max);
				echo "</td>";
			} else {
				if (is_array($v)) {
					$val = "Array (" . count($v) . ")";
				} elseif (is_object($v)) {
					$val = "Object (" . count($v) . ")";
				} elseif (is_bool($v)) {
					$val = "Bool (" . ($v ? "true" : "false") . ')';
				} elseif (is_string($v) && empty($v) && $v !== '0') {
					$val = "String (empty)";
				} elseif (is_null($v)) {
					$val = "NULL";
				} else {
					$val = $v;
				}

				echo "<td class ='value'>$val</td>";
			}
			echo "</tr>";
		}
		echo "</table>";
	} else {
		htmlspecialchars(var_dump($var));
	}
}