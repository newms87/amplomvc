<?php
class Admin_Model_Design_Layout extends Model 
{
	public function addLayout($data)
	{
		$layout_id = $this->insert('layout', $data);
		
		if (!empty($data['layout_route'])) {
			foreach ($data['layout_route'] as $layout_route) {
				$layout_route['layout_id'] = $layout_id;
				
				$this->insert('layout_route', $layout_route);
			}
		}
		
		return $layout_id;
	}
	
	public function editLayout($layout_id, $data)
	{
		$this->update('layout', $data, $layout_id);
		
		$this->delete('layout_route', array('layout_id' => $layout_id));
		
		if (!empty($data['layout_route'])) {
			foreach ($data['layout_route'] as $layout_route) {
				$layout_route['layout_id'] = $layout_id;
				
				$this->insert('layout_route', $layout_route);
			}
		}
	}
	
	public function setLayoutPageHeaders($data)
	{
		$this->query("TRUNCATE " . DB_PREFIX . "layout_header");
		$this->query("TRUNCATE " . DB_PREFIX . "page_header");
		foreach ($data['page_headers'] as $page_header_id => $header) {
			foreach($header['page_header'] as $language_id=>$html)
				$this->query("INSERT INTO " . DB_PREFIX . "page_header SET page_header_id='$page_header_id', language_id = '" . (int)$language_id . "', page_header='" . $this->db->escape($html) . "', priority = '" . (int)$header['priority'] . "', status = '" . (int)$header['status'] . "'");
			foreach(array_unique($header['layouts']) as $layout_id)
				$this->query("INSERT INTO " . DB_PREFIX . "layout_header SET layout_id = '" . (int)$layout_id . "', page_header_id='$page_header_id'");
		}
	}
	
	public function getAllPageHeaders()
	{
		$query = $this->query("SELECT lh.layout_id, ph.* FROM " . DB_PREFIX . "layout_header lh LEFT JOIN " . DB_PREFIX . "page_header ph ON(ph.page_header_id=lh.page_header_id)");
		$headers = array();
		foreach ($query->rows as $page_header) {
			$headers[$page_header['page_header_id']]['page_header'][$page_header['language_id']] = $page_header['page_header'];
			$headers[$page_header['page_header_id']]['layouts'][$page_header['layout_id']] = $page_header['layout_id'];
			$headers[$page_header['page_header_id']]['status'] = $page_header['status'];
			$headers[$page_header['page_header_id']]['priority'] = $page_header['priority'];
		}
		return $headers;
	}
	
	public function getLayoutPageHeaders($layout_id)
	{
		$query = $this->query("SELECT ph.* FROM " . DB_PREFIX . "layout_header lh LEFT JOIN " . DB_PREFIX . "page_header ph ON(ph.page_header_id=lh.page_header_id) WHERE lh.layout_id='" . (int)$layout_id ."'");
		$headers = array();
		foreach ($query->rows as $r) {
			$headers[$r['page_header_id']][$r['language_id']] = $r['page_header'];
		}
		return $headers;
	}
	
	public function deleteLayout($layout_id)
	{
		$this->delete('layout', $layout_id);
		$this->delete('layout_route', array('layout_id' => $layout_id));
		$this->delete('layout_header', array('layout_id' => $layout_id));
		$this->delete('category_to_layout', array('layout_id' => $layout_id));
		$this->delete('product_to_layout', array('layout_id' => $layout_id));
		$this->delete('information_to_layout', array('layout_id' => $layout_id));
	}
	
	public function getLayout($layout_id)
	{
		return $this->query_row("SELECT DISTINCT * FROM " . DB_PREFIX . "layout WHERE layout_id = '" . (int)$layout_id . "'");
	}
	
	public function getLayouts($data = array(), $select = '*', $total = false) {
		//Select
		if ($total) {
			$select = 'COUNT(*) as total';
		} elseif (!$select) {
			$select = '*';
		}
		
		//From
		$from = DB_PREFIX . "layout l";
		
		//Where
		$where = "1";
		
		if (!empty($data['name'])) {
			$where .= " AND LCASE(l.name) like '%" . $this->db->escape(strtolower($data['name'])) . "%'";
		}
		
		//Order By & Limit
		if (!$total) {
			$order = $this->extract_order($data);
			$limit = $this->extract_limit($data);
		} else {
			$order = '';
			$limit = '';
		}
		
		//The Query
		$query = "SELECT $select FROM $from WHERE $where $order $limit";
		
		//Execute
		$result = $this->query($query);

		//Process Results
		if ($total) {
			return $result->row['total'];
		}
		
		return $result->rows;
	}
	
	public function getLayoutRoutes($layout_id)
	{
		return $this->query_rows("SELECT * FROM " . DB_PREFIX . "layout_route WHERE layout_id = '" . (int)$layout_id . "'");
	}
	
		
	public function getTotalLayouts($data = array()) {
		return $this->getLayouts($data, null, true);
	}
}