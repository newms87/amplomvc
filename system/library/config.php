<?php
class Config {
	private $data = array();
	private $registry;
	private $store_id;
	
	public function __construct($registry, $store_id = 0){
		$this->registry = $registry;
		
		$this->store_id = (int)$store_id;
		
		$settings = $this->cache->get('setting.config.' . $this->store_id);
		
		if(!$settings){
			$results = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '0' OR store_id = '$this->store_id' ORDER BY store_id ASC");
			
			$settings = array();
			
			foreach ($results->rows as $row) {
				$settings[$row['key']] = $row['serialized'] ? unserialize($row['value']) : $row['value'];
			}
			
			$this->cache->set('setting.config.' . $this->store_id, $settings);
		}
		
		$this->data = $settings;
	}
	
	public function __get($key){
		return $this->registry->get($key);
	}
	
  	public function get($key) {
    	return isset($this->data[$key]) ? $this->data[$key] : null;
  	}
	
	public function set($key, $value) {
    	$this->data[$key] = $value;
  	}
	
	public function get_group($group){
		$results = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE `group` = '" . $this->db->escape($group) . "' AND (store_id='0' OR store_id='$this->store_id')");
		
		$group = array();
		
		foreach($results->rows as $row){
			$group[$row['key']] = $row['serialized'] ? unserialize($row['value']) : $row['value'];
		}
		
		return $group;
	}
	
	public function get_all(){
		return $this->data;
	}
	
	public function has($key) {
    	return isset($this->data[$key]);
  	}
	
	public function save($group, $key, $data, $auto_load = true){
		$this->model_setting_setting->editSettingKey($group, $key, $data, $this->store_id, $auto_load);
	}
	
	public function save_group($group, $data, $auto_load = true){
		$this->model_setting_setting->editSetting($group, $data, $this->store_id, $auto_load);
	}
}
