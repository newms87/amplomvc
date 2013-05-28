<?php
class ModelCatalogManufacturer extends Model {
	public function getManufacturer($manufacturer_id) {
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "manufacturer m LEFT JOIN " . DB_PREFIX . "manufacturer_to_store m2s ON (m.manufacturer_id = m2s.manufacturer_id) WHERE m.status='1' AND m.manufacturer_id = '" . (int)$manufacturer_id . "' AND m2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");
		return $query->row;
	}
	
	public function getManufacturerAndTeaser($manufacturer_id) {
		$query = $this->query("SELECT m.*, md.teaser FROM " . DB_PREFIX . "manufacturer m LEFT JOIN " . DB_PREFIX . "manufacturer_description md ON(m.manufacturer_id = md.manufacturer_id) LEFT JOIN " . DB_PREFIX . "manufacturer_to_store m2s ON (m.manufacturer_id = m2s.manufacturer_id) WHERE m.status='1' AND m.manufacturer_id = '" . (int)$manufacturer_id . "' AND m2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");
		return $query->row;
	}
	
	public function getManufacturerURL($manufacturer_id){
		$query = $this->query("SELECT keyword FROM " .DB_PREFIX . "manufacturer m WHERE manufacturer_id='$manufacturer_id'");
		return isset($query->row)?$this->url->site($query->row['keyword']):null;
	}
	
	public function getManufacturerByKeyword($keyword){
		$query = $this->query("SELECT manufacturer_id FROM " . DB_PREFIX . "manufacturer WHERE keyword='$keyword'");
		return isset($query->row)?$query->row['manufacturer_id']:null;
	}

	public function getManufacturers($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "manufacturer m LEFT JOIN " . DB_PREFIX . "manufacturer_to_store m2s ON (m.manufacturer_id = m2s.manufacturer_id) WHERE m.status='1', m2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";
			
			$sort_data = array(
				'name',
				'sort_order'
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
			$manufacturer_data = $this->cache->get('manufacturer.' . (int)$this->config->get('config_store_id'));
		
			if (!$manufacturer_data) {
				$query = $this->query("SELECT * FROM " . DB_PREFIX . "manufacturer m LEFT JOIN " . DB_PREFIX . "manufacturer_to_store m2s ON (m.manufacturer_id = m2s.manufacturer_id) WHERE m.status='1' AND m2s.store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY name");
	
				$manufacturer_data = $query->rows;
				
				$this->cache->set('manufacturer.' . (int)$this->config->get('config_store_id'), $manufacturer_data);
			}
		
			return $manufacturer_data;
		}
	}
}
