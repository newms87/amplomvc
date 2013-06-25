<?php
class Catalog_Model_Setting_Setting extends Model 
{
	public function getSetting($group, $store_id = 0)
	{
		$data = array();
		
		$settings = $this->queryRows("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `group` = '" . $this->db->escape($group) . "'");
		
		foreach ($settings as $setting) {
			$value = $setting['serialized'] ? unserialize($setting['value']) : $setting['value'];
			
			if (is_array($value)) {
				foreach ($value as $entry_key => $entry) {
					$this->translation->translate($value['key'], $entry_key, $entry);
				}
			}
			elseif(is_string($value)) {
				$this->translation->translate('setting', $setting['setting_id'], array($setting['key'] => $value));
			}
			
			$data[$setting['key']] = $value;
		}
		
		return $data;
	}
	
	public function getSettingKey($group, $key, $store_id = 0)
	{
		$setting = $this->queryRow("SELECT * FROM " . DB_PREFIX . "setting WHERE `group` = '" . $this->db->escape($group) . "' AND `key` = '" . $this->db->escape($key) . "' AND store_id IN (0, " . (int)$store_id . ")");
		
		$value = $setting['serialized'] ? unserialize($setting['value']) : $setting['value'];
		
		if (is_array($value)) {
			foreach ($value as $entry_key => $entry) {
				$this->translation->translate($value['key'], $entry_key, $entry);
			}
		}
		elseif(is_string($value)) {
			$this->translation->translate('setting', $setting['setting_id'], array($setting['key'] => $value));
		}
		
		return $value;
	}
	
	public function editSetting($group, $data, $store_id = 0, $auto_load = true)
	{
		foreach ($data as $key => $value) {
			$this->editSettingKey($group, $key, $value, $store_id, $auto_load);
		}
	}
	
	public function editSettingKey($group, $key = null, $value = array(), $store_id = 0, $auto_load = true){
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
		
		if (is_array($value)) {
			foreach ($value as $entry_key => $entry) {
				if (!empty($entry['translations'])) {
					$this->translation->set_translations($key, $entry_key, $entry['translations']);
				}
			}
		}
		else {
			if (!empty($value['translations'])) {
				$this->translation->set_translations('setting', $setting_id, $value['translations']);
			}
		}
		$this->cache->delete('setting');
		$this->cache->delete('store');
		$this->cache->delete('theme');
		
		return $setting_id;
	}
}