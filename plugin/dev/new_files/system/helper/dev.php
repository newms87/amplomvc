<?php
/**
 * @author  Daniel Newman
 * @date    3/20/2013
 * @package Amplo MVC Dev Plugin
 * @link    http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYING.txt and LICENSE.txt files in the root directory.
 */

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

/**
 * @param mixed  $var       - The variable to display all data for. Can be any type (int, array, object, etc..)
 * @param string $label     - The label for the link to the dump report (which opens in a new window)
 * @param bool   $show_type - Show the types for string, int, float and bool data
 * @param int    $level     - The starting recursive depth for arrays
 * @param int    $max       - The maximum recursive depth for arrays
 * @param bool   $print     - print and return the results. False will only return the HTML results.
 *
 * @return string - The HTML dump output
 */
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
			margin-left: 20px;
			z-index: 100000;
  			position: relative
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
				background: #BBEFBB;
				padding: 3px 5px
			}

			.dump_output .key_value_pair > .value {
				background: #B8C8E7;
				max-width: 800px;
				word-wrap: break-word;
				color: #28581B;
				font-weight: bold;
			}

			.dump_output .key_value_pair > .value.string {
				font-weight: normal;
			}

			.dump_output .key_value_pair > .value.integer {
				color: #813029;
			}

			.dump_output .key_value_pair > .value.double {
				color: #813029;
			}

			.dump_output .key_value_pair > .value.NULL {
				color: #E10B44;
			}

			.dump_output .key_value_pair > .value.boolean {
				color: #19028D;
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
				$val_type = gettype($v);

				if (is_array($v)) {
					$val = "Array (" . count($v) . ")";
				} elseif (is_object($v)) {
					$val = "Object (" . count($v) . ")";
				} elseif (is_bool($v)) {
					$val = "(bool) " . ($v ? "true" : "false");
				} elseif (is_string($v) && empty($v) && $v !== '0') {
					$val = "(empty string)";
				} elseif ($v === null) {
					$val = "NULL";
				} else {
					if ($show_type) {
						$val = "($val_type) $v";
					} else {
						$val = $v;
					}
				}

				echo "<td class=\"value $val_type\">$val</td>";
			}
			echo "</tr>";
		}
		echo "</table>";
	} else {
		htmlspecialchars(var_dump($var));
	}
}
