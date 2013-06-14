<?php
class Admin_Model_Catalog_Flashsale extends Model 
{
	public function addFlashsale($data)
	{
		if(!isset($data['image']))
			$data['image'] = '';
		
		$flashsale_id = $this->insert('flashsale', $data);
		
		foreach ($data['products'] as $product_id=>$product) {
			$product['flashsale_id'] = $flashsale_id;
			$product['product_id']	= $product_id;
			
			$this->insert('flashsale_product', $product);
			
			$values = array(
				'product_id'		=> $product_id,
				'flashsale_id'		=> $flashsale_id,
				'date_start'		=> $data['date_start'],
				'date_end'			=> $data['date_end'],
				'price'				=> $product['price'],
				'customer_group_id' => $data['customer_group_id']
			);
				
			$this->updateFlashsaleSpecial($values);
		}
		
		if (isset($data['designers'])) {
			foreach ($data['designers'] as $designer_id) {
				$fdata = array(
					'designer_id' => $designer_id,
					'flashsale_id' => $flashsale_id
				);
				
				$this->insert('flashsale_designer', $fdata);
			}
		}
		
		if (isset($data['articles'])) {
			foreach ($data['articles'] as $article) {
				$article['flashsale_id'] = $flashsale_id;
				
				$this->insert('flashsale_article', $article);
			}
		}
		
		if ($data['keyword']) {
			$url_alias = array(
				'route'	=>'sales/flashsale',
				'query'	=>"flashsale_id=" . (int)$flashsale_id,
				'keyword' =>$data['keyword'],
				'status'  =>$data['status']
			);
			
			$this->Model_Setting_UrlAlias->addUrlAlias($url_alias);
		}
		
		if ($data['extend_flashsale']) {
			$this->extend_flashsale_price($flashsale_id);
		}
		
		$this->cache->delete('flashsale');
	}
	
	public function editFlashsale($flashsale_id, $data)
	{
		if(!isset($data['image']))
			$data['image'] = '';
		
		$where = array(
			'flashsale_id' => $flashsale_id
		);
		
		$this->update('flashsale', $data, $where);
		
		$this->updateStatus($flashsale_id, $data['status']);
		
		
		//Update Flashsale Products
		$where = array(
			'flashsale_id' => $flashsale_id
		);
		
		$this->delete('flashsale_product', $where);
		
		foreach ($data['products'] as $product_id=>$product) {
			$product['flashsale_id'] = $flashsale_id;
			$product['product_id'] = $product_id;
			
			$this->insert('flashsale_product', $product);
			
			$values = array(
				'product_id'		=> $product_id,
				'flashsale_id'		=> $flashsale_id,
				'date_start'		=> $data['date_start'],
				'date_end'			=> $data['date_end'],
				'price'				=> $product['price'],
				'customer_group_id' => $data['customer_group_id']
			);
			
			$this->updateFlashsaleSpecial($values);
		}
		
		
		//Update Flashsale Designers
		$where = array(
			'flashsale_id' => $flashsale_id
		);
		
		$this->delete('flashsale_designer', $where);
		
		if (isset($data['designers'])) {
			foreach ($data['designers'] as $designer_id) {
				$fd = array(
					'designer_id'  => $designer_id,
					'flashsale_id' => $flashsale_id
				);
				
				$this->insert('flashsale_designer', $fd);
			}
		}
		
		
		//Update Flashsale Articles
		$where = array(
			'flashsale_id' => $flashsale_id
		);
		
		$this->delete('flashsale_article', $where);
		
		if (isset($data['articles'])) {
			foreach ($data['articles'] as $article) {
				$article['flashsale_id'] = $flashsale_id;
				
				$this->insert('flashsale_article', $article);
			}
		}
		
		
		//Update Flashsale URL Alias
		$where = array(
			'query' => 'flashsale_id=' . $flashsale_id
		);
		
		$this->Model_Setting_UrlAlias->deleteUrlAliasByRouteQuery('sales/flashsale', 'flashsale_id=' . (int)$flashsale_id);
		
		if ($data['keyword']) {
			$url_alias = array(
				'route'	=>'sales/flashsale',
				'query'=>'flashsale_id=' . (int)$flashsale_id,
				'keyword'=>$data['keyword'],
				'status'=>$data['status']
			);
			
			$this->Model_Setting_UrlAlias->addUrlAlias($url_alias);
		}

		if ($data['extend_flashsale']) {
			$this->extend_flashsale_price($flashsale_id);
		}
		
		$this->cache->delete('flashsale');
	}
	
	public function deleteFlashsale($flashsale_id)
	{
		$this->delete('flashsale', $flashsale_id);
		$this->delete('flashsale_product', array('flashsale_id'=>$flashsale_id));
		$this->delete('flashsale_article', array('flashsale_id'=>$flashsale_id));
		$this->delete('flashsale_designer', array('flashsale_id'=>$flashsale_id));
		
		$this->delete('product_special', array('flashsale_id'=>$flashsale_id));
		
		$this->Model_Setting_UrlAlias->deleteUrlAliasByRouteQuery('sales/flashsale', 'flashsale_id=' . (int)$flashsale_id);
		
		$this->cache->delete('flashsale');
	}
	
	private function updateFlashsaleSpecial($data)
	{
		$where = array(
			'product_id'	=> $data['product_id'],
			'flashsale_id' => $data['flashsale_id']
		);
		
		$query = $this->get('product_special', 'product_special_id',  $where);
		
		$data['priority'] = -1;
			
		if ($query->num_rows) {
			$this->update('product_special',  $data, $query->row['product_special_id']);
		}
		else {
			$this->insert('product_special', $data);
		}
		$this->cache->delete('product');
	}
	
	private function extend_flashsale_price($flashsale_id)
	{
		$query = $this->query("SELECT date_expires FROM " . DB_PREFIX . "flashsale_designer fd LEFT JOIN " . DB_PREFIX . "manufacturer m ON (fd.designer_id = m.manufacturer_id) WHERE flashsale_id = '" . (int)$flashsale_id . "' LIMIT 1");
		if (!$query->num_rows) {
			$this->message->add("warning", "While extending product(s) flashsale price, Flashsale ID $flashsale_id was not associated with a designer.");
			return;
		}
		
		$values = array(
			'date_end'=>$query->row['date_expires']
		);
		
		$where = array(
			'flashsale_id' => $flashsale_id
		);
		
		$this->update('product_special', $values, $where);
	}
	
	public function generate_url($flashsale_id,$name)
	{
		$url = 'sale/'.$this->Model_Setting_UrlAlias->format_url($name);
		$orig = $url;
		$count = 2;
		
		if ($flashsale_id) {
			$url_alias = $this->Model_Setting_UrlAlias->getUrlAliasByRouteQuery('sales/flashsale', "flashsale_id=$flashsale_id");
		}
		else {
			$url_alias = null;
		}
		
		$test = $this->Model_Setting_UrlAlias->getUrlAliasByKeyword($url);
		while (!empty($test) && $test['url_alias_id'] != $url_alias['url_alias_id']) {
			$url = $orig . '-' . $count++;
			$test = $this->Model_Setting_UrlAlias->getUrlAliasByKeyword($url);
		}
		return $url;
	}
	
	public function getFlashsale($flashsale_id)
	{
		$query = $this->get('flashsale', '*', $flashsale_id);
		
		if ($query->num_rows) {
			$query->row['teaser'] = html_entity_decode($query->row['teaser']);
			
			$where = array(
			'flashsale_id' => $flashsale_id
			);
			
			$options = array(
				'order_by' => 'sort_order ASC'
			);
			
			$results = $this->get('flashsale_product', '*', $where, $options);
			
			$products = array();
			foreach ($results->rows as $key=>$product) {
				$p = $this->Model_Catalog_Product->getProduct($product['product_id']);
				if(!$p)continue;
				$products[$key] = $p;
				$products[$key]['orig_price'] = $products[$key]['price'];
				$products[$key]['price'] = $product['price'];
				$products[$key]['sort_order'] = $product['sort_order'];
			}
			
			$designers = array();
			foreach($this->getFlashsaleDesigners($flashsale_id) as $d)
				$designers[] = $d['designer_id'];
			
			$where = array(
			'flashsale_id' => $flashsale_id
			);
			
			$results = $this->get('flashsale_article', '*',  $where);
			
			$articles = array();
			foreach ($results->rows as $key=>$row) {
				$articles[$key] = $row;
				$articles[$key]['description'] = html_entity_decode($row['description']);
			}
			
			$query->row['products'] = $products;
			$query->row['designers']= $designers;
			$query->row['articles'] = $articles;
		}
		
		return $query->row;
	}
	
	public function getFlashsaleDesigners($flashsale_id)
	{
		$results = $this->query("SELECT designer_id, m.name FROM " . DB_PREFIX . "flashsale_designer fd LEFT JOIN " . DB_PREFIX . "manufacturer m ON(fd.designer_id=m.manufacturer_id) WHERE flashsale_id='$flashsale_id'");
		return $results->num_rows?$results->rows:array();
	}
	
	public function getFlashsales($data=array(),$total=false) {
		$options = array();
		
		$where = '';
		
		if (!empty($data)) {
			if (isset($data['name']) && $data['name']) {
				$where .= ($where ? ' AND ':'') . "LCASE(`name`) like '%" . $this->db->escape(strtolower($data['name'])) . "%'";
			}
			
			if (isset($data['date_end']) && $data['date_end']) {
				$where .= ($where ? ' AND ':'') . "`date_end`" . $this->db->escape($data['date_end_prefix']) . "'" . $this->date->format($data['date_end']) . "'";
			}
			if (isset($data['date_start']) && $data['date_start']) {
				$where .= ($where ? ' AND ':'') . "`date_start`" . $this->db->escape($data['date_start_prefix']) . "'" . $this->date->format($data['date_start']) . "'";
			}
			
			if (isset($data['status']) && $data['status'] !== '') {
				$where .= ($where ? ' AND ':'') . "`status`='" . (int)$data['status'] . "'";
			}
			
			if (!$total) {
				if (!empty($data['sort'])) {
					$order = (isset($data['order'])?$data['order']:'ASC');
					$options['order_by'] = $data['sort'] . ' ' . $order;
				}
				
				if (!empty($data['limit'])) {
					$start = isset($data['start']) ? $data['start']:0;
					$options['limit'] = "$start, $data[limit]";
				}
			}
		
		}
		
		$select = $total?"COUNT(*) as total":"*";
		
		$query = $this->get('flashsale', $select, $where, $options);
		
		return $total ? $query->row['total'] : $query->rows;
	}
	
	public function getFlashsalesByDesignerID($designer_id)
	{
		$where = array(
			'designer_id' => $designer_id
		);
		
		$query = $this->get('flashsale_designer', 'flashsale_id', $where);
		
		return $query->rows;
	}
	
	public function getFlashsaleByKeyword($keyword)
	{
		$where = array(
			'keyword' => $keyword
		);
		
		$options = array(
			'limit' => 1
		);
		
		$query = $this->get('flashsale', '*', $where, $options);
		
		return $query->row;
	}
	
	public function getTotalFlashsales($data)
	{
		return $this->getFlashsales($data, true);
	}
	
	public function updateStatus($flashsale_id, $status)
	{
		$values = array(
		'status'=>(int)$status
		);
		
		$where = array(
			'flashsale_id' => $flashsale_id
		);
		
		$this->update('flashsale', $values, $where);
		
		if ((int)$status == 0) {
			$where = array(
			'flashsale_id' => $flashsale_id
		);
		
			$this->delete('product_special', $where);
		}
		else {
			$fs_info = $this->getFlashsale($flashsale_id);
			foreach ($fs_info['products'] as $p) {
				$values = array(
					'product_id'		=> $p['product_id'],
					'flashsale_id'		=> $flashsale_id,
					'date_start'		=> $fs_info['date_start'],
					'date_end'			=> $fs_info['date_end'],
					'price'				=> $p['price'],
					'customer_group_id' => $fs_info['customer_group_id']
			);
			
				$this->updateFlashsaleSpecial($values);
			}
		}
		
		$url_alias = $this->Model_Setting_UrlAlias->getUrlAliasByRouteQuery('sales/flashsale', 'flashsale_id='.$flashsale_id);
		
		if (isset($url_alias['url_alias_id'])) {
			$this->Model_Setting_UrlAlias->editUrlAlias($url_alias['url_alias_id'],array('status'=>$status));
		}
		
		$this->cache->delete('flashsale');
	}
}
