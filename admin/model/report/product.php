<?php
class ModelReportProduct extends Model {
   
   public function getProductViews(){
      $query = $this->query("SELECT * FROM " . DB_PREFIX . "product_views");
      return $query->rows;
   }
   
	public function getProductsViewed($data = array()) {
		
      $select = "pd.name, p.model, pv.product_id, pv.user_id, pv.ip_address, pv.session_id, COUNT(pv.product_id) as views";
         
      $limit = isset($data['limit'])?(int)$data['limit']:'';
      
      if($limit){
         $start = isset($data['start'])?(int)$data['start']:0;
   		if ($start < 0) {
   			$start = 0;
   		}			
   
   		if ($limit < 1) {
   			$limit = 20;
   		}
         $limit = "LIMIT $start, $limit";	
      }
		
      $sql = "SELECT $select FROM " . DB_PREFIX . "product p JOIN " . DB_PREFIX . "product_views pv ON(pv.product_id=p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY pv.product_id ORDER BY views DESC $limit";
      
		$query = $this->query($sql);
      
      return $query->rows;
	}	
	
	public function getTotalProductsViewed() {
      $query = $this->query("SELECT COUNT(DISTINCT product_id) as total FROM " . DB_PREFIX . "product_views");
      return $query->row['total'];
	}
	
	public function getTotalProductViews() {
   	$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_views");
		return $query->row['total'];
	}
			
	public function reset() {
		$this->query("DELETE FROM ". DB_PREFIX . "product_views");
	}
	
	public function getPurchased($data = array()) {
		$sql = "SELECT op.name, op.model, SUM(op.quantity) AS quantity, SUM(op.total + op.total * op.tax / 100) AS total FROM " . DB_PREFIX . "order_product op LEFT JOIN `" . DB_PREFIX . "order` o ON (op.order_id = o.order_id)";
		
		if (!is_null($data['filter_order_status_id'])) {
			$sql .= " WHERE o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " WHERE o.order_status_id > '0'";
		}
		
		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}
		
		$sql .= " GROUP BY op.model ORDER BY total DESC";
					
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
	
	public function getTotalPurchased($data) {
      	$sql = "SELECT COUNT(DISTINCT op.model) AS total FROM `" . DB_PREFIX . "order_product` op LEFT JOIN `" . DB_PREFIX . "order` o ON (op.order_id = o.order_id)";

		if (!is_null($data['filter_order_status_id'])) {
			$sql .= " WHERE o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " WHERE o.order_status_id > '0'";
		}
		
		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}
		
		$query = $this->query($sql);
				
		return $query->row['total'];
	}
}