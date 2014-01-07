<?php
class PrettyLanguage
{
	function __construct()
	{
		$ignore_list = array(DIR_CACHE);
		$ext         = array('php');


		$files = $this->get_all_files_r(SITE_DIR . 'admin/language/english/', $ext);

		$this->lang_replace($files);

	}

	public function lang_replace($files)
	{
		$find    = array(
			'/language/english/',
			'.php'
		);
		$replace = array(
			'/view/theme/default/template/',
			'.tpl'
		);


		$prefixes = array(
			'text',
			'entry',
			'data',
			'error',
			'button',
			'column',
		);

		$missed_regx = "/\\\$(" . implode('|', $prefixes) . ')_/';

		$_ = array();
		require(SITE_DIR . 'admin/language/english/english.php');
		$default_lang = $_;

		unset($default_lang['data_statuses']);
		unset($default_lang['data_yes_no']);
		unset($default_lang['data_no_yes']);
		unset($default_lang['data_statuses_blank']);
		unset($default_lang['data_yes_no_blank']);
		unset($default_lang['data_no_yes_blank']);

		$ignored_files = array(
			'C:/xampp/htdocs/realmeal/admin/language/english/help/documentation.php',
		);

		foreach ($files as $file) {

			if (in_array(str_replace('\\','/',$file), $ignored_files)) {
				continue;
			}

			$_ = array();
			require($file);

			$_ += $default_lang;

			$tpl   = str_replace($find, $replace, $file);

			if (!is_file($tpl)) {
				$tpl = str_replace('.tpl', '_form.tpl', $tpl);
				if (!is_file($tpl)) {
					echo "skipping $tpl... not found<BR>";
					continue;
				}
			}

			$lines = explode("\n", file_get_contents($tpl));

			$orig_lines = $lines;

			$count = 0;

			$new_lines = array();

			$missed = array();
			$ignored = array();

			$regx = "/\\\$(" . implode('|', array_keys($_)) . ')([^a-zA-Z0-9_])/';

			foreach ($lines as $num => $line) {
				$matches = null;
				if (preg_match_all($regx, $line, $matches)) {

					foreach ($matches[1] as $key => $match) {
						if (!is_string($_[$match]) || strpos($_[$match], '%') !== false) {
							$missed[$num+1] = $line;
						} else {
							$line = str_replace('$'.$match, '_l("' . addslashes($_[$match]) . '")', $line);
						}
					}
					$count++;
				}
				elseif (preg_match($missed_regx, $line)) {
					$ignored[$num+1] = $line;
				}

				$new_lines[] = $line;
			}

			if ($count > 0) {
				echo "modified $count lines in $tpl<br>";
				$this->print_lines($orig_lines, $new_lines, false, true);
				file_put_contents($tpl, implode("\n", $new_lines));
			}

			if (!empty($missed)) {
				html_dump($_,'langdata');
				echo "<BR><BR>MISSED " . count($missed) . " lines! $tpl<BR><BR>";
				foreach ($missed as $l => $miss) {
					$this->pl($l, $miss);
				}
				exit;
			}

			if (!empty($ignored)) {
				echo "<div style=\"color:red\">ignored " . count($ignored) . ' lines in ' . $tpl . "</div>";
			}
			else {
				echo "<div style=\"color:grey\">nothing to do $tpl</div>";
			}
		}
	}

	public function render_breadcrumbs($files)
	{
		foreach ($files as $file) {
			$lines = explode("\n", file_get_contents($file));

			$orig_lines = $lines;

			$count = 0;

			$bracket_count    = 0;
			$function_bracket = null;
			$has_bread        = false;

			$new_lines = array();

			foreach ($lines as $num => $line) {
				$bracket_count += substr_count($line, '{');
				$bracket_count -= substr_count($line, '}');

				$matches = null;

				if ($function_bracket && $bracket_count < current($function_bracket)) {
					$function_bracket = null;
					$has_bread        = false;
				} elseif (!$function_bracket && preg_match("/function\\s*([A-Za-z_]*)\\s*\(/", $line, $matches)) {
					$function_bracket = array($matches[1] => $bracket_count + 1);
				} elseif (!$has_bread && strpos($line, "\$this->breadcrumb->add")) {
					$has_bread = true;
				} elseif ($has_bread && strpos($line, "\$this->data['breadcrumbs'] = \$this->breadcrumb->render();\r")) {
					$has_bread = false;
				} elseif ($has_bread && strpos($line, "\$this->children")) {
					$count++;
					preg_match("/(\\s*)\\\$/", $line, $matches);

					$new_lines[] = $matches[1] . "\$this->data['breadcrumbs'] = \$this->breadcrumb->render();\r";
					$new_lines[] = $matches[1] . "\r";
				}

				$new_lines[] = $line;
			}

			if ($count > 0) {
				echo "modified $count lines in $file<br>";
			}

			//$this->print_lines($orig_lines, $new_lines, false, true);
			file_put_contents($file, implode("\n", $new_lines));
		}
	}

	public function smodel_call_update($files)
	{
		foreach ($files as $file) {
			$lines = explode("\n", file_get_contents($file));

			$orig_lines = $lines;

			$count = 0;

			foreach ($lines as $num => $line) {
				$matches = null;
				if (preg_match("/\\\$this->(model_[a-z_]*)/", $line, $matches)) {
					$parts  = explode('_', $matches[1]);
					$rename = ucfirst($parts[0]) . '_' . ucfirst($parts[1]) . '_' . ucfirst($parts[2]);

					if (isset($parts[3])) {
						$rename .= ucfirst($parts[3]);
					}

					$lines[$num] = str_replace($matches[1], $rename, $line);

					$count++;
				}
			}

			if ($count > 0) {
				echo "modified $count lines in $file<br>";
			}
			file_put_contents($file, implode("\n", $lines));
		}
	}

	public function class_name_convention($files)
	{
		foreach ($files as $file) {
			$lines = explode("\n", file_get_contents($file));

			$orig_lines = $lines;

			$dir_components = explode('/', str_replace(SITE_DIR, '', dirname($file)));

			if ($dir_components[0] == 'plugin') {
				array_shift($dir_components);
				while (!in_array(current($dir_components), array(
					"",
					"catalog",
					"admin",
					"system"
				))) {
					array_shift($dir_components);
				}
			}

			array_walk($dir_components, function (&$e, $index) {
				$e = ucfirst($e);
				$e = preg_replace_callback("/_l([a-z])/", function ($matches) { return strtoupper($matches[1]); }, $e);
			});

			$file_component = ucfirst(str_replace('.php', '', basename($file)));
			$file_component = preg_replace_callback("/_l([a-z])/", function ($matches) { return strtoupper($matches[1]); }, $file_component);

			$classname = implode('_', $dir_components) . '_' . $file_component;

			foreach ($lines as $num => $line) {
				$count = 0;

				$replace     = "class $classname";
				$lines[$num] = preg_replace("/class [A-Z0-9_\\\\\/]*/i", "class {$classname}", $line, 1, $count);

				if ($count) {
					echo "&lt;&lt;&lt;$line<br />&gt;&gt;&gt;$lines[$num]<br>";
					break;
				}
			}

			file_put_contents($file, implode("\n", $lines));
		}
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

					$to_add[end($brackets)] = '$this->template->load(' . $template . ');' . "\r\n";
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


	public function clean_php_line_endings($files)
	{
		foreach ($files as $file) {
			$lines = explode("\n", file_get_contents($file));

			$orig_lines = $lines;

			for ($i = count($lines) - 1; $i > max(0, count($lines) - 10); $i--) {
				if (preg_match("/[^\s]/", $lines[$i]) > 0) {
					//echo "checking " . htmlspecialchars($lines[$i]) . '<br>';
					if (preg_match("/\?>\s*$/", $lines[$i])) {
						unset($lines[$i]);
					} else {
						//echo 'skipping ' . htmlspecialchars($lines[$i]);
					}
					break;
				}
			}
			//$this->print_lines($orig_lines, $lines, true);
			file_put_contents($file, implode("\n", $lines));
		}
	}

	public function format_language_files($files)
	{
		echo 'Formatting Language Files';

		$files = array(SITE_DIR . 'catalog/language/english/product/test.php');
		foreach ($files as $file) {
			$lines     = explode("\n", file_get_contents($file));
			$new_lines = array();
			$max       = 0;
			foreach ($lines as $line) {
				if (preg_match('/\s*\$[^=]*=/', $line) == 0) {
					continue;
				}

				echo 'parsing line ' . $line . '<br>';
				list($entry, $text) = explode('=', $line, 2);
				html_dump($entry);
				html_dump($text);
				$entry       = str_replace(' ', '', $entry);
				$text        = trim($text);
				$max         = max($max, strlen($entry));
				$new_lines[] = array(
					'e' => $entry,
					't' => $text
				);
			}

			foreach ($new_lines as &$line) {
				$line = str_pad($line['e'], $max) . ' = ' . $line['t'];
			}
			file_put_contents($file, implode("\n", $new_lines));
		}
	}

	public function update_breadcrumb_format($files)
	{
		foreach ($files as $file) {
			$lines = explode("\n", file_get_contents($file));

			$flag_delete = false;
			$orig_lines  = $lines;

			$tricky = false;

			$block_started = false;

			$brace_count = 0;
			$r_href      = $r_text = null;

			foreach ($lines as $num => &$line) {
				if (preg_match("/[^\s]/", $line) == 0) {
					if ($block_started) {
						unset($lines[$num]);
					}
					continue;
				}

				if (!$block_started) {
					if (strpos($line, '=')) {
						list($var, $assign) = explode('=', $line, 2);

						if (preg_match('/\$this->data\[[\'"]breadcrumbs[\'"]\]/', $var) == 0) {
							continue;
						}

						if (preg_match('/\$this->data\[[\'"]breadcrumbs[\'"]\]\[\]/', $var) > 0) {
							echo "Tricky File $file at line $num<br><br>";
							$tricky = true;
							break;
						}

						if (preg_match('/\$this->tool->breadcrumbs/', $assign) > 0) {
							continue;
						}

						if (trim($assign) == 'array();') {
							unset($lines[$num]);
							$block_started = true;
						} else {
							echo "Unusual Assign Start of block: $file at line $num<br><br>";
							$tricky = true;
							break;
						}
					}

					continue;
				} else {
					if (strpos($line, '=')) {

						list($var, $assign) = explode('=', $line, 2);
						if ($brace_count == 0) {
							if (preg_match('/\$this->data\[[\'"]breadcrumbs[\'"]\]\[\]/', $var) == 0) {
								$line          = "\r\n" . $line;
								$block_started = false;
								continue;
							}

							if (trim($assign) == 'array(') {
								$brace_count++;
								unset($lines[$num]);
							} else {
								echo "Unusual Assign in block: $file at line $num<br><br>";
								$tricky = true;
								break;
							}
						} else {
							$var    = trim($var, " \t\n\r\0\x0B'");
							$assign = trim($assign, " \t\n\r\0\x0B,>");
							if ($var == 'text') {
								if ($r_text) {
									echo "OVERWRITING TEXT? $file line $num<br><br>";
									break;
								}
								$r_text = $assign;
							} elseif ($var == 'href') {
								if ($r_href) {
									echo "OVERWRITING HREF? $file line $num<br><br>";
									break;
								}
								$r_href = $assign;
							} elseif ($var != 'separator') {
								echo "Unusual attribute $file at line $num<br><br>";
								$tricky = true;
								break;
							}

							if ($r_text && $r_href) {
								$pre = null;
								preg_match("/^\s*/", $line, $pre);
								$lines[$num] = $pre[0] . '$this->breadcrumb->add(' . $r_text . ', ' . $r_href . ');';
								$r_text      = null;
								$r_href      = null;
							} else {
								unset($lines[$num]);
							}
						}
					} else {
						if (trim($line) == ');') {
							$brace_count--;
							unset($lines[$num]);
						} else {
							$line          = "\r\n" . $line;
							$block_started = false;
							continue;
						}
					}
				}
			}

			if (!$tricky) {
				file_put_contents($file, implode("\n", $lines));
			} else {
				$this->print_lines($orig_lines, $lines, true);
			}
		}
	}

	public function update_message_format($files)
	{
		foreach ($files as $file) {
			$lines = explode("\n", file_get_contents($file));

			$flag_delete = false;
			$orig_lines  = $lines;

			foreach ($lines as $num => &$line) {
				if (preg_match("/[^\s]/", $line) == 0) {
					continue;
				}

				$msg_types = array(
					'warning',
					'success',
					'notify',
					'attention'
				);

				foreach ($msg_types as $mt) {
					$r = preg_replace("/\\\$this->session->data\[['\"]" . $mt . "[\"']\]\s*=/", '$this->message->add(\'' . $mt . '\',', $line);
					if ($r) {
						if ($r != $line) {
							$pos = strrpos($r, ';');
							if ($pos !== false) {
								$r = substr_replace($r, ');', $pos, 1);
							} else {
								echo 'NO ENDING DELIMETER';
								exit;
							}
							$line = $r;
						}
					} else {
						echo "ERROR PREG";
						exit;
					}
				}
			}
			$this->print_lines($orig_lines, $lines, true);
			file_put_contents($file, implode("\n", $lines));
		}
	}

	public function update_url_links($files)
	{
		foreach ($files as $file) {
			$lines = explode("\n", file_get_contents($file));
			foreach ($lines as &$line) {
				if (strpos($line, '$this->url->link') !== false) {
					$patterns     = array(
						"/['\"]token=['\"]\s*\.\s*\\\$this->session->data\[['\"]token['\"]\][^a-zA-Z0-9]*,/",
						"/['\"]token=['\"]\s*\.\s*\\\$this->session->data\[['\"]token['\"]\]\s*\.\s*\\\$/",
						"/['\"]token=['\"]\s*\.\s*\\\$this->session->data\[['\"]token['\"]\]\s*\.\s*['\"]&/",
						"/['\"]token=['\"]\s*\.\s*\\\$token[^a-zA-Z0-9]*,/",
						"/['\"]token=['\"]\s*\.\s*\\\$token\s*\.\s*\\\$/",
						"/['\"]token=['\"]\s*\.\s*\\\$token\s*\.\s*['\"]&/",
						"/,\s*['\"]SSL['\"]\s*/i",
						"/,\s*['\"]['\"]\s*\)/",
					);
					$replacements = array(
						'\'\',',
						'$',
						'\'',
						'\'\',',
						'$',
						'\'',
						'',
						')'
					);

					$orig = $line;
					$line = preg_replace($patterns, $replacements, $line);
					if ($orig != $line) {
						echo "Changed $orig<br>$line<br><br>";
					}
				}
			}

			file_put_contents($file, implode("\n", $lines));
		}
	}

	public function get_all_files_r($dir, $ignore = array(), $ext = array('php'), $depth = 0)
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
				$files = array_merge($files, $this->get_all_files_r($file_path, $ignore, $ext, $depth + 1));
			} else {
				if (!empty($ext)) {
					$match = null;
					preg_match("/[^\.]*$/", $file, $match);

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
