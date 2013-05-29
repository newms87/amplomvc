<?php
class SystemModelPlugin extends Model{	
	public function installPlugin($plugin)
	{
		$this->delete('plugin', array('name'=>$name));
		
		$plugin['name']	= $name;
		$plugin['status']	= 1;
		
		$this->insert('plugin', $plugin);
		
		//Controller Adapters
		$this->delete('plugin_controller_adapter', array('name' => $name));
		
		foreach ($controller_adapters as $ca) {
			$ca['name']  = $name;
			$ca['admin'] = $ca['admin'] ? 1 : 0;
			
			$this->insert('plugin_controller_adapter', $ca);
		}
		
		//DB Requests
		$this->delete('plugin_db',array('name' => $name));
		
		foreach ($db_requests as $request) {
			$request['name'] = $name;
			
			if (!is_array($request['query_type'])) {
				$request['query_type'] = array($request['query_type']);
			}
			
			if (isset($request['restrict'])) {
				$request['restrict'] = count($request['restrict'])>1?implode(',',$request['restrict']):array_pop($request['restrict']);
			}
			
			foreach ($request['query_type'] as $query_type) {
				$request['query_type']  = $query_type;
				
				$this->insert('plugin_db', $request);
			}
		}
		
		//New Files
		if (!$this->integrate_new_files($name)) {
			$this->message->add("warning", "There was a problem while integrating the files in the new_files library for $name. The plugin has been uninstalled!<br />");
			$this->uninstall($name);
			return false;
		}
		
		//File Modifications
		$file_mods = $this->get_file_mods($name);
		
		if ($file_mods === false) {
			$this->message->add("warning", "There was a problem with the file_mods library for $name. The plugin has been uninstalled!<br />");
			$this->uninstall($name);
			return false;
		}
		
		if ($file_mods) {
			
			$this->plugin_handler->add_merge_files($name, $file_mods);
			
			if (!$this->plugin_handler->apply_merge_registry()) {
				$this->message->add('warning', "The installation of the plugin $name has failed and has been uninstalled!<br />");
				$this->uninstall($name);
				return false;
			}
		}
		
		return true;
	}
}