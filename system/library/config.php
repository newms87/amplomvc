<?php

class Config extends Library
{
	private $data = array();
	private $store_id;
	private $site_config;
	private $translate = true;

	public function __construct($store_id = null)
	{
		parent::__construct();

		//self assigning so we can use config immediately!
		global $registry;
		$registry->set('config', $this);

		$this->loadDefaultSites();

		$this->data     = $this->getStore($store_id);
		$this->store_id = $this->data['store_id'];

		//TODO: When we sort out configurations, be sure to add in translations for settings!

		//Get the settings specific to the requested store
		$settings = cache('setting.config.' . $this->store_id);

		if (!$settings) {
			//TODO: Should use $this->config->loadGroup('config', $this->store_id);
			$settings = $this->queryRows("SELECT * FROM " . DB_PREFIX . "setting WHERE auto_load = 1 AND store_id IN (0, $this->store_id) ORDER BY store_id ASC", 'key');

			foreach ($settings as &$setting) {
				$setting = $setting['serialized'] ? unserialize($setting['value']) : $setting['value'];
			}
			unset($setting);

			cache('setting.config.' . $this->store_id, $settings);
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

	public function getDefaultStore()
	{
		return $this->getStore(option('config_default_store'));
	}

	public function getStore($store_id = null)
	{
		if (is_null($store_id)) {
			$store_id = option('store_id');
		}

		$stores = cache('store.all');

		if (is_null($stores)) {
			$stores = $this->Model_Setting_Store->getStores();

			cache('store.all', $stores);
		}

		$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
		$url    = $scheme . $_SERVER['HTTP_HOST'] . '/' . trim($_SERVER['REQUEST_URI'], '/');

		foreach ($stores as $s) {
			if ($store_id) {
				if ($store_id == $s['store_id']) {
					$store = $s;
					break;
				}
			} else {
				if (strpos($url, trim($s['url'], '/ ')) === 0 || strpos($url, trim($s['ssl'], '/ ')) === 0) {
					$store = $s;
					break;
				}
			}
		}

		if (empty($store)) {
			$store = array(
				'store_id' => 0,
				'name'     => "Default",
				'url'      => HTTP_SITE,
				'ssl'      => HTTPS_SITE,
			);
		}

		return $store;
	}

	public function all()
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

		if (!isset($this->data[$key]) || ($store_id !== $this->store_id)) {
			$setting = $this->queryRow("SELECT * FROM " . DB_PREFIX . "setting WHERE `group` = '" . $this->escape($group) . "' AND `key` = '" . $this->escape($key) . "' AND store_id IN (0, " . (int)$store_id . ") ORDER BY store_id ASC");

			if ($setting) {
				$value = $setting['serialized'] ? unserialize($setting['value']) : $setting['value'];

				//Translate setting
				if ($this->translate && $setting['translate']) {
					if (is_array($value)) {
						foreach ($value as $entry_key => $entry) {
							$this->translation->translate($key, $entry_key, $entry);
						}
					} elseif (is_string($value)) {
						$this->translation->translate('setting', $setting['setting_id'], array($setting['key'] => $value));
					}
				}
			} else {
				$value = null;
			}

			$this->data[$key] = $value;
		}

		return $this->data[$key];
	}

	public function save($group, $key, $value, $store_id = null, $auto_load = true)
	{
		if (is_null($store_id)) {
			$store_id = $this->store_id;
		}

		$translate = 0;

		//Handle Translations
		if (is_array($value)) {
			foreach ($value as $entry_key => $entry) {
				if (is_array($entry) && isset($entry['translations'])) {
					//Clear all translations for this field
					if (!$translate) {
						$this->translation->deleteAll($key);
					}

					$this->translation->setTranslations($key, $entry_key, $entry['translations']);

					unset($value[$entry_key]['translations']);

					$translate = 1;
				}
			}
		}

		//Serialize if necessary
		if (_is_object($value)) {
			$entry_value = serialize($value);
			$serialized  = 1;
		} else {
			$entry_value = $value;
			$serialized  = 0;
		}

		$values = array(
			'group'      => $group,
			'key'        => $key,
			'value'      => $entry_value,
			'serialized' => $serialized,
			'translate'  => $translate,
			'store_id'   => $store_id,
			'auto_load'  => $auto_load ? 1 : 0,
		);

		$where = array(
			'group'    => $group,
			'key'      => $key,
			'store_id' => $store_id,
		);

		$this->delete('setting', $where);

		$setting_id = $this->insert('setting', $values);

		if ($auto_load) {
			$this->cache->delete('setting');
			$this->cache->delete('store');
			$this->cache->delete('theme');
		}

		$this->config->set($key, $value);

		$this->cache->delete("setting.$group");

		return $setting_id;
	}

	public function remove($group, $key, $store_id = null)
	{
		$where = array(
			'group' => $group,
			'key'   => $key,
		);

		if ($store_id) {
			$where['store_id'] = $store_id;
		}

		$this->delete('setting', $where);
	}

	public function loadGroup($group, $store_id = null)
	{
		static $loaded_groups = array();

		if (is_null($store_id)) {
			$store_id = $this->store_id;
		}

		if (!isset($loaded_groups[$group][$store_id])) {
			$data = cache("setting.$group.$store_id");

			if (is_null($data)) {
				$data = array();

				$settings = $this->queryRows("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id IN (0, " . (int)$store_id . ") AND `group` = '" . $this->escape($group) . "' ORDER BY store_id ASC");

				foreach ($settings as $setting) {
					$value = $setting['serialized'] ? unserialize($setting['value']) : $setting['value'];

					//Translate Settings
					if ($this->translate && $setting['translate']) {
						if (is_array($value)) {
							$this->translation->translateAll($setting['key'], false, $value);
						} elseif (is_string($value)) {
							$setting_value = array($setting['key'] => $value);

							$this->translation->translate('setting', $setting['setting_id'], $setting_value);

							$value = $setting_value[$setting['key']];
						}
					}

					$data[$setting['key']] = $value;
				}

				cache("setting.$group.$store_id", $data);
			}

			$this->data += $data;

			$loaded_groups[$group][$store_id] = $data;
		}

		return $loaded_groups[$group][$store_id];
	}

	public function saveGroup($group, $data, $store_id = null, $auto_load = true)
	{
		foreach ($data as $key => $value) {
			$this->save($group, $key, $value, $store_id, $auto_load);
		}
	}

	public function deleteGroup($group, $store_id = null)
	{
		$values = array(
			'group' => $group
		);

		$store_query = '';

		if (!is_null($store_id)) {
			$values['store_id'] = $store_id;
			$store_query        = "AND store_id = '" . (int)$store_id . "'";
		}

		$settings = $this->queryRows("SELECT * FROM " . DB_PREFIX . "setting WHERE `group` = '" . $this->escape($group) . "' $store_query");

		foreach ($settings as $setting) {
			if ($setting['translate']) {
				if ($setting['serialized']) {
					$this->translation->deleteAll($setting['key']);
				} else {
					$this->translation->deleteTranslation('setting', $setting['setting_id']);
				}
			}
		}

		$this->delete('setting', $values);

		$this->cache->delete("setting.$group");
	}

	public function addStore($data)
	{
		return $this->insert("store", $data);
	}

	public function editStore($store_id, $data)
	{
		$this->update("store", $store_id, $data);

		$this->cache->delete('store');
		$this->cache->delete('theme');
		$this->cache->delete('setting');
	}

	public function deleteStore($store_id)
	{
		$this->delete("store", $store_id);

		$this->cache->delete('store');
		$this->cache->delete('theme');
		$this->cache->delete('setting');
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
			$this->db->setAutoIncrement('store', 0);
			$this->Model_Setting_Store->addStore($this->site_config['default_store']);
		}
	}

	public function checkForUpdates()
	{
		$version = !empty($this->data['AMPLO_VERSION']) ? $this->data['AMPLO_VERSION'] : null;

		if ($version !== AMPLO_VERSION) {
			message('notify', _l("The database version %s was out of date and has been updated to version %s", $version, AMPLO_VERSION));

			$this->System_Update->updateSystem(AMPLO_VERSION);
		}
	}
}
