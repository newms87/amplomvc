<?php 
class pluginHandler{
   private $file_merge = null;
   private $merge_registry = array();
   
   protected $registry;
   protected $plugins;
   protected $language_extensions = array();
	protected $controller_adapters;
   
   function __construct(&$registry, $store_id, $admin, $merge_registry){
      $this->registry = &$registry;
      
		$this->load_controller_adapters($store_id);
      
      $this->merge_registry = $merge_registry;
      
      $this->validate_merge_registry();
   }
   
	public function __get($key) {
		return $this->registry->get($key);
	}
	
	private function load_controller_adapters($store_id){
		$cache_file = 'plugin.controller_adapters.' . (int)$store_id . "." . (defined("IS_ADMIN") ? 'admin' : 'store');
		
		$this->controller_adapters = $this->cache->get($cache_file);
		if(!isset($this->controller_adapters)){
		   $query = $this->db->query("SELECT pca.name, pca.for, pca.admin, pca.plugin_file, pca.callback FROM " . DB_PREFIX . "plugin_controller_adapter pca JOIN " . DB_PREFIX . "plugin p ON (p.name = pca.name) WHERE p.status='1' AND p.store_id = '" . (int)$store_id . "' AND pca.admin = '" . (defined("IS_ADMIN") ? 1 : 0) . "' ORDER BY pca.priority ASC");
			
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
   				trigger_error("The plugin file $file did not exists for the plugin $name.");
   				echo "The plugin file $file did not exists for the plugin $name.";
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
   
   public function loadLanguageExtensions($filename, $plugin_name=null){
      //Leave until we implement plugin language extending
      if($plugin_name) return array();
      
      $data = $this->cache->get('lang_ext.'.$filename);
      
      if($data === false || $data === NULL){
         $data = array();
         
         $query = $this->db->query("SELECT name, lang_ext FROM " . DB_PREFIX . "plugin_language_ext WHERE filename='" . $this->db->escape($filename) . "' ORDER BY priority ASC");
         
         foreach($query->rows as $ext){
            $file = DIR_PLUGIN . $ext['name'] . '/' . $ext['lang_ext'] . '.php';
            if(file_exists($file)){
               $_ = array();
               
               require($file);
               
               $data = array_merge($data, $_);
            }
            else{
               trigger_error("Error: Could not load language file $file requested by plugin");
            }
         }
         
         $this->cache->set('lang_ext.'.$filename, $data);
      }
      
      return $data;
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