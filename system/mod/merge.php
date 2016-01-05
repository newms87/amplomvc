<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC
 * @link http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt files in the root directory.
 */

class System_Mod_Merge extends System_Mod_Mod
{
	public function apply($source, $mod_file, $file_type, $meta)
	{
		$default_comm_mode = $file_type;

		$comments  = array(
			'php'  => array(
				'#',
				''
			),
			'html' => array(
				"<?php #",
				"?>"
			),
			'tpl' => array(
				"<?php #",
				"?>"
			),
			'css' => array(
				"/*",
				"*/"
			),
			'less' => array(
				"//",
				"",
			),
		);

		$comm_mode = $default_comm_mode;

		// Include two sample files for comparison
		$original      = explode("\n", str_replace("\r", "\n", str_replace("\r\n", "\n", file_get_contents($source))));
		$modifications = explode("\n", str_replace("\r", "\n", str_replace("\r\n", "\n", file_get_contents($mod_file))));

		$mod_path = str_replace(DIR_SITE, '', $mod_file);

		$new_file = array();

		$code_block = array();

		$add_before       = false;
		$add_before_block = array();

		$io_mode = '';
		$index   = 0;
		foreach ($modifications as $line => $m) {
			$line++;
			if ($io_mode != 'add') {
				$m = trim($m);
			}

			//ignore comments and empty lines (ignore whitespace when we are not adding lines)
			if (strpos($m, '#') === 0 || !$m) {
				continue;
			}


			//End Of Block
			if (strpos($m, '-----') !== false && strpos($m, '-----') < 5) {
				if ($io_mode == 'seek' || $io_mode == 'remove') {
					$block = $this->findBlock($code_block, $original, $index);

					if ($block === false) {
						$this->setError($mod_file, $line, $m, "The code block starting at this line was not found in the source file: $source.!");
						return false;
					} else {
						list($block_start, $block_end) = $block;
						//echo "GOT $block_start to $block_end<br>";

						//keep all the code from the current index up until this code block
						for ($i = $index; $i < $block_start; $i++) {
							$new_file[] = $original[$i];
						}

						if ($add_before) {
							$new_file         = array_merge($new_file, $add_before_block);
							$add_before       = false;
							$add_before_block = array();
						}

						//if we are seeking this block, keep the code in this block
						if ($io_mode == 'seek') {
							for ($i = $block_start; $i < $block_end; $i++) {
								$new_file[] = $original[$i];
							}
						}

						//set the index to the end of the code block
						$index = $block_end;

						$code_block = array();
					}
				} elseif ($io_mode == 'add') {
					//check if comments were specified and which mode
					//(we do this at the end of block in case we need to switch comment modes)
					foreach (array_keys($comments) as $comm) {
						if (strpos($m, '{' . $comm . '}') > 0) {
							$comm_mode = $comm;
							break;
						}
					}

					if ($comm_mode) {
						$c = $comments[$comm_mode][0] . "END: Mod by $mod_path " . $comments[$comm_mode][1];
						if ($add_before) {
							$add_before_block[] = $c;
						} else {
							$new_file[] = $c;
						}
					}
				} else {
					$this->setError($mod_file, $line, $m, "Check your syntax. The IO Mode was not set at end of code block");
					return false;
				}

				$io_mode = '';

				continue;
			} //Remove Block Start
			elseif (strpos($m, '<<<<<') !== false && strpos($m, '<<<<<') < 5) {
				if ($io_mode) {
					$this->setError($mod_file, $line, $m, "Check your syntax. The IO Mode was in $io_mode at start of Remove Code Block.");
					return false;
				}

				$io_mode = 'remove';

				continue;
			} elseif (strpos($m, '>>>>>') !== false && strpos($m, '>>>>>') < 5) {
				if ($io_mode) {
					$this->setError($mod_file, $line, $m, "Check your syntax. The IO Mode was in $io_mode at start of Add Code Block.");
					return false;
				}

				$io_mode = 'add';

				//check if comments were specified and which mode
				$comm_mode = $default_comm_mode;
				foreach (array_keys($comments) as $comm) {
					if (strpos($m, '{' . $comm . '}') > 0) {
						$comm_mode = $comm;
						break;
					}
				}

				//check if adding before
				if (strpos($m, '{before}') > 0) {
					$add_before       = true;
					$add_before_block = array();
				}

				if ($comm_mode) {
					$c = $comments[$comm_mode][0] . "START: Mod by $mod_path " . $comments[$comm_mode][1];
					if ($add_before) {
						$add_before_block[] = $c;
					} else {
						$new_file[] = $c;
					}
				}
				continue;
			} elseif (strpos($m, '=====') !== false && strpos($m, '=====') < 5) {
				if ($io_mode) {
					$this->setError($mod_file, $line, $m, "Check your syntax. The IO Mode was in $io_mode at start of Seek Code Block.");
					return false;
				}

				$io_mode = 'seek';

				continue;
			} elseif (strpos($m, '.....') !== false && strpos($m, '.....') < 5) {
				if ($io_mode != 'seek') {
					$this->setError($mod_file, $line, $m, "Check your syntax. The IO Mode was in $io_mode at start of Seek Code Block.");
					return false;
				}

				//echo "wildcard ***** added to block<br>";
				$code_block[] = "*";

				continue;
			}


			if ($io_mode == 'add') {
				//echo 'adding line at ' . $index . ' - ' . htmlspecialchars($mod) . '\'<br>';
				if ($add_before) {
					$add_before_block[] = $m;
				} else {
					$new_file[] = $m;
				}
			} elseif ($io_mode == 'remove') {
				//echo 'removing block \'' . htmlspecialchars($mod) . '\'<br>';

				$code_block[] = $m;
			} elseif ($io_mode == 'seek') {
				//echo 'seeking block \'' . htmlspecialchars($mod) . '\'<br>';

				$code_block[] = $m;
			} elseif ($m && !preg_match('/\/\/ignore/i', $m)) {
				$this->setError($mod_file, $line, $m, "The IO Mode was not set before this line!");
				return false;
			}
		}

		if ($io_mode) {
			$this->setError($mod_file, count($m), '', "The IO Mode was still set at the end of the file! Be sure to end all blocks of code with '-----'");
			return false;
		}

		return implode("\n", array_merge($new_file, array_slice($original, $index)));
	}

	protected function findBlock($block, $file, $start_index)
	{
		if (!count($block) || (count($block) > (count($file) - $start_index))) {
			return false;
		}

		$file_length = count($file);

		//echo "<br><br>SEEKING BLOCK FROM $start_index<br>";
		for ($i = $start_index; $i < $file_length; $i++) {
			//echo htmlspecialchars(trim($file[$i])) . "  === compare === " . htmlspecialchars($block[0]).'<br>';
			if (trim($file[$i]) == $block[0]) {
				//echo "START: " . htmlspecialchars($block[0]) . "<br>";
				$f_index = $i;
				$mode    = '';
				foreach ($block as $b) {
					if (!$b) {
						continue;
					}
					while (!trim($file[$f_index])) {
						$f_index++;
						if ($f_index >= $file_length) {
							//echo "EOF<BR>";
							return false;
						}
					}

					//echo "BLOCK: " . htmlspecialchars($b) . " ======== " . htmlspecialchars($file[$f_index]) . "<br>";
					//if we find a * we are in skip mode
					if ($b == '*') {
						$mode = 'skip';
						//echo "ENTERING SKIP MODE<BR>";
						continue;
					}

					//in skip mode we continue in the file until we find a match or eof
					if ($mode == 'skip') {
						while (trim($file[$f_index]) != $b) {
							//echo "SKIPPING " . htmlspecialchars($file[$f_index]) . "<br>";
							$f_index++;
							if ($f_index >= $file_length) {
								//echo "EOF<BR>";
								return false;
							}
						}
						$mode = '';
						$f_index++;
					} //in regular mode, if the line does not match, this is not the correct file index
					elseif (trim($file[$f_index]) != $b && trim($file[$f_index])) {
						//echo htmlspecialchars($file[$f_index]) . " !!=== " . htmlspecialchars($b) . "<br>";
						$f_index = false;
						break;
					} //we have a match
					else {
						$f_index++;
						if ($f_index >= $file_length) {
							//echo "EOF<BR>";
							return false;
						}
					}
				}
				if ($f_index !== false) {
					//echo "FOUND BLOCK AT $i ENDS $f_index<BR><BR>";
					return array(
						$i,
						$f_index
					);
				}
			}
		}

		return false;
	}

	protected function setError($file, $line, $code, $msg)
	{
		if ($file) {
			$msg = "Error in $file: Line $line:  " . htmlspecialchars($code) . ": " . $msg;
		}
		//trigger_error($msg);
		$this->error[] = $msg;
	}
}
