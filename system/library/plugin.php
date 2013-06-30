<?php
class Plugin extends Library{
	private $plugins;
	private $plugin_registry;
	private $file_merge;
	
	function __construct($registry)
	{
		parent::__construct($registry);
		
		$this->registry->set('plugin', $this);
		
		$this->language->system('plugin');
		
		$this->loadPluginFileRegistry();
		
		$this->file_merge = new FileMerge($this->registry);
	}
	
	public function getFile($file)
	{
		if (isset($this->plugin_registry[$file])) {
			$file = $this->plugin_registry[$file]['plugin_file'];
		}
		
		return $this->file_merge->getFile($file);
	}
	
	public function loadPlugin($name)
	{
		if (!isset($this->plugins[$name])) {
			$setup_file = DIR_PLUGIN . $name . '/setup.php';
			
			if (!is_file($setup_file)) {
				$this->message->add("warning", $this->_('error_setup_file', $setup_file, $name));
				
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
			$this->message->add("warning", $this->_("error_integrate_new_files", $name));
			$this->uninstall($name);
			return false;
		}
		
		//File Modifications
		$file_mods = $this->getFileMods($name);
		
		if ($file_mods === false) {
			$this->message->add("warning", $this->_('error_file_mod', $name));
			$this->uninstall($name);
			return false;
		}
		
		$this->file_merge->addFiles($name, $file_mods);
		
		if (!$this->file_merge->applyMergeRegistry()) {
			$this->message->add('warning', $this->_('error_install_merge', $name));
			$this->uninstall($name);
			return false;
		}

		$this->message->add('success', $this->_("success_install",$name));
		
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
			$this->message->add('warning', $this->_('error_uninstall_merge', $name));
			return false;
		}
		
		$this->message->add('notify', $this->_('success_uninstall', $name));
		
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
		
		//Live file already exists! This is a possible conlflict...
		//If it is not a registered plugin file for this plugin, ask admin what to do.
		if ( is_file($live_file) 
			  && (!isset($this->plugin_registry[$live_file]) || $this->plugin_registry[$live_file]['name'] !== $name)) {
			//If no request to overwrite the live file
			if (empty($_GET['overwrite_file']) || $_GET['overwrite_file'] !== $live_file) {
				$overwrite_file_url = $this->url->link($_GET['route'], $this->url->getQuery() . "&name=$name&overwrite_file=" . urlencode($live_file));
				$msg = 
					"Unable to integrate the file $plugin_file for the plugin <strong>$name</strong> because the file $live_file already exists!" .
					" Either manually remove the file or <a href='$overwrite_file_url'>overwrite</a> this file with the plugin file.";
					
				$this->message->add("warning", $msg);
				
				return false;
			}
		}
		
		//Generate the live file with the contents of the plugin file
		_is_writable(dirname($live_file));
		
		if (!symlink($file->getPathName(), $live_file)) {
			$this->message->add("warning", "There was an error while copying $plugin_file to $live_file for plugin <strong>$name</strong>.");
			return false;
		}
		
		$data = array(
			'name' => $name,
			'date_added' => $this->date->now(),
			'live_file' => $live_file,
			'plugin_file' => $plugin_file,
		);
		
		$values = '';
		
		foreach ($data as $key=>$value) {
			$values .= ($values?',':'') . "`$key`='" . $this->db->escape($value) . "'";
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "plugin_registry WHERE plugin_file = '" . $this->db->escape($data['plugin_file']) . "'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "plugin_registry SET $values");
		
		$this->cache->delete("plugin");
		
		return true;
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
	
	public function addFileModifications($name, $file_modifications)
	{
		foreach($file_modifications as $file_mod) {
			$this->file_merge->addFile($file_mod['for'], $name, $file_mod['mod']);
		}

		$this->file_merge->applyMergeRegistry();
	}
	
	public function removeFileModifications($name, $file_modifications)
	{
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
