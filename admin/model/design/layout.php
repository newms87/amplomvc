<?php
class ModelDesignLayout extends Model {
	public function addLayout($data) {
		$this->query("INSERT INTO " . DB_PREFIX . "layout SET name = '" . $this->db->escape($data['name']) . "'");
	
		$layout_id = $this->db->getLastId();
		
		if (isset($data['layout_route'])) {
			foreach ($data['layout_route'] as $layout_route) {
				$this->query("INSERT INTO " . DB_PREFIX . "layout_route SET layout_id = '" . (int)$layout_id . "', store_id = '" . (int)$layout_route['store_id'] . "', route = '" . $this->db->escape($layout_route['route']) . "'");
			}	
		}
	}
	
	public function editLayout($layout_id, $data) {
		$this->query("UPDATE " . DB_PREFIX . "layout SET name = '" . $this->db->escape($data['name']) . "' WHERE layout_id = '" . (int)$layout_id . "'");
		
		$this->query("DELETE FROM " . DB_PREFIX . "layout_route WHERE layout_id = '" . (int)$layout_id . "'");
		
		if (isset($data['layout_route'])) {
			foreach ($data['layout_route'] as $layout_route) {
				$this->query("INSERT INTO " . DB_PREFIX . "layout_route SET layout_id = '" . (int)$layout_id . "', store_id = '" . (int)$layout_route['store_id'] . "', route = '" . $this->db->escape($layout_route['route']) . "'");
			}
		}
	}
	
   public function setLayoutPageHeaders($data){
      $this->query("TRUNCATE " . DB_PREFIX . "layout_header");
      $this->query("TRUNCATE " . DB_PREFIX . "page_header");
      foreach ($data['page_headers'] as $page_header_id => $header) {
         foreach($header['page_header'] as $language_id=>$html)
            $this->query("INSERT INTO " . DB_PREFIX . "page_header SET page_header_id='$page_header_id', language_id = '" . (int)$language_id . "', page_header='" . $this->db->escape($html) . "', priority = '" . (int)$header['priority'] . "', status = '" . (int)$header['status'] . "'");
         foreach(array_unique($header['layouts']) as $layout_id)
            $this->query("INSERT INTO " . DB_PREFIX . "layout_header SET layout_id = '" . (int)$layout_id . "', page_header_id='$page_header_id'");
      }
   }
   public function getAllPageHeaders(){
      $query = $this->query("SELECT lh.layout_id, ph.* FROM " . DB_PREFIX . "layout_header lh LEFT JOIN " . DB_PREFIX . "page_header ph ON(ph.page_header_id=lh.page_header_id)");
      $headers = array();
      foreach($query->rows as $page_header){
         $headers[$page_header['page_header_id']]['page_header'][$page_header['language_id']] = $page_header['page_header'];
         $headers[$page_header['page_header_id']]['layouts'][$page_header['layout_id']] = $page_header['layout_id'];
         $headers[$page_header['page_header_id']]['status'] = $page_header['status'];
         $headers[$page_header['page_header_id']]['priority'] = $page_header['priority'];
      }
      return $headers;
   }
   public function getLayoutPageHeaders($layout_id){
      $query = $this->query("SELECT ph.* FROM " . DB_PREFIX . "layout_header lh LEFT JOIN " . DB_PREFIX . "page_header ph ON(ph.page_header_id=lh.page_header_id) WHERE lh.layout_id='" . (int)$layout_id ."'");
      $headers = array();
      foreach($query->rows as $r){
         $headers[$r['page_header_id']][$r['language_id']] = $r['page_header'];
      }
      return $headers;
   }
   
	public function deleteLayout($layout_id) {
		$this->query("DELETE FROM " . DB_PREFIX . "layout WHERE layout_id = '" . (int)$layout_id . "'");
		$this->query("DELETE FROM " . DB_PREFIX . "layout_route WHERE layout_id = '" . (int)$layout_id . "'");
		$this->query("DELETE FROM " . DB_PREFIX . "layout_header WHERE layout_id = '" . (int)$layout_id . "'");
		$this->query("DELETE FROM " . DB_PREFIX . "category_to_layout WHERE layout_id = '" . (int)$layout_id . "'");
		$this->query("DELETE FROM " . DB_PREFIX . "product_to_layout WHERE layout_id = '" . (int)$layout_id . "'");
		$this->query("DELETE FROM " . DB_PREFIX . "information_to_layout WHERE layout_id = '" . (int)$layout_id . "'");		
	}
	
	public function getLayout($layout_id) {
		$query = $this->query("SELECT DISTINCT * FROM " . DB_PREFIX . "layout WHERE layout_id = '" . (int)$layout_id . "'");
		
		return $query->row;
	}
	
	public function getLayouts($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "layout";
		
		$sort_data = array('name');	
		
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
	}
	
	public function getLayoutRoutes($layout_id) {
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "layout_route WHERE layout_id = '" . (int)$layout_id . "'");
		
		return $query->rows;
	}
	
		
	public function getTotalLayouts() {
      	$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "layout");
		
		return $query->row['total'];
	}	
}