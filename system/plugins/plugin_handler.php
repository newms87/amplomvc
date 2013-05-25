<?php 
class pluginHandler{
	private $file_merge = null;
	private $merge_registry = array();
	
	protected $registry;
	protected $plugin_registry;
	protected $plugins;
	protected $controller_adapters;
	
	function __construct(&$registry, $merge_registry){
		$this->registry = &$registry;
		
		$this->load_plugin_file_registry();
		
		$this->validate_plugin_file_registry();
		
		$this->load_controller_adapters();
		
		$this->merge_registry = $merge_registry;
		
		$this->validate_merge_registry();
	}
	
	public function __get($key) {
		return $this->registry->get($key);
	}
	
	private function load_plugin_file_registry(){
		$this->plugin_registry = $this->cache->get('plugin.registry');
		
		if(!$this->plugin_registry){
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "plugin_registry");
			
			$this->plugin_registry = array();
			
			foreach($query->rows as &$row){
				$this->plugin_registry[$row['live_file']] = $row;
			}
			
			$this->cache->set('plugin.registry',$this->plugin_registry);
		}
	}
	
	public function validate_plugin_file_registry(){
		if(isset($_GET['set_dev_mode'])){
			$this->set_development_mode($_GET['set_dev_mode']);
			$this->clean_reload();
		}
		
		if(!empty($_GET['sync_plugin_file'])){
			$this->sync_plugin_file($_GET['plugin_name'], $_GET['sync_plugin_file']);
			$this->clean_reload();
		}
		
		$dev_mode = false;
		
		foreach($this->plugin_registry as $reg){
			if(!is_file($reg['plugin_file'])){
				$this->message->add('warning', "The plugin file $reg[plugin_file] was missing! The plugin $reg[name] has been uninstalled");
				$this->url->redirect($this->url->admin('extension/plugin/uninstall', 'name=' . $reg['name'] . '&keep_data=1'));
			}
			
			if(!is_file($reg['live_file'])){
				$this->message->add('warning', "The LIVE plugin file $reg[live_file] was missing! The plugin $reg[name] has been uninstalled");
				$this->url->redirect($this->url->admin('extension/plugin/uninstall', 'name=' . $reg['name'] . '&keep_data=1'));
			}
			
			if(filemtime($reg['plugin_file']) > (int)$reg['plugin_file_modified']){
				$this->message->add('notify', "Updating file $reg[live_file] from Plugin <strong>$reg[name]</strong>. File was out of date.");
				$this->activate_plugin_file($reg['name'], new SplFileObject($reg['plugin_file']));
			}
			elseif(filemtime($reg['live_file']) > $reg['live_file_modified']){
				if(!empty($_COOKIE['development_mode'])){
					$this->sync_plugin_file($reg['name'], $reg['plugin_file']);
				}
				else{
					$_GET['plugin_name'] = $reg['name'];
					$_GET['sync_plugin_file'] = $reg['plugin_file'];
					
					$merge_url = $this->url->link($_GET['route'], $this->url->get_query());
					
					$this->message->add('warning', "The live file $reg[live_file] has been modified for the plugin <strong>$reg[name]</strong>! Click <a href=\"$merge_url\">here</a> to update the original plugin file.");
					
					$dev_mode = true;
				}
			}
		}
		
		if($dev_mode){
			$_GET['set_dev_mode'] = 1;
			$dev_url = $this->url->link($_GET['route'], $this->url->get_query());
			
			$this->message->add('warning', "<br/>Turn on <a href=\"$dev_url\">Development Mode</a> to automatically update plugin files.");
		}
	}

	public function clean_reload(){
		unset($_GET['plugin_name']);
		unset($_GET['sync_plugin_file']);
		unset($_GET['set_dev_mode']);
		unset($_GET['redirect']);
		
		$url = $this->url->link($_GET['route'], $this->url->get_query());
		$this->url->redirect($url);
	}
	
	
	public function sync_plugin_file($name, $file){
		$file =  new SplFileObject($file);
		
		$dir = DIR_PLUGIN . $name . '/new_files/';
		
		$plugin_file = preg_replace("/\\\\/", "/", $file->getPathName());
		$live_file = str_replace($dir, SITE_DIR, $plugin_file);
		
		if(!is_file($plugin_file) || !is_file($live_file)){
			$missing_file = is_file($live_file) ? $plugin_file : $live_file;
			$this->message->add('warning', "Error while syncing $live_file to $plugin_file for the plugin <strong>$name</strong>! $missing_file does not exist!");
			return false;
		}
		
		if(!copy($live_file, $plugin_file)){
			$this->message->add("warning", "There was an error while syncing $live_file to $plugin_file for the plugin <strong>$name</strong>!");
			return false;
		}
		
		touch($plugin_file);
		
		$data = array(
			'name' => $name,
			'date_added' => $this->tool->format_datetime(),
			'live_file' => $live_file,
			'plugin_file' => $plugin_file,
			'live_file_modified' => filemtime($live_file),
			'plugin_file_modified' => time(),
		);
		
		$values = '';
		foreach($data as $key=>$value){
			$values .= ($values?',':'') . "`$key`='$value'";
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "plugin_registry WHERE live_file = '$live_file'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "plugin_registry SET $values");
		
		$this->cache->delete("plugin");
		
		$this->load_plugin_file_registry();
		
		$this->message->add('notify', "The file $plugin_file for the plugin <strong>$name</strong> has been updated successfully!");
		
		return true;
	}
	
	public function activate_plugin_file($name, $file){
		$dir = DIR_PLUGIN . $name . '/new_files/';
		
		$plugin_file = preg_replace("/\\\\/", "/", $file->getPathName());
		$live_file = str_replace($dir, SITE_DIR, $plugin_file);
		
		if(strpos($live_file, '@template')){
			$live_file = str_replace('@template', 'default', $live_file);
		}
		
		$query = $this->url->get_query();
		$query = $query ? '&'.$query : '';
				
		$overwrite_files_url = $this->url->link($_GET['route'], "name=$name&overwrite_files=1" . $query);
		
		if(is_file($live_file)){
			if(isset($this->plugin_registry[$live_file])){
				$reg = $this->plugin_registry[$live_file];
				if(filemtime($reg['live_file']) > $reg['live_file_modified'] && empty($_GET['overwrite_files'])){
					$this->message->add("warning", "The file $live_file in plugin <strong>$name</strong> could not be updated because the file has been edited! <a href='$overwrite_files_url'>Click Here</a> to overwrite these files.");
					return false;
				}
			}
			elseif(empty($_GET['overwrite_files'])){
				$this->message->add("warning", "The File $live_file already exists! Cannot Integrate the file $plugin_file for the plugin <strong>$name</strong> due to the conflict! <a href='$overwrite_files_url'>Click Here</a> to overwrite these files.");
				return false;
			}
		}
		
		$copy_dir = dirname($live_file);
		
		if(!is_dir($copy_dir)){
			mkdir($copy_dir, 0777, true);
		}
		else{
			$mode = octdec($this->config->get('config_default_dir_mode'));
			chmod($copy_dir, $mode);
		}
		
		if(!copy($file->getPathName(), $live_file)){
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
		
		$values = '';
		foreach($data as $key=>$value){
			$values .= ($values?',':'') . "`$key`='$value'";
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "plugin_registry WHERE live_file = '$live_file'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "plugin_registry SET $values");
		
		$this->cache->delete("plugin");
		
		return true;
	}

	public function set_development_mode($status){
		if($status){
			//activate development mode for 24 hours
			$this->session->set_cookie('development_mode', '1', 3600 * 24);
			
			$_GET['set_dev_mode'] = 0;
			$dev_url = $this->url->link($_GET['route'], $this->url->get_query());
			$this->message->add('warning', "Warning! Development Mode is now active. Any changes made to LIVE plugin files will automatically overwrite the original plugin files. Click to <a href='$dev_url'>deactivate.</a>");
		}
		else{
			$this->session->delete_cookie('development_mode');
			
			$this->message->add('notify', "Development Mode has been deactivated.");
		}
	}

	private function load_controller_adapters(){
		$cache_file = 'plugin.controller_adapters.' . (defined("IS_ADMIN") ? 'admin' : 'store');
		
		$this->controller_adapters = $this->cache->get($cache_file);
		if(!isset($this->controller_adapters)){
			$query = $this->db->query("SELECT pca.name, pca.for, pca.admin, pca.plugin_file, pca.callback FROM " . DB_PREFIX . "plugin_controller_adapter pca JOIN " . DB_PREFIX . "plugin p ON (p.name = pca.name) WHERE p.status='1' AND pca.admin = '" . (defined("IS_ADMIN") ? 1 : 0) . "' ORDER BY pca.priority ASC");
			
			$this->controller_adapters = array();
			
			foreach($query->rows as $ca){
				$class = "controller" . strtolower(preg_replace('/[^a-z0-9]/i','', $ca['for']));
				$ca['adapter_class'] =  "ControllerPlugin_" . preg_replace('/[^a-z0-9]/i','',$ca['name'] . $ca['plugin_file']);
				
				$this->controller_adapters[$class][] = $ca;
			}
			
			$this->cache->set($cache_file, $this->controller_adapters);
		}
	}
	
	public function call_controller_adapter($controller){
		$class = strtolower(get_class($controller));
		
		if(isset($this->controller_adapters[$class])){
			foreach($this->controller_adapters[$class] as $adapter){
				$file = DIR_PLUGIN . $adapter['name'] . '/' . $adapter['plugin_file'] . '.php';
				
				if(!file_exists($file)){
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
	
	
	public function execute_db_requests($table, $query_type, $when, $class, $function, &$data=null, &$where=null){
		$table = $this->db->escape($table);
		$query_type = $this->db->escape($query_type);
		$class = $this->db->escape(strtolower($class));
		
		$method = '';
		
		if($function){
			if(is_string($function)){
				$method = $class . '::' . $this->db->escape(strtolower($function));
			}
			else{
				trigger_error("PLUGIN HANDLER: function was not a string! We need further implementation!");
			}
		}
		
		$restrict = "(`restrict` IS NULL OR LCASE(`restrict`) IN ('$class', '$method', ''))"; 
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "plugin_db WHERE `table` = '$table' AND `query_type` = '$query_type' AND `when` = '$when' AND $restrict");
		
		foreach($query->rows as $row){
			$this->db_request($row, $data, $where);
		} 
	}
	
	private function db_request($request, &$data, &$where){
		$file = DIR_PLUGIN . $request['name'] . '/' . $request['plugin_path'] . '.php';
		if(!is_file($file)){
			trigger_error("The plugin $request[name] did not have the file: $file");
			return;
		}
		
		_require_once($file);
		
		$class = "ModelPlugin" . preg_replace("/[_-]/",'',$request['name']);
		
		if(method_exists($class, $request['callback'])){
			$args = array();
			if($data) $args[] = &$data;
			if($where) $args[] = &$where;
			call_user_func_array(array(new $class($this->registry),$request['callback']),$args);
		}
		else{
			trigger_error("Could not find the method $class::$request[callback] in the plugin $request[name]");
			return;
		}
	}

	public function load_merge_registry(){
		if($this->file_merge === null){
			require_once(SITE_DIR . 'system/library/file_merge.php');
			
			$this->file_merge = new FileMerge($this->db, $this->config);
		}
		
		$this->merge_registry = $this->file_merge->get_merge_registry();
		
		$error = $this->file_merge->get_error();
		if($error){
			foreach($error as $e){
				$this->message->add('warning', $e);
			}
			return false;
		}
		
		return true;
	}
	
	public function reload_merge_registry(){
		if($this->file_merge === null){
			$this->load_merge_registry();
		}
		
		if(!$this->file_merge->sync_registry_with_db()){
			$this->message->add($this->file_merge->get_error());
		}
		
		$this->merge_registry = $this->file_merge->get_merge_registry();
		
		$error = $this->file_merge->get_error();
		if($error){
			foreach($error as $e){
				$this->message->add('warning', $e);
			}
			
			return false;
		}
		
		return true;
	}
	
	public function add_merge_file($file_path, $name, $mod_path){
		if($this->file_merge === null){
			$this->load_merge_registry();
		}
		
		$this->merge_registry[SITE_DIR . $file_path][$name] = $mod_path;
	}
	
	public function add_merge_files($name, $file_modifications){
		if($this->file_merge === null){
			$this->load_merge_registry();
		}
		
		foreach($file_modifications as $file_path=>$mod_path){
			$this->add_merge_file($file_path, $name, $mod_path);
		}
	}
	
	public function apply_merge_registry(){
		if($this->file_merge === null){
			$this->load_merge_registry();
		}
		
		$this->file_merge->set_merge_registry($this->merge_registry);
		
		$this->file_merge->apply_merge_registry();
		
		$this->clean_merged_files();
		
		$error = $this->file_merge->get_error();
		if($error){
			foreach($error as $e){
				$this->message->add('warning', $e);
			}
			return false;
		}
		
		return true;
	}
	
	public function get_file($file){
		if(isset($this->merge_registry[$file])){
			$file = str_replace(SITE_DIR, DIR_MERGED_FILES, $file);
		}
		
		return $file;
	}
	
	private function validate_merge_registry(){
		$valid = true;
		foreach($this->merge_registry as $file_path=>$names){
			if(!file_exists($file_path)){
				$valid = false;
				unset($this->merge_registry[$file_path]);
				continue;
			}
			
			$merged_file = $this->get_file($file_path);
			
			if(filemtime($file_path) > filemtime($merged_file)){
				if($this->config->get('config_debug')){
					$this->message->add('notify', "The merged file was out of date with the file $file_path. It has been updated");
				}
				$valid = false;
			}
			
			foreach($names as $name=>$mod_path){
				$plugin_file = DIR_PLUGIN . $name . '/' . $mod_path;
				if(!file_exists($plugin_file)){
					unset($this->merge_registry[$file_path][$name]);
					$msg = "The $name plugin is missing the file $plugin_file! This may cause system instability. Please disable this plugin or restore the file.";
					$this->message->add('warning', $msg);
					trigger_error($msg);
					$valid = false;
					continue;
				}
				
				if(filemtime($plugin_file) > filemtime($merged_file)){
					if($this->config->get('config_debug')){
						$this->message->add('notify', "The merged file was out of date with the file $plugin_file. It has been updated");
					}
					$valid = false;
				}
			}
			
		}
		
		if(!$valid){
			if($this->apply_merge_registry()){
				$this->clean_merged_files();
				$this->url->reload_page();
			}
			else{
				$msg = 'There was a problem validating the plugin merge file registry. The problem could not be fixed! Please validate the plugins!';
				trigger_error($msg);
				$this->message->add('warning', $msg);
			}
		}
	}
	
	public function clean_merged_files(){
		$files = $this->get_all_files_r(DIR_MERGED_FILES);
		
		$merged_files = array();
		foreach(array_keys($this->merge_registry) as $m_file){
			$merged_files[] = strtolower(str_replace(SITE_DIR, DIR_MERGED_FILES, $m_file));
		}
		
		foreach($files as $file){
			if(!in_array(strtolower($file), $merged_files)){
				if(file_exists($file)){
					unlink($file);
					
					$dir = dirname($file);
					
					while(strpos($dir, SITE_DIR) === 0 && is_dir($dir) && count(scandir($dir)) <= 2){
						if(!rmdir($dir)){
							break;
						}
						$dir = dirname($dir);
					}
				}
			}
		}
	}
	
	public function get_all_files_r($dir, $ignore=array(), $ext=array('php', 'tpl'), $depth=0){
		if($depth > 20){
			echo "we have too many recursions!";
			exit;
		}
		
		$dir = rtrim($dir, '/');
		
		if(!is_dir($dir) || in_array($dir . '/', $ignore))return array();
		
		$handle = @opendir($dir);
		
		$files = array();
		while(($file = readdir($handle)) !== false){
			if($file == '.' || $file == '..')continue;
			
			$file_path = $dir . '/' . $file;
			
			if(is_dir($file_path)){
				$files = array_merge($files, $this->get_all_files_r($file_path, $ignore,$ext, $depth+1));
			}
			else{
				if(!empty($ext)){
					$match = null;
					preg_match("/[^\.]*$/", $file, $match);
					
					if(!in_array($match[0], $ext)){
						continue;
					}
				}
				$files[] = $file_path;
			}
		}
		
		return $files;
	}
}