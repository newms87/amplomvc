<?php
class Admin_Model_Catalog_Manufacturer extends Model 
{
	public function addManufacturer($data)
	{
		if ($this->user->isDesigner()) {
			$data['sort_order'] = 0;
			$data['section_attr'] = 0;
			$data['status'] = 0;
			$data['manufacturer_store'] = array(0,1,2);
			$data['articles'] = null;
			$data['date_active'] = date_create();
			$data['date_expires'] = date_add(new DateTime(), date_interval_create_from_date_string('30 days'));
			$data['keyword'] = $this->Model_Setting_UrlAlias->format_url($data['name']);
			$data['editable'] = 1;
		}
		
		
		if (!$data['date_active']) {
				$data['date_active'] = DATETIME_ZERO;
		}
		
		if (!$data['date_expires']) {
				$data['date_expires'] = DATETIME_ZERO;
		}

		$manufacturer_id = $this->insert('manufacturer', $data);
		
		$vendor_id = $this->generate_vendor_id(array('id'=>$manufacturer_id,'name'=>$data['name']));
		$this->update('manufacturer', array('vendor_id'=>$vendor_id), array('manufacturer_id'=>$manufacturer_id));
		
		foreach ($data['manufacturer_description'] as $language_id => $value) {
			$value['manufacturer_id']  = $manufacturer_id;
			$value['language_id']		= $language_id;
			
			$this->insert('manufacturer_description',$value);
		}
		
		if (isset($data['manufacturer_store'])) {
			foreach ($data['manufacturer_store'] as $store_id) {
				$store_data = array(
					'manufacturer_id' => $manufacturer_id,
					'store_id'		=> $store_id
				);
				
				$this->insert('manufacturer_to_store', $store_data);
			}
		}
		
		if (isset($data['articles'])) {
			foreach ($data['articles'] as $article) {
				$article['manufacturer_id'] = $manufacturer_id;
				
				$this->insert('manufacturer_article', $article);
			}
		}
		
		if ($data['keyword']) {
			$url_alias = array(
				'route' => 'product/manufacturer/product',
				'query' => 'manufacturer_id=' . (int)$manufacturer_id,
				'keyword' => $data['keyword'],
				'status' => $data['status'],
			);
			
			$this->Model_Setting_UrlAlias->addUrlAlias($url_alias);
		}
		
		if ($this->user->isDesigner()) {
			$values = array(
				'designer_id' => $manufacturer_id,
				'user_id'	=> $this->user->getId()
				);
			$this->insert('user_designer', $values);
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
		
		if ($this->user->isAdmin()) {
			$this->update('manufacturer', $data, array('manufacturer_id'=>$manufacturer_id));
		}
		else {
			$values = array(
				'name' => $data['name'],
				'image'=> $data['image'],
			);
			$this->update('manufacturer', $values, array('manufacturer_id'=>$manufacturer_id));
		}
		
		$this->delete('manufacturer_description', array('manufacturer_id'=>$manufacturer_id));
		
		foreach ($data['manufacturer_description'] as $language_id => $value) {
			$value['manufacturer_id'] = $manufacturer_id;
			$value['language_id'] = $language_id;
			
			$this->insert('manufacturer_description', $value);
		}
		
		if ($this->user->isAdmin()) {
			$this->delete('manufacturer_to_store', array('manufacturer_id'=>$manufacturer_id));
	
			if (isset($data['manufacturer_store'])) {
				foreach ($data['manufacturer_store'] as $store_id) {
					$values = array(
						'manufacturer_id' => $manufacturer_id,
						'store_id'		=> $store_id
					);
					
					$this->insert('manufacturer_to_store', $values);
				}
			}
			
			$this->delete('manufacturer_article', array('manufacturer_id'=>$manufacturer_id));
			
			if (isset($data['articles'])) {
				foreach ($data['articles'] as $article) {
					$article['manufacturer_id'] = $manufacturer_id;
				
					$this->insert('manufacturer_article', $article);
				}
			}
			
			$this->Model_Setting_UrlAlias->deleteUrlAliasByRouteQuery('product/manufacturer/product', "manufacturer_id=$manufacturer_id");
			
			if ($data['keyword']) {
				$url_alias = array(
					'route' => 'product/manufacturer/product',
					'query' => 'manufacturer_id=' . (int)$manufacturer_id,
					'keyword' => $data['keyword'],
					'status' => $data['status'],
				);
			
				$this->Model_Setting_UrlAlias->addUrlAlias($url_alias);
			}
		}

		$this->cache->delete('manufacturer');
	}
	
	public function deleteManufacturer($manufacturer_id)
	{
		$this->delete('manufacturer', array('manufacturer_id'=>$manufacturer_id));
		$this->delete('manufacturer_article', array('manufacturer_id'=>$manufacturer_id));
		$this->delete('manufacturer_description', array('manufacturer_id'=>$manufacturer_id));
		$this->delete('manufacturer_to_store', array('manufacturer_id'=>$manufacturer_id));
		
		$this->Model_Setting_UrlAlias->deleteUrlAliasByRouteQuery('product/manufacturer/product', "manufacturer_id=$manufacturer_id");
		
		$this->delete('user_designer', array('designer_id'=>$manufacturer_id));
		
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
		$query = $this->query("SELECT DISTINCT * FROM " . DB_PREFIX . "manufacturer WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		
		return $query->row;
	}
	
	public function getManufacturers($data = array(), $select = '*', $total = false) {
		if ($total) {
			$select = 'COUNT(*) as total';
		} elseif (!$select) {
			$select = '*';
		}
		
		$from = DB_PREFIX . "manufacturer m";
		
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
		
		if (!$total) {
			$order = $this->extract_order($data);
			$limit = $this->extract_limit($data);
		} else {
			$order = '';
			$limit = '';
		}
		
		$query = "SELECT $select FROM $from WHERE $where $order $limit";
		
		$result = $this->query($query);
		
		if ($total) {
			return $result->row['total'];
		}
		
		return $result->rows;
	}
	
	public function isEditable($manufacturer_id)
	{
		$query = $this->query("SELECT editable FROM " . DB_PREFIX . "manufacturer WHERE manufacturer_id='$manufacturer_id'");
		return (int)$query->row['editable'] == 1;
	}
	
	public function getManufacturerDescriptions($manufacturer_id)
	{
		$descriptions = $this->cache->get("manufacturer.$manufacturer_id");
		if (!$descriptions) {
			$query = $this->query("SELECT * FROM " . DB_PREFIX . "manufacturer_description WHERE manufacturer_id='" . (int)$manufacturer_id . "'");
			
			$descriptions = array();
			foreach ($query->rows as $result) {
				$descriptions[$result['language_id']] = $result;
			}
			
			$this->cache->set("manufacturer.$manufacturer_id", $descriptions);
		}
		
		return $descriptions;
	}
	
	public function getManufacturerWithDescription($manufacturer_id)
	{
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "manufacturer m LEFT JOIN " . DB_PREFIX . "manufacturer_description md ON (md.manufacturer_id=m.manufacturer_id) WHERE m.manufacturer_id='" . (int)$manufacturer_id . "' AND md.language_id='" . $this->config->get('config_language_id') . "'");
		
		$query->row['description'] = html_entity_decode($query->row['description']);
		$query->row['shipping_return'] = html_entity_decode($query->row['shipping_return']);
		
		return $query->row;
	}
	
	public function getManufacturerDescription($manufacturer_id)
	{
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "manufacturer_description WHERE manufacturer_id='" . (int)$manufacturer_id . "' AND language_id='" . $this->config->get('config_language_id') . "'");
		return isset($query->row['description'])?html_entity_decode($query->row['description']):'';
	}
	
	public function getManufacturerArticles($manufacturer_id)
	{
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "manufacturer_article WHERE manufacturer_id='$manufacturer_id'");
		return $query->num_rows?$query->rows:array();
	}
	
	public function getManufacturerKeyword($manufacturer_id)
	{
		$query = $this->query("SELECT keyword FROM " . DB_PREFIX . "manufacturer WHERE manufacturer_id='$manufacturer_id'");
		return isset($query->row['id'])? $query->row['keyword']:'';
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
	
	public function getTotalManufacturersByImageId($image_id)
	{
			$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "manufacturer WHERE image_id = '" . (int)$image_id . "'");

		return $query->row['total'];
	}

	public function getTotalManufacturers($data)
	{
		return $this->getManufacturers($data, null, true);
	}
}
