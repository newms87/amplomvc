<?php
class PrettyLanguage
{
	function __construct()
	{
		$ignore_list = array(DIR_CACHE);
		$ext         = array('php');


		$this->lang_replace_global();

	}

	public function update_template_format($files)
	{
		foreach ($files as $file) {
			$lines = explode("\n", file_get_contents($file));

			$orig_lines = $lines;

			$brackets  = array();
			$to_remove = array();
			$to_add    = array();

			echo $file . '<br>';

			$dir_temp = -1;

			foreach ($lines as $num => $line) {
				for ($i = 0; $i < strlen($line); $i++) {
					if ($line[$i] == '{') {
						array_push($brackets, $num);
					} elseif ($line[$i] == '}') {
						array_pop($brackets);
					}
				}

				if (strpos($line, "DIR_THEME") !== false) {
					$dir_temp = 0;
				}

				switch ($dir_temp) {
					case 0:
						if (!preg_match('/if\s*\(file_exists\(DIR_THEME\s*\.\s*\$this->config->get\([^\)]*\)\s*\.\s*\'[^\']*\'\)\)\s*\{/', $line)) {
							echo "PROBLEM at 0";
							exit;
						}
						$lines[$num] .= "**REMOVE ME**";
						break;
					case 1:
						if (!preg_match('/\$this->template_file\s*=\s*\$this->config->get\(\'config_theme\'\)\s*\.\s*\'[^\']*\';/', $line)) {
							echo "PROBLEM at 1";
							exit;
						}
						$lines[$num] .= "**REMOVE ME**";
						break;
					case 3:
						array_pop($brackets);
						break;
					case 2:
						if (!preg_match('/\}\s*else\s*\{/', $line)) {
							echo "PROBLEM at 2";
							exit;
						}
						$lines[$num] .= "**REMOVE ME**";
						break;
					case 4:
						if (trim($line) != '}') {
							echo "PROBLEM at 4";
							exit;
						}
						$lines[$num] .= "**REMOVE ME**";
						break;
					case 5:
						if (!trim($line)) {
							$lines[$num] .= "**REMOVE ME**";
						}
						$dir_temp = -1;
					default:
						break;
				}

				if ($dir_temp >= 0 && $dir_temp != 3) {
					$dir_temp++;
					continue;
				}

				if (preg_match('/\$this->template_file/', $line)) {
					if (substr_count($line, '=') > 1) {
						echo "WEIRD TEMPLATE LINE!";
						exit;
					}
					$template = trim(preg_replace(array(
						'/\.tpl/',
						'/;/',
						'/[^=]*=/'
					), '', $line));

					if ($dir_temp == 3) {
						if (!preg_match('/default\/template\//', $template)) {
							echo "PROBLEM PREG at 3";
							exit;
						}
						$template = str_replace("default/template/", '', $template);
						$dir_temp++;
					}

					$lines[$num] .= "**REMOVE ME**";

					$to_add[end($brackets)] = '$this->view->load(' . $template . ');' . "\r\n";
				} elseif ($dir_temp == 3) {
					$this->pl($num, $line);
					echo "PROBLEM at 3";
					exit;
				}
			}

			$new_lines      = array();
			$new_orig_lines = array();
			foreach ($lines as $num => $l) {
				if (!preg_match('/\*\*REMOVE ME\*\*/', $l)) {
					$new_lines[] = $l;
				} else {
					$new_lines[] = null;
				}
				$new_orig_lines[] = $l;
				if (in_array($num, array_keys($to_add))) {
					$new_lines[]      = $to_add[$num];
					$new_orig_lines[] = '';
				}
			}

			//$this->print_lines($new_orig_lines, $new_lines, true);
			file_put_contents($file, implode("\n", $new_lines));
		}
	}

	public function get_all_files_r($dir, $ext = array('php'), $ignore = array(), $depth = 0)
	{
		if ($depth > 20) {
			echo "we have too many recursions!";
			exit;
		}

		if (!is_dir($dir) || in_array($dir . '/', $ignore)) {
			return array();
		}

		$handle = @opendir($dir);

		$files = array();
		while (($file = readdir($handle)) !== false) {
			if ($file == '.' || $file == '..') {
				continue;
			}

			$file_path = rtrim($dir, '/') . '/' . $file;

			if (is_dir($file_path)) {
				$files = array_merge($files, $this->get_all_files_r($file_path, $ext,$ignore, $depth + 1));
			} else {
				if (!empty($ext)) {
					$match = null;
					preg_match("/[^\\.]*$/", $file, $match);

					if (!in_array($match[0], $ext)) {
						continue;
					}
				}
				$files[] = $file_path;
			}
		}

		return $files;
	}

	public function print_lines($orig, $lines, $changes_only = true, $special_chars = false)
	{
		$orig_i     = 0;
		$total_orig = count($orig);

		for ($new_i = 0; $new_i < count($lines); $new_i++) {
			$l = $lines[$new_i];

			if ($orig_i >= $total_orig) {
				$color = '#C2E782';
			} elseif ($lines[$new_i] != $orig[$orig_i]) {
				$in_orig = false;
				for ($i = $orig_i; $i < count($orig); $i++) {
					if ($lines[$new_i] == $orig[$i]) {
						$in_orig = $i;
						break;
					}
				}

				$in_new = false;
				for ($i = $new_i; $i < count($lines); $i++) {
					if ($orig[$orig_i] == $lines[$i]) {
						$in_new = $i;
						break;
					}
				}

				if ($in_new && $in_orig) {
					if ($in_new < $in_orig) {
						$in_orig = false;
					} else {
						$in_new = false;
					}
				}

				if ($in_new) {
					for ($i = $new_i; $i < $in_new; $i++) {
						$this->pl($i + 1, $lines[$i], '#C2E782', $special_chars);
					}

					$new_i = $in_new - 1;

					continue;
				} elseif ($in_orig) {
					for ($i = $orig_i; $i < $in_orig; $i++) {
						$this->pl($i + 1, $orig[$i], '#F98888', $special_chars);
					}

					$orig_i = $in_orig;
					$new_i++;

					continue;
				} else {
					$orig_i++;
					$color = '#C282E7';
					/*
					$changes = false;
					for ($i = 0; $i < count($lines) - $new_i; $i++) {
						if (!isset($orig[$orig_i+$i])) break;

						if ($orig[$orig_i+$i] == $lines[$new_i+$i]) {
							echo "FOUND Changes!<br>";
							$changes = $i;
							break;
						}
					}

					if ($changes) {
						for ($i = 0; $i < $changes; $i++) {
							$this->pl($orig_i + $i + 1, $orig[$orig_i + $i], '#A232A7', $special_chars);
							$this->pl($new_i + $i + 1, $lines[$new_i + $i], '#C282E7', $special_chars);
						}

						$new_i += $i-1;
						$orig_i += $i;

						continue;
					} else {
						$color = '#C2E782';
					}*/
				}
			} else {
				$orig_i++;
				if ($changes_only) {
					continue;
				}
				$color = '#CBCBCB';
			}

			$this->pl($new_i + 1, $l, $color, $special_chars);
		}
	}

	public function pl($num, $line, $color = '#CBCBCB', $special_chars = true)
	{
		if ($special_chars) {
			$line = htmlspecialchars($line);
		}

		$line = preg_replace(array(
			'/ /',
			'/\t/'
		), array(
			'&nbsp;',
			'&nbsp;&nbsp;&nbsp;'
		), $line);

		echo "<div style='background: $color'>$num. $line</div>";
	}
}
