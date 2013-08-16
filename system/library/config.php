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
		
		$this->loadDefaultSites();
		
		//If we only have a store_id, get the store info
		$store = $this->getStore($store_id);
		
		$this->store_id = $store['store_id'];
		$this->data['config_store_id'] = $store['store_id'];
		$this->data['config_url'] = $store['url'];
		$this->data['config_ssl'] = $store['ssl'];
		
		//TODO: When we sort out configurations, be sure to add in translations for settings!
		//This shoud all be done in the System_Model_Setting class
		
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
		
		if (!empty($this->data['auto_update'])) {
			$this->checkForUpdates();
		}
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
	
	public function getDefaultStore()
	{
		return $this->getStore($this->config->get('config_default_store'));
	}
	
	public function getStore($store_id = null)
	{
		if (is_null($store_id)) {
			//TODO: Admin should be only 1 domain, should not be a store!! We can have different templates for admin,
			//but should always be the same domain etc.. store_id 0 should be all stores.
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
			
				$store = $this->db->queryRow("SELECT * FROM " . DB_PREFIX . "store WHERE `$field` = '" . $this->db->escape($url) . "'");
				
				if (empty($store)) {
					$store_id = $this->db->queryVar("SELECT `value` FROM " . DB_PREFIX . "setting WHERE `key` = 'config_default_store'");
					$store = $this->db->queryRow("SELECT * FROM " . DB_PREFIX . "store WHERE store_id = '$store_id'");
				}
			}
		} else {
			$store = $this->db->queryRow("SELECT * FROM " . DB_PREFIX . "store WHERE store_id = '$store_id'");
		}
		
		if (!empty($store)) {
			return $store;
		}
		
		return $this->site_config['default_store'];
	}
		
	public function get_all()
	{
		return $this->data;
	}
	
	public function has($key)
	{
		return isset($this->data[$key]);
  	}
	
	public function load($group, $key, $store_id = null)
	{
		if (is_null($store_id)) {
			$store_id = $this->store_id;
		}
		
		if (!isset($this->data[$key]) || $store_id !== $this->store_id) {
			$this->data[$key] = $this->System_Model_Setting->getSettingKey($group, $key, $store_id);
		}
		
		return $this->data[$key];
	}
	
	public function save($group, $key, $value, $store_id = null, $auto_load = true)
	{
		if (is_null($store_id)) {
			$store_id = $this->store_id;
		}
		
		$this->System_Model_Setting->editSettingKey($group, $key, $value, $store_id, $auto_load);
	}
	
	public function loadGroup($group, $store_id = null)
	{
		static $loaded_groups = array();
		
		if (is_null($store_id)) {
			$store_id = $this->store_id;
		}

		if (!isset($loaded_groups[$group][$store_id])) {
			$group_data = $this->System_Model_Setting->getSetting($group, $store_id);
		
			$this->data += $group_data;
			
			$loaded_groups[$group][$store_id] = $group_data;
		}
		
		return $loaded_groups[$group][$store_id];
	}
	
	public function saveGroup($group, $data, $store_id = null, $auto_load = true)
	{
		if (!$store_id) {
			$store_id = $this->store_id;
		}
		
		$this->System_Model_Setting->editSetting($group, $data, $store_id, $auto_load);
	}
	
	private function loadDefaultSites()
	{
		$site_config_file = DIR_SYSTEM . 'site_config.php';
		
		$_ = array();
		
		require_once($site_config_file);
		
		$this->site_config = $_;
	}
	
	public function run_site_config()
	{
		$admin_store = $this->site_config['admin_store'];
		
		$admin_exists = $this->db->queryVar("SELECT COUNT(*) as total FROM " . DB_PREFIX . "store WHERE store_id = " . (int)$admin_store['store_id'] . " AND `url` ='" . $this->db->escape($admin_store['url']) . "' AND `ssl` = '" . $this->db->escape($admin_store['ssl']) . "'");
		
		if (!$admin_exists) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "store WHERE store_id = " . (int)$admin_store['store_id']);
			
			$this->db->query("SET GLOBAL sql_mode='NO_AUTO_VALUE_ON_ZERO'");
			$this->db->query("SET SESSION sql_mode='NO_AUTO_VALUE_ON_ZERO'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "store SET " . $this->db->getInsertString($admin_store));
		}
		
		$default_exists = $this->db->queryVar("SELECT COUNT(*) as total FROM " . DB_PREFIX . "store WHERE store_id > 0 LIMIT 1");
		
		if (!$default_exists) {
			$this->db->setAutoincrement('store', 0);
			$this->Model_Setting_Store->addStore($this->site_config['default_store']);
		}
	}
	
	public function checkForUpdates()
	{
		$version = !empty($this->data['ac_version']) ? $this->data['ac_version'] : null;
		
		if ($version !== AC_VERSION) {
			$this->language->system('config');
			$this->message->add('notify', $this->_('notify_update', $version, AC_VERSION));

			$this->System_Update->update(AC_VERSION);
		}
	}
}
