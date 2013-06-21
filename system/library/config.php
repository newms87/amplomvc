<?php
class Config extends Library
{
	private $data = array();
	private $store_id;
	private $site_config;
	
	public function __construct($registry, $store_id = null)
	{
		parent::__construct($registry);
		
		//self assigning so we can use config immediately!
		$this->registry->set('config', $this);
		
		$this->load_site_config();
		
		//If we only have a store_id, get the store info	
		$store = $this->load_store($store_id);
		
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
		
		$this->checkForUpdates();
	}
	
  	public function get($key)
  	{
		return isset($this->data[$key]) ? $this->data[$key] : null;
  	}
	
	public function set($key, $value)
	{
		$this->data[$key] = $value;
  	}
	
	public function isAdmin()
	{
		return defined("IS_ADMIN");
	}
	
	private function load_store($store_id = null)
	{
		if (is_null($store_id)) {
			//TODO: How do we handle different domains for admin? Invalid domains makes DB sync difficult...
			if ($this->isAdmin()) {
				return $this->site_config['admin_store'];
			}
			else {
				//Resolve Store ID
				if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
					$scheme = 'https://';
					$field = 'ssl';
				}
				else {
					$scheme = 'http://';
					$field = 'url';
				}
				
				$url = $scheme . str_replace('www.', '', $_SERVER['HTTP_HOST']) . rtrim(dirname($_SERVER['PHP_SELF']), '/.\\') . '/';
			
				$store = $this->db->query_row("SELECT * FROM " . DB_PREFIX . "store WHERE `$field` = '" . $this->db->escape($url) . "'");
				
				if (empty($store)) {
					$store_id = $this->db->query_var("SELECT `value` FROM " . DB_PREFIX . "setting WHERE `key` = 'config_default_store'");
					$store = $this->db->query_row("SELECT * FROM " . DB_PREFIX . "store WHERE store_id = '$store_id'");
				}
			}
		} else {
			$store = $this->db->query_row("SELECT * FROM " . DB_PREFIX . "store WHERE store_id = '$store_id'");
		}
		
		if (!empty($store)) {
			return $store;
		}
		
		return $this->site_config['default_store'];
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
		$this->Model_Setting_Setting->editSettingKey($group, $key, $data, $this->store_id, $auto_load);
	}
	
	public function save_group($group, $data, $auto_load = true)
	{
		$this->Model_Setting_Setting->editSetting($group, $data, $this->store_id, $auto_load);
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
		$admin_store = $this->site_config['admin_store'];
		
		$admin_exists = $this->db->query_var("SELECT COUNT(*) as total FROM " . DB_PREFIX . "store WHERE store_id = 0 AND `url` ='" . $this->db->escape($admin_store['url']) . "' AND `ssl` = '" . $this->db->escape($admin_store['ssl']) . "'");
		
		if (!$admin_exists) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "store WHERE store_id = 0");
			
			$this->db->query("SET GLOBAL sql_mode='NO_AUTO_VALUE_ON_ZERO'");
			$this->db->query("SET SESSION sql_mode='NO_AUTO_VALUE_ON_ZERO'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "store SET " . $this->db->get_insert_string($this->site_config['admin_store']));
		}
		
		$default_exists = $this->db->query_var("SELECT COUNT(*) as total FROM " . DB_PREFIX . "store WHERE store_id > 0 LIMIT 1");
		
		if (!$default_exists) {
			$this->db->set_autoincrement('store', 0);
			$this->Model_Setting_Store->addStore($this->site_config['default_store']);
		}
	}
	
	public function checkForUpdates()
	{
		$version = $this->get('ac_version');
		
		if ($version !== VERSION && $this->config->get('auto_update')) {
			$this->language->system('config');
			$this->message->add('notify', $this->_('notify_update', $version, VERSION));

			$this->System_Update->update(VERSION);
		}
	}
}
