<?php
class FileMerge 
{
	private $registry;
	private $merge_registry;
	private $error_msg = '';
	//This tracks plugins that have issues and have been uninstalled while applying registry changes
	private $uninstall = array();
	
	function __construct($registry)
	{
		global $merge_registry;
		$this->merge_registry = $merge_registry;
		
		$this->registry = $registry;
		
		$this->loadMergeRegistry();
		
		$this->validateMergeRegistry();
	}
	
	public function __get($key)
	{
		return $this->registry->get($key);
	}
	
	public function getError()
	{
		return $this->error_msg;
	}
	
	private function setError($file, $line, $code, $msg)
	{
		if ($file) {
			$msg = "Error in $file: Line $line:  ". htmlspecialchars($code) . ": " . $msg;
		}
		//trigger_error($msg);
		$this->error_msg[] = $msg;
	}
	
	public function getFile($file)
	{
		if (isset($this->merge_registry[$file])) {
			return str_replace(SITE_DIR, DIR_MERGED_FILES, $file);
		}
		
		return $file;
	}
	
	public function addFile($file, $name, $mod_path)
	{
		if (!is_file(SITE_DIR . $file)) {
			$msg = "File " . SITE_DIR . $file . " was not found. Unable to add to file modification registry";
			$this->message->add('warning', $msg);
			trigger_error($msg);
			
			return false;
		}
	
		if (!is_file(DIR_PLUGIN . $name . '/' . $mod_path)) {
			$msg = "Mod File " . DIR_PLUGIN . $name . '/' . $mod_path . " was not found. Unable to add to file modification registry";
			$this->message->add('warning', $msg);
			trigger_error($msg);
			
			return false;
		}
		
		$this->merge_registry[SITE_DIR . $file][$name] = $mod_path;
	}
	
	public function addFiles($name, $files)
	{		
		foreach ($files as $file_path => $mod_path) {
			$this->addFile($file_path, $name, $mod_path);
		}
	}
	
	public function removeFile($file, $name)
	{
		unset($this->merge_registry[SITE_DIR . $file][$name]);
	}
	
	public function loadMergeRegistry()
	{
		$this->merge_registry = array();
		
		$registry = $this->db->query("SELECT * FROM " . DB_PREFIX . "plugin_file_modification");
		
		foreach ($registry->rows as $row) {
			$this->merge_registry[SITE_DIR . $row['original_file']][$row['name']] = $row['mod_path'];
		}
	}
	
	private function writeMergeRegistry()
	{
		$this->db->query("TRUNCATE " . DB_PREFIX . "plugin_file_modification");
		
		$new_registry = '';
		
		foreach ($this->merge_registry as $filename=>$names) {
			$file = str_replace(SITE_DIR,'',$filename);
			
			foreach ($names as $name=>$mod_path) {
				$data = array(
					'name'			=> $name,
					'original_file' => $file,
					'mod_path'		=> $mod_path
				);
				
				$this->db->query("INSERT INTO " . DB_PREFIX . "plugin_file_modification SET `name` = '$name', `original_file` = '$file', `mod_path` = '$mod_path'");
				
				$new_registry .= $file . ',' . $name . ',' . $mod_path . "\n";
			}
		}
		
		$reg_file = DIR_MERGED_FILES . 'registry.txt';
		chmod($reg_file, 0777);
		$success = file_put_contents($reg_file, $new_registry);
		chmod($reg_file, 0444);
		
		if ($success === false) {
			$this->message->add('warning', "Failed to write to $reg_file");
		}
		
		return $success !== false;
	}
	
	public function syncRegistryWithDb()
	{
		$this->loadMergeRegistry();
 
		if (!$this->applyMergeRegistry()) {
			$msg = "Error: There was a problem remerging the file modifications. This could cause system instability. Please try to uninstall and reinstall the plugins!";
			trigger_error($msg);
			return false;
		}
		else {
			$this->writeMergeRegistry();
			return true;
		}
	}
	
	//TODO: Plugins should not be restricted to 1 Mod file per 1 original file
	// This way we can install different features on demand for plugins
	public function applyMergeRegistry()
	{
		$return = true;
		
		foreach ($this->merge_registry as $file_path => $names) {
			$working_file = $file_path;
			$merged_file = str_replace(SITE_DIR, DIR_MERGED_FILES, $file_path);
			
			foreach ($names as $name => $mod_path) {
				if (isset($this->uninstall[$name])) {
					unset($this->merge_registry[$file_path][$name]);
					continue;
				}
				
				$mod_file = DIR_PLUGIN . $name . '/' . $mod_path;
				
				if ($this->mergeFiles($working_file, $mod_file, $merged_file)) {
					$working_file = $merged_file;
				} else {
					$this->message->add('warning', $this->getError());
					$return = false;
					
					$this->uninstall[$name] = true;
					unset($this->merge_registry[$file_path][$name]);
					//$this->plugin->uninstall($name);
				}
			}
		}

		if (!$this->writeMergeRegistry()) {
			return false;
		}
		
		$this->cleanMergedDirectory();
		
		return $return;
	}
	
	public function cleanMergedDirectory()
	{
		$files = $this->tool->get_files_r(DIR_MERGED_FILES, array('php', 'tpl'));
		
		$merged_files = array();
		foreach (array_keys($this->merge_registry) as $m_file) {
			$merged_files[] = strtolower(str_replace(SITE_DIR, DIR_MERGED_FILES, $m_file));
		}
		
		foreach ($files as $file) {
			if (!in_array(strtolower(str_replace('\\', '/', $file)), $merged_files)) {
				if (is_file($file)) {
					unlink($file);
					
					$dir = dirname($file);
					
					while (strpos($dir, SITE_DIR) === 0 && is_dir($dir) && count(scandir($dir)) <= 2) {
						if (!rmdir($dir)) {
							break;
						}
						$dir = dirname($dir);
					}
				}
			}
		}
	}
	
	private function validateMergeRegistry()
	{
		$valid = true;
		foreach ($this->merge_registry as $file_path => $names) {
			if (!is_file($file_path)) {
				$valid = false;
				unset($this->merge_registry[$file_path]);
				continue;
			}
			
			$merged_file = str_replace(SITE_DIR, DIR_MERGED_FILES, $file_path);
			
			if (!is_file($merged_file)) {
				$valid = false;
				continue;
			}
			
			if (filemtime($file_path) > filemtime($merged_file)) {
				if ($this->config->get('config_debug')) {
					$this->message->add('notify', "The merged file was out of date with the file $file_path. It has been updated");
				}
				
				$valid = false;
			}
			
			foreach ($names as $name => $mod_path) {
				$plugin_file = DIR_PLUGIN . $name . '/' . $mod_path;
				
				if (!is_file($plugin_file)) {
					unset($this->merge_registry[$file_path][$name]);
					
					$msg = "The $name plugin is missing the file $plugin_file! This may cause system instability. The plugin $name has been uninstalled.";
					$this->plugin->uninstall($name);
					$this->message->add('warning', $msg);
					trigger_error($msg);
					
					$valid = false;
					
					continue;
				}
				
				if (!is_file($plugin_file) || filemtime($plugin_file) > filemtime($merged_file)) {
					if ($this->config->get('config_debug')) {
						$this->message->add('notify', "The merged file was out of date with the file $plugin_file. It has been updated");
					}
					$valid = false;
				}
			}
			
		}
		
		if (!$valid) {
			if ($this->applyMergeRegistry()) {
				$this->url->reload_page();
			}
			else {
				$msg = 'There was a problem validating the plugin merge file registry. The problem could not be fixed! Please validate the plugins!';
				trigger_error($msg);
				$this->message->add('warning', $msg);
			}
		}
	}

	public function mergeFiles($orig_file, $mod_file, $new_file_path)
	{
		if (!file_exists($orig_file)) {
			$this->setError('','','',"Plugin file modification failed: $orig_file does not exist!");
			return false;
		} elseif (!file_exists($mod_file)) {
			$this->setError('','','',"Plugin file modification failed: $mod_file does not exist!");
			return false;
		}
		
		$comments = array('php'=>array('#',''), 'html'=>array("<? #", "?>"));
		$comm_mode = false;
		
		// Include two sample files for comparison
		$original = explode("\n", str_replace("\n\n","\n",str_replace("\r","\n",file_get_contents($orig_file))));
		$modifications = explode("\n", str_replace("\n\n","\n",str_replace("\r","\n",file_get_contents($mod_file))));
		
		//this makes the filepaths safe for displaying
		$roots = array(SITE_DIR, DIR_MERGED_FILES);
		$orig_file = str_replace($roots, array('','merged'), $orig_file);
		$mod_file = str_replace($roots, '', $mod_file);
		
		$orig_length = count($original);
		
		$new_file = array();
		
		$code_block = array();
		
		$add_before = false;
		$add_before_block = array();
		
		$io_mode = '';
		$index = 0;
		foreach ($modifications as $line=>$mod) {
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
						$this->setError(SITE_DIR . $mod_file, $line, $mod, "The code block starting at this line was not found in the original file: " . SITE_DIR . "$orig_file.!");
						return false;
					}
					else {
						list($block_start, $block_end) = $block;
						//echo "GOT $block_start to $block_end<br>";
						
						//keep all the code from the current index up until this code block
						for($i=$index; $i<$block_start; $i++){
							$new_file[] = $original[$i] . "\r\n";
						}
						
						if ($add_before) {
							$new_file = array_merge($new_file, $add_before_block);
							$add_before = false;
							$add_before_block = array();
						}
						
						//if we are seeking this block, keep the code in this block
						if ($io_mode == 'seek') {
							for($i=$block_start; $i<$block_end;$i++){
								$new_file[] = $original[$i] . "\r\n";
							}
						}
						
						//set the index to the end of the code block
						$index = $block_end;
						
						$code_block = array();
					}
				}
				elseif ($io_mode == 'add') {
					//check if comments were specified and which mode
					//(we do this at the end of block in case we need to switch comment modes)
					foreach (array_keys($comments) as $comm) {
						if(strpos($mod, '{' . $comm . '}') > 0){
							$comm_mode = $comm;
							break;
						}
					}
					
					if ($comm_mode) {
						$c = $comments[$comm_mode][0] . "END: Added From Plugin file $mod_file " . $comments[$comm_mode][1] . "\r\n";
						if ($add_before) {
							$add_before_block[] = $c;
						}
						else {
						$new_file[] = $c;
						}
					}
				}
				else {
					$this->setError($mod_file, $line, $mod, "Check your syntax. The IO Mode was not set at end of code block");
					return false;
				}
				
				$io_mode = '';
			
				continue;
			}
			//Remove Block Start
			elseif (strpos($mod, '<<<<<') !== false && strpos($mod, '<<<<<') < 5) {
				if ($io_mode) {
					$this->setError($mod_file, $line, $mod, "Check your syntax. The IO Mode was in $io_mode at start of Remove Code Block.");
					return false;
				}
				
				$io_mode = 'remove';
				
				continue;
			}
			elseif (strpos($mod, '>>>>>') !== false && strpos($mod, '>>>>>') < 5) {
				if ($io_mode) {
					$this->setError($mod_file, $line, $mod, "Check your syntax. The IO Mode was in $io_mode at start of Add Code Block.");
					return false;
				}
				
				$io_mode = 'add';
				
				//check if comments were specified and which mode
				$comm_mode = false;
				foreach (array_keys($comments) as $comm) {
					if(strpos($mod, '{' . $comm . '}') > 0){
						$comm_mode = $comm;
						break;
					}
				}
				
				//check if adding before
				if(strpos($mod, '{before}') > 0){
					$add_before = true;
					$add_before_block = array();
				}
				
				if ($comm_mode) {
					$c = $comments[$comm_mode][0] . "START: Added From Plugin file $mod_file " . $comments[$comm_mode][1] . "\r\n";
					if ($add_before) {
						$add_before_block[] = $c;
					}
					else {
					$new_file[] = $c;
					}
				}
				continue;
			}
			elseif (strpos($mod, '=====') !== false && strpos($mod, '=====') < 5) {
				if ($io_mode) {
					$this->setError($mod_file, $line, $mod, "Check your syntax. The IO Mode was in $io_mode at start of Seek Code Block.");
					return false;
				}
				
				$io_mode = 'seek';
				
				continue;
			}
			elseif (strpos($mod, '.....') !== false && strpos($mod, '.....') < 5) {
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
				}
				else {
					$new_file[] = $mod . "\r\n";
				}
			}
			elseif ($io_mode == 'remove') {
				//echo 'removing block \'' . htmlspecialchars($mod) . '\'<br>';
				
				$code_block[] = $mod;
			}
			elseif ($io_mode == 'seek') {
				//echo 'seeking block \'' . htmlspecialchars($mod) . '\'<br>';
				
				$code_block[] = $mod;
			}
			elseif ($mod && !preg_match('/\/\/ignore/i',$mod)) {
				$this->setError($mod_file, $line, $mod, "The IO Mode was not set before this line!");
				return false;
			}
		}
		
		if ($io_mode) {
			$this->setError($mod_file, count($mod_file), '', "The IO Mode was still set at the end of the file! Be sure to end all blocks of code with '-----'");
			return false;
		}
		
		//we need to fix the end lines for original file
		for($i=$index; $i< $orig_length; $i++){
			$original[$i] .= "\r\n";
		}
		
		$new_file = array_merge($new_file, array_slice($original, $index));
		
		if (!is_dir(dirname($new_file_path))) {
			$mode = octdec($this->config->get('config_plugin_dir_mode'));
			mkdir(dirname($new_file_path), $mode, true);
			chmod(dirname($new_file_path), $mode);
		}
		if (!is_file($new_file_path)) {
			$mode = octdec($this->config->get('config_plugin_file_mode'));
			touch($new_file_path);
			chmod($new_file_path, $mode);
		}
		
		if ( file_put_contents($new_file_path, $new_file) ) {
			return true;
		}
		else {
			$this->setError($new_file_path, 0, '', "Could not write to file!");
			return false;
		}
	}

	private function findBlock($block, $file, $start_index)
	{
		if(!count($block) || (count($block) > (count($file) - $start_index))) return false;
		
		$file_length = count($file);
		
		//echo "<br><br>SEEKING BLOCK FROM $start_index<br>";
		for($i=$start_index; $i<$file_length; $i++){
			//echo htmlspecialchars(trim($file[$i])) . "  === compare === " . htmlspecialchars($block[0]).'<br>';;
			if (trim($file[$i]) == $block[0]) {
				//echo "START: " . htmlspecialchars($block[0]) . "<br>";
				$f_index = $i;
				$mode = '';
				foreach ($block as $b) {
					if(!$b)continue;
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
					}
					//in regular mode, if the line does not match, this is not the correct file index
					elseif (trim($file[$f_index]) != $b && trim($file[$f_index])) {
						//echo htmlspecialchars($file[$f_index]) . " !!=== " . htmlspecialchars($b) . "<br>";
						$f_index = false;
						break;
					}
					//we have a match
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
					return array($i, $f_index);
				}
			}
		}
		
		return false;
	}
}