<?php
class Admin_Model_Localisation_Country extends Model
{
	public function addCountry($data)
	{
		$this->query("INSERT INTO " . DB_PREFIX . "country SET name = '" . $this->escape($data['name']) . "', iso_code_2 = '" . $this->escape($data['iso_code_2']) . "', iso_code_3 = '" . $this->escape($data['iso_code_3']) . "', address_format = '" . $this->escape($data['address_format']) . "', postcode_required = '" . (int)$data['postcode_required'] . "', status = '" . (int)$data['status'] . "'");
	
		$this->cache->delete('country');
	}
	
	public function editCountry($country_id, $data)
	{
		$this->query("UPDATE " . DB_PREFIX . "country SET name = '" . $this->escape($data['name']) . "', iso_code_2 = '" . $this->escape($data['iso_code_2']) . "', iso_code_3 = '" . $this->escape($data['iso_code_3']) . "', address_format = '" . $this->escape($data['address_format']) . "', postcode_required = '" . (int)$data['postcode_required'] . "', status = '" . (int)$data['status'] . "' WHERE country_id = '" . (int)$country_id . "'");
	
		$this->cache->delete('country');
	}
	
	public function deleteCountry($country_id)
	{
		$this->query("DELETE FROM " . DB_PREFIX . "country WHERE country_id = '" . (int)$country_id . "'");
		
		$this->cache->delete('country');
	}
	
	public function getCountry($country_id)
	{
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "country WHERE country_id = '" . (int)$country_id . "'");
		
		return $query->row;
	}
		
	public function getCountries($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "country";
			
			$sort_data = array(
				'name',
				'iso_code_2',
				'iso_code_3'
			);
			
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY name";
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
		} else {
			$country_data = $this->cache->get('country');
		
			if (!$country_data) {
				$query = $this->query("SELECT * FROM " . DB_PREFIX . "country ORDER BY name ASC");
	
				$country_data = $query->rows;
			
				$this->cache->set('country', $country_data);
			}

			return $country_data;
		}
	}
	
	public function getTotalCountries()
	{
			$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "country");
		
		return $query->row['total'];
	}
}