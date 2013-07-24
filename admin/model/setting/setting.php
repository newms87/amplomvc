<?php
class Admin_Model_Setting_Setting extends Model
{
	public function getSetting($group, $store_id = 0)
	{
		$where = array(
			'store_id'  => $store_id,
			'group'	=> $group,
		);
		
		$query = $this->get('setting', '*',  $where);
		
		$data = array();
		
		foreach ($query->rows as $result) {
			if (!$result['serialized']) {
				$data[$result['key']] = $result['value'];
			} else {
				$data[$result['key']] = unserialize($result['value']);
			}
		}

		return $data;
	}
	
	public function editSetting($group, $data, $store_id = 0, $auto_load = true)
	{
		$this->deleteSetting($group, $store_id);

		foreach ($data as $key => $value) {
			$values = array(
				'store_id'  => $store_id,
				'group'	=> $group,
				'key'		=>$key,
				'auto_load' => $auto_load ? 1 : 0
			);
			
			if (is_array($value) || is_object($value)) {
				$values['value'] = serialize($value);
				$values['serialized'] = 1;
			}
			else {
				$values['value'] = $value;
				$values['serialized'] = 0;
			}
			
			$this->insert('setting', $values);
		}
		
		$this->cache->delete('setting');
		$this->cache->delete('store');
	}
	
	public function editSettingKey($group, $key = null, $value = array(), $store_id = 0, $auto_load = true){
		if (is_array($value) || is_object($value)) {
			$value = serialize($value);
			$serialized = 1;
		}
		else {
			$serialized = 0;
		}
		
		$values = array(
			'group' => $group,
			'key' => $key,
			'value' => $value,
			'serialized' => $serialized,
			'store_id' => $store_id,
			'auto_load' => $auto_load ? 1 : 0
		);
		
		$this->delete('setting',"`group` = '$group' AND `key` = '$key' AND store_id = '$store_id'");
		
		$this->insert('setting',  $values);
		
		$this->cache->delete('setting');
		$this->cache->delete('store');
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
