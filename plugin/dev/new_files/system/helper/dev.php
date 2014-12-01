<?php
//Amplo Performance Logging
if (!defined("SHOW_DB_PROFILE")) {
	define("SHOW_DB_PROFILE", defined("DB_PROFILE") && DB_PROFILE);
}

if (!defined("DB_PROFILE_NO_CACHE")) {
	define("DB_PROFILE_NO_CACHE", true);
}

//SIM TIME
//Virtual time (for simulating time progression)
$ac_time_offset = !empty($_COOKIE['ac_time_offset']) ? (int)$_COOKIE['ac_time_offset'] : 0;

//Only allow logged in users to sim time.
if (empty($_SESSION['user_id'])) {
	$ac_time_offset = 0;
} elseif (!empty($_GET['sim_time'])) {
	if ($_GET['sim_time'] === 'reset') {
		$ac_time_offset = 0;
	} else {
		$ac_time_offset += (int)$_GET['sim_time'];
	}
}

//TODO: Implement full system profile
function _profile($key, array $data = array())
{
	global $profile, $__start;

	$mb        = 1024 * 1024;
	$memory    = round(memory_get_peak_usage() / $mb, 2) . " MB";
	$allocated = round(memory_get_peak_usage(true) / $mb, 2) . " MB";
	$time      = round(microtime(true) - $__start, 6);

	$data += array(
		'time'      => $time,
		'memory'    => $memory,
		'allocated' => $allocated,
	);

	$profile[$key] = $data;
}

//custom var dump
function html_dump($var, $label = "HTML Dump", $show_type = false, $level = 0, $max = -1, $print = true)
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
	html_dump_r($var, $level, $max, $show_type);
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

function html_dump_r($var, $level, $max, $show_type = false)
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
				html_dump_r($v, $level + 1, $max, $show_type);
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
					if ($show_type) {
						$val = '(' . gettype($v) . ')' . $v;
					} else {
						$val = $v;
					}
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
