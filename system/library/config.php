<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC
 * @link http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYING.txt and LICENSE.txt files in the root directory.
 */

class Config extends Library
{
	private $translate = true;

	public function get($key)
	{
		global $_options;

		return isset($_options[$key]) ? $_options[$key] : null;
	}

	public function set($key, $value)
	{
		global $_options;
		$_options[$key] = $value;
	}

	public function has($key)
	{
		global $_options;

		return isset($_options[$key]);
	}

	public function load($group, $key = null)
	{
		global $_options;

		if (!isset($_options[$key])) {
			$setting = $this->queryRow("SELECT * FROM {$this->t['setting']} WHERE `group` = '" . $this->escape($group) . "' AND `key` = '" . $this->escape($key) . "'");

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

			$_options[$key] = $value;
		}

		return $_options[$key];
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

		$this->set($key, $value);

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
		global $_options;
		static $loaded_groups = array();

		if (!isset($loaded_groups[$group])) {
			$data = cache("setting.$group");

			if ($data === null) {
				$data = array();

				$settings = $this->queryRows("SELECT * FROM {$this->t['setting']} WHERE `group` = '" . $this->escape($group) . "'");

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

			$_options += $data;

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

		$settings = $this->queryRows("SELECT * FROM {$this->t['setting']} WHERE `group` = '" . $this->escape($group) . "'");

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
		$default_exists = $this->queryVar("SELECT COUNT(*) as total FROM " . DB_PREFIX . "site");

		if (!$default_exists) {
			$site = array(
				'name'   => 'Amplo MVC',
				'prefix' => DB_PREFIX,
				'url'    => HTTP_SITE,
				'ssl'    => HTTPS_SITE,
			);

			$this->db->setAutoIncrement('site', 0);
			$this->Model_Site->createSite($site);
		}
	}
}
