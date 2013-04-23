<?php
class ModelSettingPlugin extends Model {
   
   public function getInstalledPlugins() {
      $query = $this->query("SELECT * FROM " . DB_PREFIX . "plugin GROUP BY name");
      $query2 = $this->query("SELECT * FROM " . DB_PREFIX . "plugin_db GROUP BY name");
      
      $queries = array_merge($query->rows, $query2->rows);
      
      $installed = array();
      foreach($queries as $row){
         $installed[$row['name']][] = $row;
      }
      return $installed;
   }
   
   public function install($name, $controller_adapters, $db_requests) {
   	$this->cache->delete('plugin');
		
		$stores = $this->model_setting_store->getStores();
		
      $this->delete('plugin', array('name'=>$name));
      
      //Activate this plugin for all stores
      foreach($stores as $store){
         $plugin['name']         = $name;
         $plugin['store_id']     = $store['store_id'];
         $plugin['status']       = 1;
         
         $this->insert('plugin', $plugin);
      }
      
		
		//Controller Adapters
		$this->delete('plugin_controller_adapter', array('name' => $name));
		
		foreach($controller_adapters as $ca){
			$ca['name']  = $name;
			$ca['admin'] = $ca['admin'] ? 1 : 0;
			
			$this->insert('plugin_controller_adapter', $ca);
		}
		
      //DB Requests
      $this->delete('plugin_db',array('name' => $name));
      
      foreach($db_requests as $request){
         $request['name'] = $name;
         
         if(!is_array($request['query_type']))
            $request['query_type'] = array($request['query_type']);
         
         if(isset($request['restrict'])){
            $request['restrict'] = count($request['restrict'])>1?implode(',',$request['restrict']):array_pop($request['restrict']);
         }
         
         foreach($request['query_type'] as $query_type){
            $request['query_type']  = $query_type;
            
            $this->insert('plugin_db', $request);
         }
      }
      
		//New Files
		if(!$this->integrate_new_files($name)){
			$this->message->add("warning", "There was a problem while integrating the files in the new_files library for $name. The plugin has been uninstalled!<br />");
			$this->uninstall($name);
			return false;
		}
		
      //File Modifications
      $file_mods = $this->get_file_mods($name);
		
		if($file_mods === false){
			$this->message->add("warning", "There was a problem with the file_mods library for $name. The plugin has been uninstalled!<br />");
			$this->uninstall($name);
			return false;
		}
		
      if($file_mods){
         
         $this->plugin_handler->add_merge_files($name, $file_mods);
         
         if(!$this->plugin_handler->apply_merge_registry()){
            $this->message->add('warning', "The installation of the plugin $name has failed and has been uninstalled!<br />");
            $this->uninstall($name);
            return false;
         }
      }
      
      return true;
   }
   
   public function uninstall($name) {
   	$this->cache->delete('plugin');
		
   	//remove files from plugin that were registered
		$query = $this->get('plugin_registry', '*', array('name' => $name));
   	
		foreach($query->rows as $row){
			if(is_file($row['live_file'])){
				$this->message->add("notify", "removing plugin file $row[live_file]");
				unlink($row['live_file']);
			}
		}
		
		$this->delete('plugin_registry', array('name' => $name));
		
      $this->delete('plugin', array('name'=>$name));
      $this->delete('plugin_controller_adapter', array('name'=>$name));
      $this->delete('plugin_db', array('name'=>$name));
      $this->delete('plugin_file_modification', array('name'=>$name));
		
      if(!$this->plugin_handler->reload_merge_registry()){
         $this->message->add('warning', "There was a problem while uninstalling $name! Please try again.");
         return false;
      }
      
      return true;
   }
   
   
   public function getPluginData($name=false){
      $where = $name?"WHERE `name`='".$this->db->escape($name)."'":'';
      $query = $this->query("SELECT * FROM " . DB_PREFIX . "plugin $where ORDER BY `name`");
      $plugin_data = array();
      foreach($query->rows as $row){
         if($name){
            $plugin_data[] = $row;
         }
         else{
            $plugin_data[$row['name']] = $row;
         }
      }
      return $plugin_data;
   }
   
   public function updatePlugin($name, $plugs){
      $this->deletePlugin($name);
      
      foreach($plugs as $data){
         if(isset($data['hooks'])){
            foreach($data['hooks'] as $key=>$h){
               unset($data['hooks'][$key]);
               $data['hooks'][$h['for']] = $h;
               unset($data['hooks'][$h['for']]['for']);
            }
         }
         else{
            $data['hooks'] = null;
         }
         
         $plugin['name'] = $this->db->escape($name);
         $plugin['function'] = $this->db->escape($data['function']);
         $plugin['plugin_path'] = $this->db->escape($data['plugin_path']);
         $plugin['base_type'] = $this->db->escape($data['base_type']);
         $plugin['route'] = $this->db->escape($data['route']);
         $plugin['store_id'] = (int)$data['store_id'];
         $plugin['class_path'] = $this->db->escape($data['class_path']);
         $plugin['type'] = $this->db->escape($data['type']);
         $plugin['hooks'] = $data['hooks']?serialize($data['hooks']):'';
         $plugin['priority'] = (int)$data['priority'];
         $plugin['status'] = (int)$data['status'];
         
         $this->insert('plugin', $plugin);
      }
   }
   
   public function deletePlugin($name, $plugin_path=null){
      $where = array(
         'name' => $name
      );
      if($plugin_path){
         $where['plugin_path'] = $plugin_path;
      }
      
      $this->delete('plugin', $where);
   }
	
	public function integrate_new_files($name){
		$dir = DIR_PLUGIN . $name . '/new_files/';
		
		$files = $this->tool->get_files_r($dir);
		
		foreach($files as $file){
			if(!$this->plugin_handler->activate_plugin_file($name, $file)){
				return false;
			}
		}
		
		return true;
	}
	
	public function get_file_mods($name){
		$dir = DIR_PLUGIN . $name . '/file_mods';
		
		if(!is_dir($dir)) return array();
		
		$files = $this->tool->get_files_r($dir, false, FILELIST_STRING);
		
		$file_mods = array();
		
		foreach($files as $file){
			$rel_file = str_replace('\\','/',substr(str_replace($dir, '', $file),1));
			
			if(is_file(SITE_DIR . $rel_file)){
				$file_mods[$rel_file] = 'file_mods/' . $rel_file;
				continue;
			}
			
			$filename = basename($file);
			
			if(strpos($filename, '@template_') === 0){
				$themes = glob(SITE_DIR . 'catalog/view/theme/*', GLOB_ONLYDIR);
				
				foreach($themes as $theme){
					$theme_path = $this->name_to_path($theme . '/template/', str_replace('@template_', '', $filename));
					
					if($theme_path){
						$file_mods[str_replace(SITE_DIR, '', $theme_path)] = 'file_mods/' . $filename;
					}
					else{
						$this->message->add("warning", "The template " . basename($theme) . " may be incompatible with the plugin $name because the template did not have a version of the file $filename.");
					}
				}
			}
			else{
				$path = $this->name_to_path(SITE_DIR, $filename);
				
				if($path){
					$file_mods[str_replace(SITE_DIR, '', $path)] = 'file_mods/' . $filename;
				}
				else{
					$this->message->add("warning", "Invalid File Mod: " . $filename . ". The path could not be resolved to a file.");
					return false;
				}
			}
		}
		
		return $file_mods;
	}
	
	private function name_to_path($dir, $name){
		$path = explode("_", $name);
		
		$segment = '';
		
		do{
			$segment .= array_shift($path);
			
			if(is_dir($dir . $segment)){
				$dir .= $segment . '/';
				$segment = '';
			}
			elseif(is_file($dir . $segment)){
				return $dir . $segment;
			}
			else{
				$segment .= "_";
			}
		}while(count($path) > 0);
		
		return '';
	}
}