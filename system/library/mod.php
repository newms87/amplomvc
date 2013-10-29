<?php
class Mod extends Library
{
	private $mod_registry;
	private $live_registry;

	private $error = array();

	function __construct($registry)
	{
		parent::__construct($registry);

		global $mod_registry, $live_registry;

		$this->mod_registry  = $mod_registry;
		$this->live_registry = $live_registry;

		$this->validate();
	}

	public function fetchErrors()
	{
		$error = $this->error;

		$this->error = null;

		return $error;
	}

	private function setError($file, $line, $code, $msg)
	{
		if ($file) {
			$msg = "Error in $file: Line $line:  " . htmlspecialchars($code) . ": " . $msg;
		}
		//trigger_error($msg);
		$this->error[] = $msg;
	}

	public function isRegistered($file)
	{
		foreach ($this->mod_registry as $source => $mods) {
			foreach ($mods as $destination => $mod_files) {
				foreach ($mod_files as $mod_file => $value) {
					if ($mod_file === $file) return true;
				}
			}
		}

		return false;
	}

	public function getSourceDestination($source)
	{
		return str_replace(SITE_DIR, DIR_MOD_FILES, $source);
	}

	public function addFile($source, $mod_file, $destination = null, $directives = array())
	{
		if (!is_file($source)) {
			if (is_file(SITE_DIR . $source)) {
				$source = SITE_DIR . $source;
			} else {
				$this->message->add('warning', "File $source was not found. Unable to add to file modification registry" . get_caller(0, 3));
				return false;
			}
		}

		if (!is_file($mod_file)) {
			if (is_file(SITE_DIR . $mod_file)) {
				$mod_file = SITE_DIR . $mod_file;
			} else {
				$this->message->add('warning', "Mod File $mod_file was not found. Unable to add to file modification registry" . get_caller(0, 3));
				return false;
			}
		}

		if (!isset($this->mod_registry[$source])) {
			$this->mod_registry[$source] = array();
		}

		if (!$destination || ($source === $destination)) {
			$destination = $this->getSourceDestination($source);
		}

		$this->mod_registry[$source][$destination][$mod_file] = $directives;
	}

	public function addFiles($source, $mod_files, $destination = null)
	{
		foreach ($mod_files as $source_file => $mod_file) {
			$this->addFile($source ? $source : $source_file, $mod_file, $destination);
		}
	}

	public function getModFiles($dir)
	{
		$files = array();

		foreach ($this->mod_registry as $source => $mods) {
			foreach ($mods as $destination => $mod_files) {
				foreach ($mod_files as $mod_file => $value) {
					if (strpos($mod_file, $dir) === 0) {
						$files[] = $mod_file;
					}
				}
			}
		}

		return $files;
	}

	public function removeFile($file)
	{
		foreach ($this->mod_registry as $source => $mods) {
			foreach ($mods as $destination => $mod_files) {
				foreach ($mod_files as $mod_file => $value) {
					//Allows for removing directories or similar files at once
					if (strpos($mod_file, $file) === 0) {
						unset($this->mod_registry[$source][$destination][$mod_file]);
					}
				}

				if (empty($this->mod_registry[$source][$destination])) {
					if ($source != $destination && is_file($destination)) {
						unlink($destination);
					}
					unset($this->mod_registry[$source][$destination]);
				}
			}

			if (empty($this->mod_registry[$source])) {
				unset($this->mod_registry[$source]);
			}
		}
	}

	public function removeDirectory($dir)
	{
		$this->removeFile($dir);
	}

	public function addModFile($mod_file)
	{
		$directives = $this->tool->getFileCommentDirectives($mod_file);

		if (isset($directives['skip'])) return true;

		if (!empty($directives['originalsource'])) {
			$source = SITE_DIR . trim($directives['source']);
		}
		elseif (!empty($directives['source'])) {
			$source = SITE_DIR . trim($directives['source']);

			if (!is_file($source)) {
				$this->error[] = "File Mod failed. The source file $source does not exist!" . get_caller(0, 4);
				return false;
			}
		} else {
			$this->error[] = "File Mod failed for $mod_file. You must specify a Source File in the PHPDoc Comment Directives. (eg: /** Source: relative/path/to/mysourcefile.php */)" . get_caller(0, 4);
			return false;
		}

		if (!empty($directives['destination'])) {
			$destination = SITE_DIR . trim($directives['destination']);
		} else {
			$this->error[] = "File Mod failed for $mod_file. You must specify a Destination File in the PHPDoc Comment Directives. (eg: /** Destination: relative/path/to/mydestinationfile.php */)" . get_caller(0, 4);
			return false;
		}

		$set_file_root = function(&$file) { $file = SITE_DIR . trim($file); };
		$file_filter = function($file) { return trim($file); };

		if (!empty($directives['require'])) {
			$directives['require'] = array_filter(explode("\n", $directives['require']), $file_filter);
			array_walk_recursive($directives['require'], $set_file_root);
		}

		if (!empty($directives['include'])) {
			$directives['include'] = array_filter(explode("\n", $directives['include']), $file_filter);
			array_walk_recursive($directives['include'], $set_file_root);
		}

		$this->addFile($source, $mod_file, $destination, $directives);
	}

	public function addModDirectory($dir)
	{
		$mod_files = $this->tool->get_files_r($dir, null, FILELIST_STRING);

		foreach ($mod_files as $mod_file) {
			$this->addModFile($mod_file);
		}

		if (!$this->error && $this->apply()) {
			$this->write();
		}

		return $this->error ? false : true;
	}

	public function apply($invalidated_only = false)
	{
		$this->live_registry = array();

		foreach ($this->mod_registry as $source => $mods) {
			foreach ($mods as $destination => $mod_files) {
				//Update Live Registry
				if ($this->getSourceDestination($source) === $destination) {
					$this->live_registry[$source] = $destination;
				}

				if ($invalidated_only) {
					if (!isset($this->invalid[$destination])) continue; //skip if no changes made

					$this->message->add('notify', _("Mod File $destination was updated"));
				}

				//Each mod file using the same destination will stack changes on the destination file
				$stack_source = $source;

				foreach ($mod_files as $mod_file => $extends) {
					$this->modifyFile($stack_source, $mod_file, $destination);

					//stack changes
					$stack_source = $destination;

					//Required File Mods
					if (!empty($extends['require'])) {
						foreach ($extends['require'] as $required_file) {
							$this->modifyFile($stack_source, $required_file, $destination);
						}
					}

					//Included File Mods
					if (!empty($extends['include'])) {
						foreach ($extends['include'] as $included_file) {
							if (is_file($included_file)) {
								$this->modifyFile($stack_source, $included_file, $destination);
							}
						}
					}
				}
			}
		}

		return $this->error ? false : true;
	}

	public function write()
	{
		chmod(AC_MOD_REGISTRY, 0777);

		$registries = array(
			'live' => $this->live_registry,
			'mod'  => $this->mod_registry,
		);

		if (!file_put_contents(AC_MOD_REGISTRY, serialize($registries))) {
			$this->error[] = "Failed to write to Mod Registry!";
		}

		chmod(AC_MOD_REGISTRY, 0444);

		return $this->error ? false : true;
	}

	public function modifyFile($source, $mod_file, $destination = null)
	{
		if (!is_file($mod_file)) {
			$this->error[] = "File merge failed. The mod file $mod_file does not exist!" . get_caller(0, 4);
			return false;
		}

		$directives = $this->tool->getFileCommentDirectives($mod_file);

		if (!$source) {
			$source = preg_replace("/(.*?)file_mods\\//", SITE_DIR, $mod_file);
		}

		if (!is_file($source) || $source == $mod_file) {
			$this->error[] = "File Mod failed. The source file $source does not exist!" . get_caller(0, 4);

			return false;
		}

		if (!$destination) {
			$destination = str_replace(SITE_DIR, DIR_MOD_FILES, $source);
		}

		$algorithm = !empty($directives['algorithm']) ? trim($directives['algorithm']) : 'fileMerge';

		switch ($algorithm) {
			case 'Ganon':
				$contents = $this->Ganon($source, $mod_file);
				break;
			case 'fileMerge':
				$contents = $this->fileMerge($source, $mod_file);
				break;
			default:
				$this->error[] = "File Merge Failed! Unknown algorithm: $algorithm." . get_caller(0, 4);
				return false;
				break;
		}

		if (!$contents) {
			$this->error[] = "File Merge Failed for $mod_file. Skipping this files changes.";
			return false;
		}

		_is_writable(dirname($destination));

		if (!is_file($destination)) {
			touch($destination);
		}

		chmod($destination, 0777);

		if (!file_put_contents($destination, $contents)) {
			$this->error[] = "Could not write to file $destination!" . get_caller(0, 4);
		}

		chmod($destination, AMPLOCART_FILE_MODE);

		return $this->error ? false : true;
	}

	private function Ganon($source, $mod_file)
	{
		ini_set('max_execution_time', 300); //300 seconds = 5 minutes

		require_once DIR_RESOURCES . 'ganon.php';

		if (!($node = file_get_dom($source))) {
			$this->message->add('warning', "There was an error while parsing the source file $source with Ganon!");

			return false;
		}

		require $mod_file;

		return $node->html();
	}

	private function fileMerge($source, $mod_file)
	{
		$comments  = array(
			'php'  => array(
				'#',
				''
			),
			'html' => array(
				"<? #",
				"?>"
			)
		);
		$comm_mode = false;

		// Include two sample files for comparison
		$original      = explode("\n", str_replace("\n\n", "\n", str_replace("\r", "\n", file_get_contents($source))));
		$modifications = explode("\n", str_replace("\n\n", "\n", str_replace("\r", "\n", file_get_contents($mod_file))));

		//this makes the filepaths safe for displaying
		$roots     = array(
			SITE_DIR,
			DIR_MOD_FILES
		);
		$source = str_replace($roots, array('','merged'), $source);

		$mod_file  = str_replace($roots, '', $mod_file);

		$orig_length = count($original);

		$new_file = array();

		$code_block = array();

		$add_before       = false;
		$add_before_block = array();

		$io_mode = '';
		$index   = 0;
		foreach ($modifications as $line => $mod) {
			$line++;
			if ($io_mode != 'add') {
				$mod = trim($mod);
			}

			//ignore comments and empty lines (ignore whitespace when we are not adding lines)
			if (strpos($mod, '#') === 0 || !$mod) {
				continue;
			}


			//End Of Block
			if (strpos($mod, '-----') !== false && strpos($mod, '-----') < 5) {
				if ($io_mode == 'seek' || $io_mode == 'remove') {
					$block = $this->findBlock($code_block, $original, $index);

					if ($block === false) {
						$this->setError(SITE_DIR . $mod_file, $line, $mod, "The code block starting at this line was not found in the source file: $source.!");
						return false;
					} else {
						list($block_start, $block_end) = $block;
						//echo "GOT $block_start to $block_end<br>";

						//keep all the code from the current index up until this code block
						for ($i = $index; $i < $block_start; $i++) {
							$new_file[] = $original[$i] . "\r\n";
						}

						if ($add_before) {
							$new_file         = array_merge($new_file, $add_before_block);
							$add_before       = false;
							$add_before_block = array();
						}

						//if we are seeking this block, keep the code in this block
						if ($io_mode == 'seek') {
							for ($i = $block_start; $i < $block_end; $i++) {
								$new_file[] = $original[$i] . "\r\n";
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
						if (strpos($mod, '{' . $comm . '}') > 0) {
							$comm_mode = $comm;
							break;
						}
					}

					if ($comm_mode) {
						$c = $comments[$comm_mode][0] . "END: Added From Plugin file $mod_file " . $comments[$comm_mode][1] . "\r\n";
						if ($add_before) {
							$add_before_block[] = $c;
						} else {
							$new_file[] = $c;
						}
					}
				} else {
					$this->setError($mod_file, $line, $mod, "Check your syntax. The IO Mode was not set at end of code block");
					return false;
				}

				$io_mode = '';

				continue;
			} //Remove Block Start
			elseif (strpos($mod, '<<<<<') !== false && strpos($mod, '<<<<<') < 5) {
				if ($io_mode) {
					$this->setError($mod_file, $line, $mod, "Check your syntax. The IO Mode was in $io_mode at start of Remove Code Block.");
					return false;
				}

				$io_mode = 'remove';

				continue;
			} elseif (strpos($mod, '>>>>>') !== false && strpos($mod, '>>>>>') < 5) {
				if ($io_mode) {
					$this->setError($mod_file, $line, $mod, "Check your syntax. The IO Mode was in $io_mode at start of Add Code Block.");
					return false;
				}

				$io_mode = 'add';

				//check if comments were specified and which mode
				$comm_mode = false;
				foreach (array_keys($comments) as $comm) {
					if (strpos($mod, '{' . $comm . '}') > 0) {
						$comm_mode = $comm;
						break;
					}
				}

				//check if adding before
				if (strpos($mod, '{before}') > 0) {
					$add_before       = true;
					$add_before_block = array();
				}

				if ($comm_mode) {
					$c = $comments[$comm_mode][0] . "START: Added From Plugin file $mod_file " . $comments[$comm_mode][1] . "\r\n";
					if ($add_before) {
						$add_before_block[] = $c;
					} else {
						$new_file[] = $c;
					}
				}
				continue;
			} elseif (strpos($mod, '=====') !== false && strpos($mod, '=====') < 5) {
				if ($io_mode) {
					$this->setError($mod_file, $line, $mod, "Check your syntax. The IO Mode was in $io_mode at start of Seek Code Block.");
					return false;
				}

				$io_mode = 'seek';

				continue;
			} elseif (strpos($mod, '.....') !== false && strpos($mod, '.....') < 5) {
				if ($io_mode != 'seek') {
					$this->setError($mod_file, $line, $mod, "Check your syntax. The IO Mode was in $io_mode at start of Seek Code Block.");
					return false;
				}

				//echo "wildcard ***** added to block<br>";
				$code_block[] = "*";

				continue;
			}


			if ($io_mode == 'add') {
				//echo 'adding line at ' . $index . ' - ' . htmlspecialchars($mod) . '\'<br>';
				if ($add_before) {
					$add_before_block[] = $mod . "\r\n";
				} else {
					$new_file[] = $mod . "\r\n";
				}
			} elseif ($io_mode == 'remove') {
				//echo 'removing block \'' . htmlspecialchars($mod) . '\'<br>';

				$code_block[] = $mod;
			} elseif ($io_mode == 'seek') {
				//echo 'seeking block \'' . htmlspecialchars($mod) . '\'<br>';

				$code_block[] = $mod;
			} elseif ($mod && !preg_match('/\/\/ignore/i', $mod)) {
				$this->setError($mod_file, $line, $mod, "The IO Mode was not set before this line!");
				return false;
			}
		}

		if ($io_mode) {
			$this->setError($mod_file, count($mod_file), '', "The IO Mode was still set at the end of the file! Be sure to end all blocks of code with '-----'");
			return false;
		}

		//we need to fix the end lines for original file
		for ($i = $index; $i < $orig_length; $i++) {
			$original[$i] .= "\r\n";
		}

		return array_merge($new_file, array_slice($original, $index));
	}

	private function findBlock($block, $file, $start_index)
	{
		if (!count($block) || (count($block) > (count($file) - $start_index))) {
			return false;
		}

		$file_length = count($file);

		//echo "<br><br>SEEKING BLOCK FROM $start_index<br>";
		for ($i = $start_index; $i < $file_length; $i++) {
			//echo htmlspecialchars(trim($file[$i])) . "  === compare === " . htmlspecialchars($block[0]).'<br>';;
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

	private function validate()
	{
		$this->invalid = array();

		foreach ($this->mod_registry as $source => $mods) {
			if (!is_file($source)) {
				unset($this->mod_registry[$source]);
				$this->invalid['source'] = true; //Nothing to apply, only write
				continue;
			}

			$source_filemtime = filemtime($source);

			foreach ($mods as $destination => $mod_files) {
				if (!is_file($destination) || (($dest_filemtime = filemtime($destination)) < $source_filemtime)) {
					$this->invalid[$destination] = true;
				}

				foreach ($mod_files as $mod_file => $value) {
					if (!is_file($mod_file)) {
						$this->removeFile($mod_file);
						$this->invalid[$destination] = true;
					} elseif (filemtime($mod_file) > $dest_filemtime) {
						$this->invalid[$destination] = true;
					}
				}
			}
		}

		if ($this->invalid) {
			if ($this->apply(true)) {
				$this->write();
				$this->message->add('notify', "The Mod File Registry was out of date and has been updated");
			} else {
				//We cannot use url library here because it has not been loaded yet.
				$plugin_url = HTTP_ADMIN . 'extension/plugin';
				$this->message->add('warning', $this->fetchErrors());
				$this->message->add('warning', 'Please visit the <a href="' . $plugin_url . '">Plugins</a> and resolve the issue.');
			}
		}
	}
}
