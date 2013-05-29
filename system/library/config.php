<?php
class Config 
{
	private $data = array();
	private $registry;
	private $store_id;
	private $site_config;
	
	public function __construct(&$registry, $store = 0)
	{
		$this->registry = &$registry;
		
		//self assigning so we can use config immediately!
		$this->registry->set('config', $this);
		
		$this->load_site_config();
		
		//If we only have a store_id, get the store info
		if (is_integer($store)) {
			$store = $this->load_store($store);
		}
		elseif (empty($store)) {
			//If the store is invalid, set to the deafult store (or the first store if no default)
			$result = $this->db->query("SELECT `value` FROM " . DB_PREFIX . "setting WHERE `key` = 'config_default_store'");
			
			$store_id = $result->num_rows ? $result->row['value'] : 1;
			
			$store = $this->load_store($store_id);
		}
		
		$this->store_id = $store['store_id'];
		$this->data['config_store_id'] = $store['store_id'];
		$this->data['config_url'] = $store['url'];
		$this->data['config_ssl'] = $store['ssl'];
		
		//Get the settings specific to the requested store
		$settings = $this->cache->get('setting.config.' . $this->store_id);
		
		if (!$settings) {
			$results = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '0' OR store_id = '$this->store_id' ORDER BY store_id ASC");
			
			$settings = array();
			
			foreach ($results->rows as $row) {
				$settings[$row['key']] = $row['serialized'] ? unserialize($row['value']) : $row['value'];
			}
			
			$this->cache->set('setting.config.' . $this->store_id, $settings);
		}
		
		$this->data += $settings;
	}
	
	public function __get($key)
	{
		return $this->registry->get($key);
	}
	
  	public function get($key)
  	{
		return isset($this->data[$key]) ? $this->data[$key] : null;
  	}
	
	public function set($key, $value)
	{
		$this->data[$key] = $value;
  	}
	
	private function load_store($store_id)
	{
		$result = $this->db->query("SELECT * FROM ". DB_PREFIX . "store WHERE store_id = '" . (int)$store_id . "' LIMIT 1");
			
		if ($result->num_rows) {
			return $result->row;
		}
		else {
			return array(
				'store_id' => 1,
				'url' => SITE_URL,
				'ssl' => SITE_SSL,
			);
		}
	}
		
	public function get_group($group)
	{
		$results = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE `group` = '" . $this->db->escape($group) . "' AND (store_id='0' OR store_id='$this->store_id')");
		
		$group = array();
		
		foreach ($results->rows as $row) {
			$group[$row['key']] = $row['serialized'] ? unserialize($row['value']) : $row['value'];
		}
		
		return $group;
	}
	
	public function get_all()
	{
		return $this->data;
	}
	
	public function has($key)
	{
		return isset($this->data[$key]);
  	}
	
	public function save($group, $key, $data, $auto_load = true)
	{
		$this->model_setting_setting->editSettingKey($group, $key, $data, $this->store_id, $auto_load);
	}
	
	public function save_group($group, $data, $auto_load = true)
	{
		$this->model_setting_setting->editSetting($group, $data, $this->store_id, $auto_load);
	}
	
	private function load_site_config()
	{
		$site_config_file = DIR_SYSTEM . 'site_config.php';
		
		$_ = array();
		
		require_once($site_config_file);
		
		$this->site_config = $_;
	}
	
	public function run_site_config()
	{
		$admin_exists = $this->db->query_var("SELECT COUNT(*) as total FROM " . DB_PREFIX . "store WHERE store_id = 0");
		
		if (!$admin_exists) {
			$this->db->query("SET GLOBAL sql_mode='NO_AUTO_VALUE_ON_ZERO'");
			$this->db->query("SET SESSION sql_mode='NO_AUTO_VALUE_ON_ZERO'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "store SET " . $this->db->get_insert_string($this->site_config['admin_store']));
		}
		
		$default_exists = $this->db->query_var("SELECT COUNT(*) as total FROM " . DB_PREFIX . "store WHERE store_id > 0 LIMIT 1");
		
		if (!$default_exists) {
			$this->db->set_autoincrement('store', 0);
			$this->model_setting_store->addStore($this->site_config['default_store']);
		}
	}
}
