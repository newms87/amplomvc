<?php
class Plugin{
	private $registry;
	private $plugins;
	private $plugin_registry;
	private $file_merge;
	
	function __construct($registry)
	{
		$this->registry = $registry;
		
		$this->registry->set('plugin', $this);
		
		$this->language->system('plugin');
		
		$this->loadPluginFileRegistry();
		
		if (!$this->config->get('config_development_mode')) {
			if (isset($_GET['set_development_mode'])) {
				if ($_GET['set_development_mode']) {
					//activate development mode for 24 hours
					$this->session->set_cookie('development_mode', '1', 3600 * 24);
				
					$_GET['set_development_mode'] = 0;
					$dev_url = $this->url->link($_GET['route'], $this->url->get_query());
				
					$this->message->add('warning', "Warning! Development Mode has been actived for 24 hours for this user. Click to <a href='$dev_url'>deactivate.</a>");
				}
				else {
					$this->session->delete_cookie('development_mode');
					
					$this->message->add('notify', "Development Mode has been deactivated.");
				}
				
				$this->cleanReload();
			}
			
			if (!empty($_COOKIE['development_mode'])) {
				$this->config->set('config_development_mode', 1);
			}
		}
		
		$this->file_merge = new FileMerge($this->registry);
	}
	
	public function __get($key)
	{
		return $this->registry->get($key);
	}
	
	public function getFile($file)
	{
		if (isset($this->plugin_registry[$file])) {
			$this->syncPluginFileWithLive($this->plugin_registry[$file]);
		}
		
		return $this->file_merge->getFile($file);
	}
	
	public function loadPlugin($name)
	{
		if (!isset($this->plugins[$name])) {
			$setup_file = DIR_PLUGIN . $name . '/setup.php';
			
			if (!is_file($setup_file)) {
				$this->message->add("warning", $this->language->format('error_setup_file', $setup_file, $name));
				
				return false;
			}
			
			_require(DIR_SYSTEM . 'plugins/plugin_setup.php');
			_require($setup_file);
			
			$user_class = preg_replace("/[^A-Z0-9]/i", "",$name) . '_Setup';
			
			$this->plugins[$name] = new $user_class($this->registry);
		}
		
		return $this->plugins[$name];
	}
	
	/**
	 * Install a plugin
	 * 
	 * @param $name - The name of the directory for the plugin to install
	 * @return Boolean - true on success, or false on failure (error message will be set)
	 */
	
	public function install($name)
	{
		$this->cache->delete('plugin');
		$this->cache->delete('model');
		
		$plugin = $this->loadPlugin($name);
		
		if (method_exists($plugin, 'install')) {
			$plugin->install();
		}
	
		$this->System_Model_Plugin->install($name);
		
		//New Files
		if (!$this->integrateNewFiles($name)) {
			$this->message->add("warning", $this->language->format("error_integrate_new_files", $name));
			$this->uninstall($name);
			return false;
		}
		
		//File Modifications
		$file_mods = $this->getFileMods($name);
		
		if ($file_mods === false) {
			$this->message->add("warning", $this->language->format('error_file_mod', $name));
			$this->uninstall($name);
			return false;
		}
		
		$this->file_merge->addFiles($name, $file_mods);
		
		if (!$this->file_merge->applyMergeRegistry()) {
			$this->message->add('warning', $this->language->format('error_install_merge', $name));
			$this->uninstall($name);
			return false;
		}

		$this->message->add('success', $this->language->format("success_install",$name));
		
		return true;
	}
	
	public function uninstall($name, $keep_data = true)
	{
		$this->cache->delete('plugin');
		$this->cache->delete('model');
		
		$plugin = $this->loadPlugin($name);
		
		$data = null;
		
		if (method_exists($plugin, 'uninstall')) {
			$data = $plugin->uninstall($keep_data);
		}
		
		//Uninstall the plugin from the system
		$this->System_Model_Plugin->uninstall($name, $data);
		
		//Reload the merge registry
		if (!$this->file_merge->syncRegistryWithDb()) {
			$this->message->add('warning', $this->language->format('error_uninstall_merge', $name));
			return false;
		}
		
		$this->message->add('notify', $this->language->format('success_uninstall', $name));
		
		return true;
	}

	public function integrateNewFiles($name)
	{
		$dir = DIR_PLUGIN . $name . '/new_files/';
		
		$file_types = array('php', 'tpl', 'js', 'css', 'png', 'jpg', 'jpeg', 'gif');
		
		$files = $this->tool->get_files_r($dir, $file_types);
		
		foreach ($files as $file) {
			if (!$this->activatePluginFile($name, $file)) {
				return false;
			}
		}
		
		return true;
	}
	
	public function activatePluginFile($name, $file)
	{
		$dir = DIR_PLUGIN . $name . '/new_files/';
		
		$plugin_file = preg_replace("/\\\\/", "/", $file->getPathName());
		$live_file = str_replace($dir, SITE_DIR, $plugin_file);
		
		if (strpos($live_file, '@template')) {
			$live_file = str_replace('@template', 'default', $live_file);
		}
		
		//Live file already exists! This is a possible conlflict...
		if (is_file($live_file)) {
			//The admin has requested to overwrite the live file
			if (!empty($_GET['overwrite_file']) && $_GET['overwrite_file'] === $live_file) {
				//continue to overwrite file...
			}
			//A possible conflict, lets resolve it!
			else {
				$overwrite_file_url = $this->url->link($_GET['route'], $this->url->get_query() . "&name=$name&overwrite_file=" . urlencode($live_file));
				
				//This is a registered plugin file, so we can update this safely
				if (isset($this->plugin_registry[$live_file])) {
					$reg = $this->plugin_registry[$live_file];
					
					if (filemtime($reg['live_file']) > (int)$reg['live_file_modified']) {
						$edit_date = $this->tool->format_datetime(filemtime($reg['live_file']));
						
						$msg = 
							"The Live file $live_file for the plugin <strong>$name</strong> could not be updated because it was edited on $edit_date!" .
							" Either manually remove the live file, or <a href='$overwrite_file_url'>overwrite</a> these changes with contents from the plugin file $plugin_file.";
							
						$this->message->add("warning", $msg);
						
						return false;
					}
				}
				//This file was here first! Ask admin what to do about this file.
				else {
					$msg = 
						"Unable to integrate the file $plugin_file for the plugin <strong>$name</strong> because the file $live_file already exists!" .
						" Either manually remove the file or <a href='$overwrite_file_url'>overwrite</a> this file with the plugin file.";
						
					$this->message->add("warning", $msg);
					return false;
				}
			}
		}
		
		//Generate the live file with the contents of the plugin file
		$copy_dir = dirname($live_file);
		
		if (!is_dir($copy_dir)) {
			mkdir($copy_dir, 0777, true);
		}
		else {
			$mode = octdec($this->config->get('config_default_dir_mode'));
			chmod($copy_dir, $mode);
		}
		
		if (!copy($file->getPathName(), $live_file)) {
			$this->message->add("warning", "There was an error while copying $plugin_file to $live_file for plugin <strong>$name</strong>.");
			return false;
		}
		
		$data = array(
			'name' => $name,
			'date_added' => $this->tool->format_datetime(),
			'live_file' => $live_file,
			'plugin_file' => $plugin_file,
			'live_file_modified' => time(),
			'plugin_file_modified' => filemtime($plugin_file)
		);
		
		$this->setPluginRegistryEntry($data);
		
		return true;
	}
	
	public function getPluginRegistry()
	{
		return $this->plugin_registry;
	}
	
	private function loadPluginFileRegistry()
	{
		$this->plugin_registry = $this->cache->get('plugin.registry');
		
		if (!$this->plugin_registry) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "plugin_registry");
			
			$this->plugin_registry = array();
			
			foreach ($query->rows as &$row) {
				$this->plugin_registry[$row['live_file']] = $row;
			}
			
			$this->cache->set('plugin.registry',$this->plugin_registry);
		}
	}
	
	public function requestDevelopmentMode()
	{
		if (!$this->config->get('config_development_mode')) {
			$dev_url = $this->url->link($_GET['route'], $this->url->get_query() . '&set_development_mode=1');
			$this->message->add('warning', "<br />You can turn on <a href=\"$dev_url\">Development Mode</a> so the system will anticipate changes, and automatically overwrite plugin files when possible.<br />");
		}
	}

	public function cleanReload()
	{
		unset($_GET['plugin_name']);
		unset($_GET['resolve_conflict']);
		unset($_GET['set_development_mode']);
		unset($_GET['redirect']);
		
		$url = $this->url->link($_GET['route'], $this->url->get_query());
		$this->url->redirect($url);
	}
	
	public function syncPluginFileWithLive($registry_entry)
	{
		$name = $registry_entry['name'];
		$plugin_file = $registry_entry['plugin_file'];
		$plugin_modified = (int)$registry_entry['plugin_file_modified'];
		$live_file = $registry_entry['live_file'];
		$live_modified = (int)$registry_entry['live_file_modified'];
		
		/**
		 * 1. Determine Coarse of action
		 */
		
		//If the plugin file is missing, the plugin likely has a problem, so lets uninstall it.
		if (!is_file($plugin_file)) {
			$missing = is_file($plugin_file) ? $live_file : $plugin_file;
			$this->message->add('warning', "The plugin file $missing was missing! The plugin $name has been uninstalled.");
			
			$this->uninstall($name);
			
			$this->url->redirect($this->url->admin('extension/plugin'));
		}
		
		//Assume nothing needs to be done
		$update_live = $update_plugin = false;
		
		//Conflict Resolution has been selected
		if (!empty($_GET['resolve_conflict'])) {
			$keep = $overwrite = false;
			
			if ($_GET['resolve_conflict'] === $live_file) {
				$keep = $live_file;
				$overwrite = $plugin_file; 
			} elseif ($_GET['resolve_conflict'] === $plugin_file) {
				$keep = $plugin_file;
				$overwrite = $live_file;
			}
			
			if ($keep && $overwrite) {
				if (!copy($keep, $overwrite)) {
					$this->message->add("warning", "There was an error while resolving the conflict in plugin <strong>$name</strong>.<br />Attempt to overwrite the file $overwrite with $keep failed!");
					return false;
				}
				
				$this->updatePluginFileTimestamps($name, $plugin_file, $live_file);
				
				$this->cleanReload();
			}
		}
		
		//If the live file for the plugin is missing, we need to regenerate it
		if (!is_file($live_file)) {
			$update_live = true;
		}
		//If the live file has been modified, we need to verify these changes should be pushed to the plugin file
		elseif (filemtime($live_file) > $live_modified) {
			$update_plugin = true;
		}
		
		//If the plugin file has been modified, we need to update the live file
		if (filemtime($plugin_file) > $plugin_modified) {
			$update_live = true;
		}
		
		//Live file is out of date
		if ($update_live) {
			//A simple update of the live file is all that is needed
			if (!$update_plugin) {
				if (!copy($plugin_file, $live_file)) {
					$this->message->add("warning", "There was an error while syncing $plugin_file to $live_file for the plugin <strong>$name</strong>!");
					return false;
				}
				
				$this->message->add("notify", "The Live file $live_file for the plugin <strong>$name</strong> has been synchronized!");
			}
			//Both files have been modified, we have a conflict!
			else {
				$keep_live = $this->url->link($_GET['route'], $this->url->get_query() . '&resolve_conflict=' . urlencode($live_file));
				$keep_plugin = $this->url->link($_GET['route'], $this->url->get_query() . '&resolve_conflict=' . urlencode($plugin_file));
				
				$msg = 
					"There is a conflict with the plugin <strong>$name</strong>!<br />" .
					" Both the Plugin file $plugin_file and the Live file $live_file have been modified.<br />" .
					" Synchronize the files and <a href=\"$keep_plugin\">keep Plugin</a> file changes," .
					" or <a href=\"$keep_live\">keep Live</a> file changes.";
					
				$this->message->add('warning', $msg);
				
				return false;
			}
		}
		//Ask admin if changes should be pushed to plugin.
		elseif ($update_plugin) {
			//Developer has requested to push changes to plugin
			if ($this->config->get('config_development_mode')) {
				if (!copy($live_file, $plugin_file)) {
					$this->message->add("warning", "There was an error while syncing $live_file to $plugin_file for the plugin <strong>$name</strong>!");
					return false;
				}
				
				$this->message->add("notify", "The Plugin file $plugin_file for the plugin <strong>$name</strong> has been synchronized!");
			}
			else {
				$keep_live = $this->url->link($_GET['route'], $this->url->get_query() . '&resolve_conflict=' . urlencode($live_file));
				$keep_plugin = $this->url->link($_GET['route'], $this->url->get_query() . '&resolve_conflict=' . urlencode($plugin_file));
				
				$msg = 
					"The Live file $live_file has been modified for the plugin <strong>$name</strong>!<br />" .
					" Either <a href=\"$keep_live\">update</a> the Plugin file," .
					" or <a href=\"$keep_plugin\">revert</a> the Live file to the contents of the Plugin file.";
					
				$this->message->add('warning', $msg);
				
				return false;
			}
		}
		//The files are already up to date!
		else {
			return true;
		}
		
		$this->updatePluginFileTimestamps($name, $plugin_file, $live_file);
		
		return true;
	}
	
	private function updatePluginFileTimestamps($name, $plugin_file, $live_file)
	{
		//Update plugin file data
		$data = array(
			'name' => $name,
			'date_added' => $this->tool->format_datetime(),
			'live_file' => $live_file,
			'plugin_file' => $plugin_file,
			'live_file_modified' => time(),
			'plugin_file_modified' => time(),
		);
		
		$this->setPluginRegistryEntry($data);
		
		$this->loadPluginFileRegistry();
	}
	
	private function setPluginRegistryEntry($data)
	{
		$values = '';
		foreach ($data as $key=>$value) {
			$values .= ($values?',':'') . "`$key`='" . $this->db->escape($value) . "'";
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "plugin_registry WHERE plugin_file = '" . $this->db->escape($data['plugin_file']) . "'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "plugin_registry SET $values");
		
		$this->cache->delete("plugin");
	}
	
	public function addFileModifications($name, $file_modifications) {
		foreach($file_modifications as $file_mod) {
			$this->file_merge->addFile($file_mod['for'], $name, $file_mod['mod']);
		}

		$this->file_merge->applyMergeRegistry();
	}
	
	public function removeFileModifications($name, $file_modifications) {
		foreach($file_modifications as $file_mod) {
			$this->file_merge->removeFile($file_mod['for'], $name);
		}
		
		$this->file_merge->applyMergeRegistry();
	}
	
	public function getFileMods($name)
	{
		$dir = DIR_PLUGIN . $name . '/file_mods';
		
		if(!is_dir($dir)) return array();
		
		$files = $this->tool->get_files_r($dir, false, FILELIST_STRING);
		
		$file_mods = array();
		
		foreach ($files as $file) {
			$rel_file = str_replace('\\','/',substr(str_replace($dir, '', $file),1));
			
			if (is_file(SITE_DIR . $rel_file)) {
				$file_mods[$rel_file] = 'file_mods/' . $rel_file;
				continue;
			}
			
			$filename = basename($file);
			
			//TODO: We probably dont need this anymore???
			$path = $this->nameToPath(SITE_DIR, $filename);
			
			if ($path) {
				$file_mods[str_replace(SITE_DIR, '', $path)] = 'file_mods/' . $filename;
			} else {
				$this->message->add("warning", "Invalid File Mod: " . $filename . ". The path could not be resolved to a file.");
				return false;
			}
		}
		
		return $file_mods;
	}
	
	private function nameToPath($dir, $name)
	{
		$path = explode("_", $name);
		
		$segment = '';
		
		do{
			$segment .= array_shift($path);
			
			if (is_dir($dir . $segment)) {
				$dir .= $segment . '/';
				$segment = '';
			}
			elseif (is_file($dir . $segment)) {
				return $dir . $segment;
			}
			else {
				$segment .= "_";
			}
		}while(count($path) > 0);
		
		return '';
	}
}
