<?php
class Extend {
	private $registry;
	
	function __construct($registry){
		$this->registry = $registry;
	}
	
	public function __get($key){
		return $this->registry->get($key);
	}
	
	public function add_navigation_link($link, $group = 'admin'){
		$defaults = array(
			'display' => '',
			'title' => '',
			'href' => '',
			'query' => '',
			'is_route' => '',
			'parent_id' => 0,
			'sort_order' => 0,
			'status' => 1,
		);
		
		foreach($defaults as $key => $default){
			if(!isset($link[$key])){
				$link[$key] = $default;
			}
		}
		
		$link['name'] = $this->tool->get_slug($link['name']);
		
		$result = $this->db->query("SELECT navigation_group_id FROM " . DB_PREFIX . "navigation_group WHERE name = '" . $this->db->escape($group) . "'");
		
		if($result->num_rows){
			$this->model_design_navigation->addNavigationLink($result->row['navigation_group_id'], $link);
		}
	}
	
	public function remove_navigation_link($name){
		$result = $this->db->query("SELECT navigation_id FROM " . DB_PREFIX . "navigation WHERE name = '" . $this->db->escape($name) . "'");
		
		if($result->num_rows){
			$this->model_design_navigation->deleteNavigationLink($result->row['navigation_id']);
		}
	}
	
	public function add_layout($name, $routes = array(), $data = array()){
		if(!is_array($routes)){
			$routes = array($routes);
		}
		
		$query = $this->db->query("SELECT COUNT(*) as total FROM " . DB_PREFIX . "layout WHERE name='$name'");
		
		if($query->row['total']){
			$this->message->add("warning", "Error while adding $name to layout! Duplicate name exists!");
			return false;
		}
		
		$layout = array(
			'name' => $name,
		);
		
		$layout += $data;
		
		if(!empty($routes)){
			$stores = $this->model_setting_store->getStores();
			
			foreach($stores as $store){
				foreach($routes as $route){
					$layout['layout_route'][] = array(
						'store_id' => $store['store_id'],
						'route' => $route 
					);
				}
			}
		}
		
		$this->model_design_layout->addLayout($layout);
	}
	
	public function remove_layout($name){
		$result = $this->db->query("SELECT layout_id FROM " . DB_PREFIX . "layout WHERE name='" . $this->db->escape($name) . "' LIMIT 1");
		
		if($result->num_rows){
			$this->model_design_layout->deleteLayout($result->row['layout_id']);
		}
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