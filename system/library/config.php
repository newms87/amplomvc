<?php

class Config extends Library
{
	private $data = array();
	private $translate = true;

	public function __construct()
	{
		parent::__construct();

		//self assigning so we can use config immediately!
		global $registry;
		$registry->set('config', $this);

		$this->setSite($this->route->getSite());

		if (!defined('AMPLO_AUTO_UPDATE') || AMPLO_AUTO_UPDATE) {
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

	public function setSite($site)
	{
		//TODO: When we sort out configurations, be sure to add in translations for settings!

		$settings = cache('setting.config');

		if (!$settings) {
			//TODO: Should use $this->loadGroup('config');
			$settings = $this->queryRows("SELECT * FROM " . self::$tables['setting'] . " WHERE auto_load = 1", 'key');

			foreach ($settings as &$setting) {
				$setting = $setting['serialized'] ? unserialize($setting['value']) : $setting['value'];
			}
			unset($setting);

			cache('setting.config', $settings);
		}

		if (!$settings && IS_ADMIN && $this->route->getPath() !== 'admin/settings/restore_defaults') {
			//message('success', _l("Welcome to Amplo MVC! Your installation has been successfully installed so you're ready to get started."));
			//TODO: Should still set defaults here
			//redirect('admin/settings/restore_defaults');
		}

		$this->data = $settings;

		$this->data['site_id']  = !empty($site['store_id']) ? $site['store_id'] : 0;
		$this->data['store_id'] = $this->data['site_id'];
		$this->data['name']     = !empty($site['name']) ? $site['name'] : 'No Site';
		$this->data['url']      = !empty($site['url']) ? $site['url'] : URL_SITE;
		$this->data['ssl']      = !empty($site['ssl']) ? $site['ssl'] : HTTPS_SITE;
	}

	public function &all()
	{
		return $this->data;
	}

	public function has($key)
	{
		return isset($this->data[$key]);
	}

	public function load($group, $key)
	{
		if (!isset($this->data[$key])) {
			$setting = $this->queryRow("SELECT * FROM " . self::$tables['setting'] . " WHERE `group` = '" . $this->escape($group) . "' AND `key` = '" . $this->escape($key) . "'");

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

	public function save($group, $key, $value, $auto_load = true)
	{
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
			'auto_load'  => $auto_load ? 1 : 0,
		);

		$where = array(
			'group' => $group,
			'key'   => $key,
		);

		$this->delete('setting', $where);

		$setting_id = $this->insert('setting', $values);

		if ($auto_load) {
			clear_cache('setting');
			clear_cache('theme');
		}

		$this->config->set($key, $value);

		clear_cache("setting.$group");

		return $setting_id;
	}

	public function remove($group, $key)
	{
		$where = array(
			'group' => $group,
			'key'   => $key,
		);

		$this->delete('setting', $where);
	}

	public function loadGroup($group)
	{
		static $loaded_groups = array();

		if (!isset($loaded_groups[$group])) {
			$data = cache("setting.$group");

			if ($data === null) {
				$data = array();

				$settings = $this->queryRows("SELECT * FROM " . self::$tables['setting'] . " WHERE `group` = '" . $this->escape($group) . "'");

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

				cache("setting.$group", $data);
			}

			$this->data += $data;

			$loaded_groups[$group] = $data;
		}

		return $loaded_groups[$group];
	}

	public function saveGroup($group, $data, $auto_load = true)
	{
		foreach ($data as $key => $value) {
			$this->save($group, $key, $value, $auto_load);
		}

		return true;
	}

	public function deleteGroup($group)
	{
		$values = array(
			'group' => $group
		);

		$store_query = '';

		$settings = $this->queryRows("SELECT * FROM " . self::$tables['setting'] . " WHERE `group` = '" . $this->escape($group) . "' $store_query");

		foreach ($settings as $setting) {
			if ($setting['translate']) {
				if ($setting['serialized']) {
					$this->translation->deleteAll($setting['key']);
				} else {
					$this->translation->deleteTranslation('setting', $setting['setting_id']);
				}
			}
		}

		clear_cache("setting.$group");

		return $this->delete('setting', $values);
	}

	public function runSiteConfig()
	{
		$default_exists = $this->queryVar("SELECT COUNT(*) as total FROM " . self::$tables['store']);

		if (!$default_exists) {
			$store = array(
				'name'   => 'Amplo MVC',
				'prefix' => DB_PREFIX,
				'url'    => HTTP_SITE,
				'ssl'    => HTTPS_SITE,
			);

			$this->db->setAutoIncrement('store', 0);
			$this->Model_Setting_Store->save(null, $store);
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
