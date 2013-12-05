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

		$this->store_id                = $store['store_id'];
		$this->data['config_store_id'] = $store['store_id'];
		$this->data['config_url']      = $store['url'];
		$this->data['config_ssl']      = $store['ssl'];

		//TODO: When we sort out configurations, be sure to add in translations for settings!
		//This shoud all be done in the System_Model_Setting class

		//Get the settings specific to the requested store
		$settings = $this->cache->get('setting.config.' . $this->store_id);

		if (!$settings) {
			//TODO: Should use $this->System_Model_Setting->getSetting('config', $this->store_id);
			$settings = $this->queryRows("SELECT * FROM " . DB_PREFIX . "setting WHERE auto_load = 1 AND store_id IN (0, $this->store_id) ORDER BY store_id ASC", 'key');

			foreach ($settings as &$setting) {
				$setting = $setting['serialized'] ? unserialize($setting['value']) : $setting['value'];
			}
			unset($setting);

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
			} else {
				//Resolve Store ID
				if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
					$scheme = 'https://';
					$field  = 'ssl';
				} else {
					$scheme = 'http://';
					$field  = 'url';
				}

				$url = $scheme . str_replace('www.', '', $_SERVER['HTTP_HOST']) . rtrim(dirname($_SERVER['PHP_SELF']), '/.\\') . '/';

				$store = $this->queryRow("SELECT * FROM " . DB_PREFIX . "store WHERE `$field` = '" . $this->escape($url) . "'");

				if (empty($store)) {
					$store_id = $this->queryVar("SELECT `value` FROM " . DB_PREFIX . "setting WHERE `key` = 'config_default_store'");
					$store    = $this->queryRow("SELECT * FROM " . DB_PREFIX . "store WHERE store_id = '$store_id'");
				}
			}
		} else {
			$store = $this->queryRow("SELECT * FROM " . DB_PREFIX . "store WHERE store_id = '$store_id'");
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

		if (!isset($this->data[$key]) || ($store_id !== $this->store_id && $store_id !== 0)) {
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
		if (is_null($store_id)) {
			$store_id = $this->store_id;
		}

		$this->System_Model_Setting->editSetting($group, $data, $store_id, $auto_load);
	}

	public function deleteGroup($group, $store_id = null)
	{
		$this->System_Model_Setting->deleteSetting($group, $store_id);
	}

	private function loadDefaultSites()
	{
		$site_config_file = DIR_SYSTEM . 'site_config.php';

		$_ = array();

		require_once($site_config_file);

		$this->site_config = $_;
	}

	//TODO: Need to rethink this site config. At very least move store model into system directory.
	public function run_site_config()
	{
		$default_exists = $this->queryVar("SELECT COUNT(*) as total FROM " . DB_PREFIX . "store WHERE store_id > 0 LIMIT 1");

		if (!$default_exists) {
			$this->setAutoincrement('store', 0);
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
