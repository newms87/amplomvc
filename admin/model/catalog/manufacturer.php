<?php
class Admin_Model_Catalog_Manufacturer extends Model
{
	public function addManufacturer($data)
	{
		if (empty($data['date_active'])) {
			$data['date_active'] = DATETIME_ZERO;
		}
		
		if (empty($data['date_expires'])) {
			$data['date_expires'] = DATETIME_ZERO;
		}

		$manufacturer_id = $this->insert('manufacturer', $data);
		
		$vendor_id = $this->generate_vendor_id(array('id'=>$manufacturer_id,'name'=>$data['name']));
		$this->update('manufacturer', array('vendor_id'=>$vendor_id), array('manufacturer_id'=>$manufacturer_id));
		
		if (isset($data['stores'])) {
			foreach ($data['stores'] as $store_id) {
				$store_data = array(
					'manufacturer_id' => $manufacturer_id,
					'store_id'		=> $store_id
				);
				
				$this->insert('manufacturer_to_store', $store_data);
			}
		}
		
		if (!empty($data['keyword'])) {
			$this->url->setAlias($data['keyword'], 'product/manufacturer', 'manufacturer_id=' . (int)$manufacturer_id);
		}
		
		if (!empty($data['translations'])) {
			$this->translation->setTranslations('manufacturer', $manufacturer_id, $data['translations']);
		}
		
		$this->cache->delete('manufacturer');
	}
	
	public function editManufacturer($manufacturer_id, $data)
	{
		if (!$data['date_active']) {
			$data['date_active'] = DATETIME_ZERO;
		}
		
		if (!$data['date_expires']) {
				$data['date_expires'] = DATETIME_ZERO;
		}
		
		$this->update('manufacturer', $data, array('manufacturer_id'=>$manufacturer_id));
		
		$this->delete('manufacturer_to_store', array('manufacturer_id'=>$manufacturer_id));

		if (isset($data['stores'])) {
			foreach ($data['stores'] as $store_id) {
				$values = array(
					'manufacturer_id' => $manufacturer_id,
					'store_id'		=> $store_id
				);
				
				$this->insert('manufacturer_to_store', $values);
			}
		}
				
		if (!empty($data['keyword'])) {
			$this->url->setAlias($data['keyword'], 'product/manufacturer', 'manufacturer_id=' . (int)$manufacturer_id);
		} else {
			$this->url->removeAlias('product/manufacturer', 'manufacturer_id=' . (int)$manufacturer_id);
		}
		
		if (!empty($data['translations'])) {
			$this->translation->setTranslations('manufacturer', $manufacturer_id, $data['translations']);
		}

		$this->cache->delete('manufacturer');
	}

	public function updateField($manufacturer_id, $data)
	{
		$this->insert('manufacturer', $data, $manufacturer_id);
	}
	
	public function copyManufacturer($manufacturer_id)
	{
		$manufacturer = $this->getManufacturer($manufacturer_id);
		
		$manufacturer['keyword'] = '';
		
		$manufacturer['stores'] = $this->getManufacturerStores($manufacturer_id);
		
		$manufacturer['translations'] = $this->translation->get_translations('manufacturer', $manufacturer_id);
		
		$this->addManufacturer($manufacturer);
	}
	
	public function deleteManufacturer($manufacturer_id)
	{
		$this->delete('manufacturer', array('manufacturer_id'=>$manufacturer_id));
		$this->delete('manufacturer_to_store', array('manufacturer_id'=>$manufacturer_id));
		
		$this->url->removeAlias('product/manufacturer', 'manufacturer_id=' . (int)$manufacturer_id);
		
		$this->translation->delete('manufacturer', $manufacturer_id);
		
		$this->cache->delete('manufacturer');
	}
	
	public function generate_vendor_id($data)
	{
		$n = explode(' ', strtolower($data['name']), 2);
		$f = $n[0];
		$l = count($n)>1?$n[1][0]:$f[1];
		return sprintf('%04d',$data['id']) . '-' . (sprintf('%02d',ord($f)-96)) . (sprintf('%02d',ord($l)-96));
	}
	
	public function generate_url($manufacturer_id, $name)
	{
		$url = $this->Model_Setting_UrlAlias->format_url($name);
		$orig = $url;
		$count = 2;
		
		$url_alias = $manufacturer_id?$this->Model_Setting_UrlAlias->getUrlAliasByRouteQuery('product/manufacturer/product', "manufacturer_id=$manufacturer_id"):null;
		
		$test = $this->Model_Setting_UrlAlias->getUrlAliasByKeyword($url);
		while (!empty($test) && $test['url_alias_id'] != $url_alias['url_alias_id']) {
			$url = $orig . '-' . $count++;
			$test = $this->Model_Setting_UrlAlias->getUrlAliasByKeyword($url);
		}
		return $url;
	}
	
	public function getManufacturer($manufacturer_id)
	{
		return $this->queryRow("SELECT * FROM " . DB_PREFIX . "manufacturer WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
	}
	
	public function getManufacturers($data = array(), $select = '*', $total = false) {
		//Select
		if ($total) {
			$select = 'COUNT(*) as total';
		} elseif (empty($select)) {
			$select = '*';
		}
		
		//From
		$from = DB_PREFIX . "manufacturer m";
		
		//Where
		$where = "1";
		
		if (isset($data['name'])) {
			$where .= " AND LCASE(`name`) like '%" . $this->db->escape(strtolower($data['name'])) . "%'";
		}
		
		if (isset($data['status'])) {
			$where .= " AND status = '" . (int)$data['status'] . "'";
		}
			
		if (isset($data['manufacturer_ids'])) {
			$where .= " AND manufacturer_id IN(" . implode(',', $data['manufacturer_ids']) . ")";
		}
		
		//Order and Limit
		if (!$total) {
			if (!empty($data['sort']) && strpos($data['sort'], '__image_sort__') === 0) {
				if (!$this->db->hasColumn('manufacturer', $data['sort'])) {
					$this->extend->enable_image_sorting('manufacturer', str_replace('__image_sort__', '', $data['sort']));
				}
			}
			
			$order = $this->extract_order($data);
			$limit = $this->extract_limit($data);
		} else {
			$order = '';
			$limit = '';
		}
		
		//The Query
		$query = "SELECT $select FROM $from WHERE $where $order $limit";
		
		$result = $this->query($query);
		
		if ($total) {
			return $result->row['total'];
		}
		
		return $result->rows;
	}
	
	public function getManufacturerStores($manufacturer_id)
	{
		$manufacturer_store_data = array();
		
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "manufacturer_to_store WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		foreach ($query->rows as $result) {
			$manufacturer_store_data[] = $result['store_id'];
		}
		
		return $manufacturer_store_data;
	}
	
	public function getTotalManufacturers($data)
	{
		return $this->getManufacturers($data, '', true);
	}
}
