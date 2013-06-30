<?php
class Admin_Model_Setting_Setting extends Model 
{
	public function getSetting($group, $store_id = 0)
	{
		$settings = $this->queryRows("SELECT * FROM " . DB_PREFIX . "setting WHERE `group` = '" . $this->db->escape($group) . "' AND store_id IN (0, " . (int)$store_id . ")");
		
		$data = array();
		
		foreach ($settings as $setting) {
			if (!$setting['serialized']) {
				$data[$setting['key']] = $setting['value'];
			} else {
				$data[$setting['key']] = unserialize($setting['value']);
			}
		}

		return $data;
	}
	
	public function getSettingKey($group, $key, $store_id = 0)
	{
		$setting = $this->queryRow("SELECT * FROM " . DB_PREFIX . "setting WHERE `group` = '" . $this->db->escape($group) . "' AND `key` = '" . $this->db->escape($key) . "' AND store_id IN (0, " . (int)$store_id . ")");
		
		if ($setting) {
			return $setting['serialized'] ? unserialize($setting['value']) : $setting['value'];
		}
		
		return null;
	}
	
	public function editSetting($group, $data, $store_id = 0, $auto_load = true)
	{
		foreach ($data as $key => $value) {
			$this->editSettingKey($group, $key, $value, $store_id, $auto_load);
		}
	}
	
	public function editSettingKey($group, $key = null, $value = array(), $store_id = 0, $auto_load = true)
	{
		//Handle Translations
		if (is_array($value)) {
			foreach ($value as $entry_key => $entry) {
				if (is_array($entry) && isset($entry['translations'])) {
					$this->translation->set_translations($key, $entry_key, $entry['translations']);
					unset($value[$entry_key]['translations']);
				}
			}
		}
		
		//Serialize if necessary
		if (is_array($value) || is_object($value)) {
			$entry_value = serialize($value);
			$serialized = 1;
		} else {
			$serialized = 0;
			$entry_value = $value;
		}
		
		$values = array(
			'group' => $group,
			'key' => $key,
			'value' => $entry_value,
			'serialized' => $serialized,
			'store_id' => $store_id,
			'auto_load' => $auto_load ? 1 : 0,
		);
		
		$where = array(
			'group' => $group,
			'key' => $key,
			'store_id' => $store_id,
		);
		
		$this->delete('setting', $where);
		
		$setting_id = $this->insert('setting',  $values);
		
		if (!empty($translations)) {
			$this->translation->set_translations('setting', $setting_id, $translations);
		}
		
		$this->cache->delete('setting');
		$this->cache->delete('store');
		$this->cache->delete('theme');
		
		return $setting_id;
	}
	
	public function deleteSetting($group, $store_id = 0)
	{
		$values = array(
		'store_id'=>$store_id,
		'group'=>$group
		);
		
		$this->delete('setting', $values);
		
		$this->cache->delete('theme');
		$this->cache->delete('setting');
		$this->cache->delete('store');
	}
}
