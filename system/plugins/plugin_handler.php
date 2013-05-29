<?php
class pluginHandler
{
	private $file_merge = null;
	private $merge_registry = array();
	
	protected $registry;
	protected $plugin_registry;
	protected $plugins;
	protected $controller_adapters;
	
	function __construct(&$registry, $merge_registry)
	{
		$this->registry = &$registry;
		
		$this->registry->set('plugin_handler', $this);
		
		$this->load_plugin_file_registry();
		
		//TODO: We need an effecient way to handle plugin validation
		// if we have 1000's of plugin files we cannot check for changes on every file every time
		// we must check periodically, or when in development mode
		
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
				
				$this->clean_reload();
			}
			
			if (!empty($_COOKIE['development_mode'])) {
				$this->config->set('config_development_mode', 1);
			}
		}
		
		if ($this->config->get('config_development_mode')) {
			$this->validate_plugin_file_registry();
		}
		
		$this->load_controller_adapters();
		
		$this->merge_registry = $merge_registry;
		
		$this->validate_merge_registry();
	}
	
	public function __get($key)
	{
		return $this->registry->get($key);
	}
	
	private function load_plugin_file_registry()
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
	
	public function validate_plugin_file_registry()
	{
		$dev_mode = false;
		
		foreach ($this->plugin_registry as $registry_entry) {
			if (!$this->sync_plugin_file_with_live($registry_entry)) {
				$dev_mode = true;
			}
		}
		
		if (!$this->config->get('config_development_mode') && $dev_mode) {
			$dev_url = $this->url->link($_GET['route'], $this->url->get_query() . '&set_development_mode=1');
			
			$this->message->add('warning', "<br />You can turn on <a href=\"$dev_url\">Development Mode</a> so the system will anticipate changes, and automatically overwrite plugin files when possible.<br />");
		}
	}

	public function clean_reload()
	{
		unset($_GET['plugin_name']);
		unset($_GET['resolve_conflict']);
		unset($_GET['set_development_mode']);
		unset($_GET['redirect']);
		
		$url = $this->url->link($_GET['route'], $this->url->get_query());
		$this->url->redirect($url);
	}
	
	
	public function sync_plugin_file_with_live($registry_entry)
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
			//TODO: we should be able to uninstall from library...
			if (!defined("IS_ADMIN")) {
				$this->url->redirect($this->url->admin());
			}
			
			$missing = is_file($plugin_file) ? $live_file : $plugin_file;
			$this->message->add('warning', "The plugin file $missing was missing! The plugin $name has been uninstalled.");
			
			$this->model_setting_plugin->uninstall($name);
			
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
			}
		}
		//Determine if any updates are necessary or conflicts exist
		else {
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
				$this->message->add('notify', "Updating file $live_file for the Plugin <strong>$name</strong>. File was out of date.");
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
				if (!empty($_COOKIE['development_mode'])) {
					if (!copy($live_file, $plugin_file)) {
						$this->message->add("warning", "There was an error while syncing $live_file to $plugin_file for the plugin <strong>$name</strong>!");
						return false;
					}
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
		}
		
		//Update plugin file data
		$data = array(
			'name' => $name,
			'date_added' => $this->tool->format_datetime(),
			'live_file' => $live_file,
			'plugin_file' => $plugin_file,
			'live_file_modified' => filemtime($live_file),
			'plugin_file_modified' => time(),
		);
		
		$this->set_plugin_file($data);
		
		$this->load_plugin_file_registry();
		
		$this->message->add('notify', "The file $plugin_file for the plugin <strong>$name</strong> has been updated successfully!");
		
		return true;
	}
	
	public function activate_plugin_file($name, $file)
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
		
		$this->set_plugin_file($data);
	}

	private function set_plugin_file($data)
	{
		$values = '';
		foreach ($data as $key=>$value) {
			$values .= ($values?',':'') . "`$key`='" . $this->db->escape($value) . "'";
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "plugin_registry WHERE plugin_file = '" . $this->db->escape($data['plugin_file']) . "'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "plugin_registry SET $values");
		
		$this->cache->delete("plugin");
		
		return true;
	}

	private function load_controller_adapters()
	{
		$cache_file = 'plugin.controller_adapters.' . (defined("IS_ADMIN") ? 'admin' : 'store');
		
		$this->controller_adapters = $this->cache->get($cache_file);
		if (!isset($this->controller_adapters)) {
			$query = $this->db->query("SELECT pca.name, pca.for, pca.admin, pca.plugin_file, pca.callback FROM " . DB_PREFIX . "plugin_controller_adapter pca JOIN " . DB_PREFIX . "plugin p ON (p.name = pca.name) WHERE p.status='1' AND pca.admin = '" . (defined("IS_ADMIN") ? 1 : 0) . "' ORDER BY pca.priority ASC");
			
			$this->controller_adapters = array();
			
			foreach ($query->rows as $ca) {
				$class = "controller" . strtolower(preg_replace('/[^a-z0-9]/i','', $ca['for']));
				$ca['adapter_class'] =  "ControllerPlugin_" . preg_replace('/[^a-z0-9]/i','',$ca['name'] . $ca['plugin_file']);
				
				$this->controller_adapters[$class][] = $ca;
			}
			
			$this->cache->set($cache_file, $this->controller_adapters);
		}
	}
	
	public function call_controller_adapter($controller)
	{
		$class = strtolower(get_class($controller));
		
		if(isset($this->controller_adapters[$class]))
{
			foreach($this->controller_adapters[$class ] as $adapter)
{
				$file = DIR_PLUGIN . $adapter['name'] . '/' . $adapter['plugin_file'] . '.php';
				
				if (!file_exists($file)) {
					trigger_error("The plugin file $file did not exists for the plugin <strong>$name</strong>.");
					echo "The plugin file $file did not exists for the plugin <strong>$name</strong>.";
					return;
				}
				
				_require_once($file);
				
				$adapter_class = new $adapter['adapter_class']('', $this->registry);
				
				//synchronize the adapter controller with the original controller that is being rendered
				$adapter_class->data = &$controller->data;
				$adapter_class->error = &$controller->error;
				$adapter_class->template = &$controller->template;
				
				call_user_func(array($adapter_class,$adapter['callback']));
			}
		}
	}
	
	
	public function execute_db_requests($table, $query_type, $when, $class, $function, &$data=null, &$where=null)
	{
		$table = $this->db->escape($table);
		$query_type = $this->db->escape($query_type);
		$class = $this->db->escape(strtolower($class));
		
		$method = '';
		
		if($function)
{
			if (is_string($function)) {
				$method = $class . '::' . $this->db->escape(strtolower($function));
			}
			else {
				trigger_error("PLUGIN HANDLER: function was not a string! We need further implementation!");
			}
		}
		
		$restrict = "(`restrict` IS NULL OR LCASE(`restrict`) IN ('$class ', '$method', ''))";
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "plugin_db WHERE `table` = '$table' AND `query_type` = '$query_type' AND `when` = '$when' AND $restrict");
		
		foreach($query->rows as $row)
{
			$this->db_request($row, $data, $where);
		}
	}
	
	private function db_request($request, &$data, &$where)
	{
		$file = DIR_PLUGIN . $request['name'] . '/' . $request['plugin_path'] . '.php';
		if (!is_file($file)) {
			trigger_error("The plugin $request[name] did not have the file: $file");
			return;
		}
		
		_require_once($file);
		
		$class = "ModelPlugin" . preg_replace("/[_-]/",'',$request['name']);
		
		if(method_exists($class, $request['callback']))
{
			$args = array();
			if($data) $args[] = &$data;
			if($where) $args[] = &$where;
			call_user_func_array(array(new $class ($this->registry),$request['callback']),$args);
		}
		else {
			trigger_error("Could not find the method $class ::$request[callback] in the plugin $request[name]");
			return;
		}
	}

	public function load_merge_registry()
	{
		if ($this->file_merge === null) {
			require_once(SITE_DIR . 'system/library/file_merge.php');
			
			$this->file_merge = new FileMerge($this->db, $this->config);
		}
		
		$this->merge_registry = $this->file_merge->get_merge_registry();
		
		$error = $this->file_merge->get_error();
		if ($error) {
			foreach ($error as $e) {
				$this->message->add('warning', $e);
			}
			return false;
		}
		
		return true;
	}
	
	public function reload_merge_registry()
	{
		if ($this->file_merge === null) {
			$this->load_merge_registry();
		}
		
		if (!$this->file_merge->sync_registry_with_db()) {
			$this->message->add($this->file_merge->get_error());
		}
		
		$this->merge_registry = $this->file_merge->get_merge_registry();
		
		$error = $this->file_merge->get_error();
		if ($error) {
			foreach ($error as $e) {
				$this->message->add('warning', $e);
			}
			
			return false;
		}
		
		return true;
	}
	
	public function add_merge_file($file_path, $name, $mod_path)
	{
		if ($this->file_merge === null) {
			$this->load_merge_registry();
		}
		
		$this->merge_registry[SITE_DIR . $file_path][$name] = $mod_path;
	}
	
	public function add_merge_files($name, $file_modifications)
	{
		if ($this->file_merge === null) {
			$this->load_merge_registry();
		}
		
		foreach ($file_modifications as $file_path=>$mod_path) {
			$this->add_merge_file($file_path, $name, $mod_path);
		}
	}
	
	public function apply_merge_registry()
	{
		if ($this->file_merge === null) {
			$this->load_merge_registry();
		}
		
		$this->file_merge->set_merge_registry($this->merge_registry);
		
		$this->file_merge->apply_merge_registry();
		
		$this->clean_merged_files();
		
		$error = $this->file_merge->get_error();
		if ($error) {
			foreach ($error as $e) {
				$this->message->add('warning', $e);
			}
			return false;
		}
		
		return true;
	}
	
	public function get_file($file)
	{
		if (isset($this->merge_registry[$file])) {
			$file = str_replace(SITE_DIR, DIR_MERGED_FILES, $file);
		}
		
		return $file;
	}
	
	private function validate_merge_registry()
	{
		$valid = true;
		foreach ($this->merge_registry as $file_path=>$names) {
			if (!file_exists($file_path)) {
				$valid = false;
				unset($this->merge_registry[$file_path]);
				continue;
			}
			
			$merged_file = $this->get_file($file_path);
			
			if (filemtime($file_path) > filemtime($merged_file)) {
				if ($this->config->get('config_debug')) {
					$this->message->add('notify', "The merged file was out of date with the file $file_path. It has been updated");
				}
				$valid = false;
			}
			
			foreach ($names as $name=>$mod_path) {
				$plugin_file = DIR_PLUGIN . $name . '/' . $mod_path;
				if (!file_exists($plugin_file)) {
					unset($this->merge_registry[$file_path][$name]);
					$msg = "The $name plugin is missing the file $plugin_file! This may cause system instability. Please disable this plugin or restore the file.";
					$this->message->add('warning', $msg);
					trigger_error($msg);
					$valid = false;
					continue;
				}
				
				if (filemtime($plugin_file) > filemtime($merged_file)) {
					if ($this->config->get('config_debug')) {
						$this->message->add('notify', "The merged file was out of date with the file $plugin_file. It has been updated");
					}
					$valid = false;
				}
			}
			
		}
		
		if (!$valid) {
			if ($this->apply_merge_registry()) {
				$this->clean_merged_files();
				$this->url->reload_page();
			}
			else {
				$msg = 'There was a problem validating the plugin merge file registry. The problem could not be fixed! Please validate the plugins!';
				trigger_error($msg);
				$this->message->add('warning', $msg);
			}
		}
	}
	
	public function clean_merged_files()
	{
		$files = $this->get_all_files_r(DIR_MERGED_FILES);
		
		$merged_files = array();
		foreach (array_keys($this->merge_registry) as $m_file) {
			$merged_files[] = strtolower(str_replace(SITE_DIR, DIR_MERGED_FILES, $m_file));
		}
		
		foreach ($files as $file) {
			if (!in_array(strtolower($file), $merged_files)) {
				if (file_exists($file)) {
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
	
	public function get_all_files_r($dir, $ignore=array(), $ext=array('php', 'tpl'), $depth=0){
		if ($depth > 20) {
			echo "we have too many recursions!";
			exit;
		}
		
		$dir = rtrim($dir, '/');
		
		if(!is_dir($dir) || in_array($dir . '/', $ignore))return array();
		
		$handle = @opendir($dir);
		
		$files = array();
		while (($file = readdir($handle)) !== false) {
			if($file == '.' || $file == '..')continue;
			
			$file_path = $dir . '/' . $file;
			
			if (is_dir($file_path)) {
				$files = array_merge($files, $this->get_all_files_r($file_path, $ignore,$ext, $depth+1));
			}
			else {
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
}