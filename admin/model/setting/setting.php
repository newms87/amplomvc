<?php 
class ModelSettingSetting extends Model {
	public function getSetting($group, $store_id = 0) {
      $where = array(
         'store_id'  => $store_id,
         'group'     => $group,
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
	
	public function editSetting($group, $data, $store_id = 0) {
	   $this->deleteSetting($group, $store_id);

		foreach ($data as $key => $value) {
		   $values = array(
		      'store_id'  => $store_id,
            'group'     => $group,
            'key'       =>$key
           );
         
         if(is_array($value) || is_object($value)){
            $values['value'] = serialize($value);
            $values['serialized'] = 1;
         }
         else{
            $values['value'] = $value;
            $values['serialized'] = 0;
         }
         
      	$this->insert('setting', $values);
		}
      
      $this->cache->delete('template');
	}
	
	public function deleteSetting($group, $store_id = 0) {
	   $values = array(
        'store_id'=>$store_id,
        'group'=>$group
       );
       
      $this->delete('setting', $values);
      
      $this->cache->delete('template');
	}
}
