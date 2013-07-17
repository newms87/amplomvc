<?php
class System_Model_Setting extends Model 
{
	private $translate = true;
	
	public function translateFields($translate)
	{
		$this->translate = $translate;
	}
	
	public function getSetting($group, $store_id = null)
	{
		if (is_null($store_id)) {
			$store_id = $this->config->get('config_store_id');
		}
		
		$data = array();
		
		$settings = $this->queryRows("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id IN (0, " . (int)$store_id . ") AND `group` = '" . $this->db->escape($group) . "'");
		
		foreach ($settings as $setting) {
			$value = $setting['serialized'] ? unserialize($setting['value']) : $setting['value'];
			
			if ($this->translate && $setting['translate']) {
				if (is_array($value)) {
					$this->translation->translate_all($setting['key'], false, $value);
				}
				elseif(is_string($value)) {
					$setting_value = array($setting['key'] => $value);
					
					$this->translation->translate('setting', $setting['setting_id'], $setting_value);
					
					$value = $setting_value[$setting['key']];
				}
			}
			
			$data[$setting['key']] = $value;
		}

		return $data;
	}
	
	public function getSettingKey($group, $key, $store_id = null)
	{
		if (is_null($store_id)) {
			$store_id = $this->config->get('config_store_id');
		}
		
		$setting = $this->queryRow("SELECT * FROM " . DB_PREFIX . "setting WHERE `group` = '" . $this->db->escape($group) . "' AND `key` = '" . $this->db->escape($key) . "' AND store_id IN (0, " . (int)$store_id . ")");
		
		if ($setting) {
			$value = $setting['serialized'] ? unserialize($setting['value']) : $setting['value'];
			
			if ($this->translate && $setting['translate']) {
				if (is_array($value)) {
					foreach ($value as $entry_key => $entry) {
						$this->translation->translate($key, $entry_key, $entry);
					}
				}
				elseif(is_string($value)) {
					$this->translation->translate('setting', $setting['setting_id'], array($setting['key'] => $value));
				}
			}
		}
		else {
			$value = null;
		}
		
		return $value;
	}
	
	public function editSetting($group, $data, $store_id = 0, $auto_load = true)
	{
		foreach ($data as $key => $value) {
			$this->editSettingKey($group, $key, $value, $store_id, $auto_load);
		}
	}
	
	public function editSettingKey($group, $key = null, $value = array(), $store_id = 0, $auto_load = true)
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
					
					$this->translation->set_translations($key, $entry_key, $entry['translations']);
					
					unset($value[$entry_key]['translations']);
					
					$translate = 1;
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
			'translate' => $translate,
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
		
		$this->cache->delete('setting');
		$this->cache->delete('store');
		$this->cache->delete('theme');
		
		return $setting_id;
	}
	
	public function deleteSetting($group, $store_id = 0)
	{
		$settings = $this->queryRows("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `group` = '" . $this->db->escape($group) . "'");
		
		foreach ($settings as $setting) {
			if ($setting['translate']) {
				if ($setting['serialized']) {
					$this->translation->deleteAll($setting['key']);
				} else {
					$this->translation->delete('setting', $setting['setting_id']);
				}
			}
		}
		
		$values = array(
			'store_id' => $store_id,
			'group' => $group
		);
		
		$this->delete('setting', $values);
		 
		$this->cache->delete('theme');
		$this->cache->delete('setting');
		$this->cache->delete('store');
	}
}
