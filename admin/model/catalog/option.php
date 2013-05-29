<?php
class ModelCatalogOption extends Model 
{
	public function addOption($data)
	{
		
		$option_id = $this->insert('option', $data);
		
		
		foreach ($data['option_description'] as $language_id => $value) {
			$value['language_id'] = $language_id;
			$value['option_id']	= $option_id;
			
			$this->insert('option_description', $value);
		}

		if (isset($data['option_value'])) {
			foreach ($data['option_value'] as $option_value) {
				$option_value['option_id'] = $option_id;
				
				$option_value_id = $this->insert('option_value', $option_value);
				
				foreach ($option_value['option_value_description'] as $language_id => $option_value_description) {
					$option_value_description['option_value_id'] = $option_value_id;
					$option_value_description['language_id']	= $language_id;
					$option_value_description['option_id']		= $option_id;
					
					$this->insert('option_value_description', $option_value_description);
				}
			}
		}
		$this->cache->delete('option');
	}
	
	public function editOption($option_id, $data)
	{
		$this->update('option', $data, array('option_id'=>$option_id));
		
		$this->delete('option_description', array('option_id'=>$option_id));

		foreach ($data['option_description'] as $language_id => $value) {
			$value['language_id'] = $language_id;
			$value['option_id']	= $option_id;
			
			$this->insert('option_description', $value);
		}
		
		$this->delete('option_value_description', array('option_id'=>$option_id));
		
		if (isset($data['option_value'])) {
			foreach ($data['option_value'] as $option_value) {
				$option_value['option_id'] = $option_id;
				
				if ($option_value['option_value_id']) {
					$this->update('option_value', $option_value, $option_value['option_value_id']);
					$option_value_id = $option_value['option_value_id'];
				}
				else {
					$option_value_id = $this->insert('option_value', $option_value);
				}
				
				foreach ($option_value['option_value_description'] as $language_id => $option_value_description) {
					$option_value_description['option_value_id'] = $option_value_id;
					$option_value_description['language_id']	= $language_id;
					$option_value_description['option_id']		= $option_id;
					
					$this->insert('option_value_description', $option_value_description);
				}
			}
		}
		
		$this->cache->delete('option');
	}
	
	public function deleteOption($option_id)
	{
		$this->delete('option', $option_id);
		$this->delete('option_description', array('option_id'=>$option_id));
		$this->delete('option_value', array('option_id'=>$option_id));
		$this->delete('option_value_description', array('option_id'=>$option_id));
		
		$this->cache->delete('option');
	}
	
	public function getOption($option_id)
	{
		$query = $this->query("SELECT * FROM `" . DB_PREFIX . "option` o LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE o.option_id = '" . (int)$option_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		
		return $query->row;
	}
		
	public function getOptions($data = array()) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "option` o LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE od.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		
		if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
			$sql .= " AND LCASE(od.name) LIKE '%" . $this->db->escape(strtolower($data['filter_name'])) . "%'";
		}

		$sort_data = array(
			'od.name',
			'o.type',
			'o.sort_order'
		);
		
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY od.name";
		}
		
		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}
		
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}
		
			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
		
		$query = $this->query($sql);

		return $query->rows;
	}
	
	public function getOptionDescriptions($option_id)
	{
		$option_data = array();
		
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "option_description WHERE option_id = '" . (int)$option_id . "'");
				
		foreach ($query->rows as $result) {
			$option_data[$result['language_id']] = $result;
		}
		
		return $option_data;
	}
	
	public function getOptionValues($option_id)
	{
		$option_value_data = array();
		
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "option_value ov LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE ov.option_id = '" . (int)$option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY ov.sort_order ASC");
		
		return $query->rows;
	}
	
	public function getOptionValueDescriptions($option_id)
	{
		$options = array(
			'order_by'=>'sort_order ASC'
		);
		
		$query = $this->get('option_value','*', array('option_id'=>$option_id), $options);
		
		foreach ($query->rows as &$option_value) {
			
			$description_query = $this->get('option_value_description', '*', array('option_value_id'=>$option_value['option_value_id']));
			
			foreach ($description_query->rows as $description) {
				$option_value['option_value_description'][$description['language_id']] = $description;
			}
		}
		
		return $query->rows;
	}

	public function getTotalOptions()
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "option`");
		
		return $query->row['total'];
	}
}
