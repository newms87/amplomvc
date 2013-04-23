<?php
class Extend {
	private $registry;
	
	function __construct($registry){
		$this->registry = $registry;
	}
	
	public function __get($key){
		return $this->registry->get($key);
	}
	
	public function add_db_hook($hook_set, $action, $table, $callback, $param = null, $priority = 0){
		$config_id = 'db_hook_' . $action . '_' . $table;
		
		$hooks = $this->config->get($config_id);
		
		if(!is_array($hooks)){
			$hooks = array();
		}
		
		$hooks[] = array(
			'hook_set' => $hook_set,
			'callback' => $callback,
			'param'	  => $param,
			'priority' => $priority,
		);
		
		$this->config->save('db_hook', $config_id, $hooks);
	}
	
	public function remove_db_hook($hook_set){
		$db_hooks = $this->config->get_group('db_hook');
		
		foreach($db_hooks as $hook_key => $hook){
			foreach($hook as $h_key => $h){
				if($h['hook_set'] == $hook_set){
					unset($db_hooks[$hook_key][$h_key]);
				}
			}
			
			if(empty($db_hooks[$hook_key])){
				unset($db_hooks[$hook_key]);
			}
		}
		
		$this->config->save_group('db_hook', $db_hooks);
	}
	
	public function enable_image_sorting($table, $column){
		$hook_set = '__image_sort__' . $table . '_' . $column;
		
		$this->add_db_hook($hook_set, 'insert', $table, array('Extend' => 'update_hsv_value'), $column);
		$this->add_db_hook($hook_set, 'update', $table, array('Extend' => 'update_hsv_value'), $column);
		
		$this->db->table_add_column($table, '__image_sort__' . $column, 'FLOAT');
	}
	
	public function disable_image_sorting($table, $column){
		$hook_set = '__image_sort__' . $table . '_' . $column;
		
		$this->remove_db_hook($hook_set);
		
		$this->db->table_drop_column($table, '__image_sort__' . $column);
	}
	
	public function update_hsv_value(&$data, $column){
		//TODO: Implement this! How do we hook into the DB for plugins that have called $this->enable_image_sorting()?
		
		$colors = $this->image->get_dominant_color($data[$column]);
		$HSV = $this->image->RGB_to_HSV($colors['r'], $colors['g'], $colors['b']);
		$data['__image_sort__' . $column] = $HSV['H'];
	}
}