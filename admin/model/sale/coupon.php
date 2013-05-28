<?php
class ModelSaleCoupon extends Model {
	public function addCoupon($data) {
		
		$data['date_added'] = $this->tool->format_datetime();
		
		$coupon_id = $this->insert('coupon', $data);
		
		if (isset($data['coupon_products'])) {
			foreach ($data['coupon_products'] as $product_id) {
				$values = array(
					'product_id' => $product_id,
					'coupon_id'  => $coupon_id
				);
				$this->insert('coupon_product', $values);
			}
		}
		
		if(isset($data['coupon_categories'])){
			foreach ($data['coupon_categories'] as $category_id) {
				$values = array(
					'category_id' => $category_id,
					'coupon_id'  => $coupon_id
				);
				
				$this->insert('coupon_category', $values);
			}
		}
		
		if(isset($data['coupon_customers'])){
			foreach ($data['coupon_customers'] as $customer_id) {
				$values = array(
					'customer_id' => $customer_id,
					'coupon_id'  => $coupon_id
				);
				
				$this->insert('coupon_customer', $values);
			}
		}
	}
	
	public function editCoupon($coupon_id, $data) {
		$this->update('coupon', $data, $coupon_id);
		
		$this->delete('coupon_product', array('coupon_id' => $coupon_id));
		
		if (isset($data['coupon_product'])) {
			foreach ($data['coupon_product'] as $product_id) {
				$values = array(
					'product_id' => $product_id,
					'coupon_id'  => $coupon_id
				);
				$this->insert('coupon_product', $values);
			}
		}
		
		$this->delete('coupon_category', array('coupon_id' => $coupon_id));
		
		if(isset($data['coupon_category'])){
			foreach ($data['coupon_category'] as $category_id) {
				$values = array(
					'category_id' => $category_id,
					'coupon_id'  => $coupon_id
				);
				
				$this->insert('coupon_category', $values);
			}
		}
		
		$this->delete('coupon_customer', array('coupon_id' => $coupon_id));
		
		if(isset($data['coupon_customer'])){
			foreach ($data['coupon_customer'] as $customer_id) {
				$values = array(
					'customer_id' => $customer_id,
					'coupon_id'  => $coupon_id
				);
				
				$this->insert('coupon_customer', $values);
			}
		}
	}
	
	public function deleteCoupon($coupon_id) {
		$this->delete('coupon', $coupon_id);
		$this->delete('coupon_product', array('coupon_id' => $coupon_id));
		$this->delete('coupon_category', array('coupon_id' => $coupon_id));
		$this->delete('coupon_customer', array('coupon_id' => $coupon_id));
		$this->delete('coupon_history', array('coupon_id' => $coupon_id));
	}
	
	public function getCoupon($coupon_id) {
		$query = $this->get('coupon', '*', $coupon_id);
		
		return $query->row;
	}

	public function getCouponByCode($code) {
		$query = $this->query("SELECT DISTINCT * FROM " . DB_PREFIX . "coupon WHERE code = '" . $this->db->escape($code) . "'");
		
		return $query->row;
	}
		
	public function getCoupons($data = array()) {
		$sql = "SELECT coupon_id, name, code, discount, date_start, date_end, status FROM " . DB_PREFIX . "coupon";
		
		$sort_data = array(
			'name',
			'code',
			'discount',
			'date_start',
			'date_end',
			'status'
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
	}

	public function getCouponProducts($coupon_id) {
		$query = $this->query("SELECT product_id FROM " . DB_PREFIX . "coupon_product WHERE coupon_id = '" . (int)$coupon_id . "'");
		return $query->rows;
	}
	
	public function getCouponCategories($coupon_id){
		$query = $this->query("SELECT category_id FROM " . DB_PREFIX . "coupon_category WHERE coupon_id = '" . (int)$coupon_id . "'");
		return $query->rows;
	}
	
	public function getCouponCustomers($coupon_id){
		$query = $this->query("SELECT customer_id FROM " . DB_PREFIX . "coupon_customer WHERE coupon_id = '" . (int)$coupon_id . "'");
		return $query->rows;
	}
	
	public function getTotalCoupons() {
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "coupon");
		return $query->row['total'];
	}
	
	public function getCouponHistories($coupon_id, $start = 0, $limit = 10) {
		$query = $this->query("SELECT ch.order_id, CONCAT(c.firstname, ' ', c.lastname) AS customer, ch.amount, ch.date_added FROM " . DB_PREFIX . "coupon_history ch LEFT JOIN " . DB_PREFIX . "customer c ON (ch.customer_id = c.customer_id) WHERE ch.coupon_id = '" . (int)$coupon_id . "' ORDER BY ch.date_added ASC LIMIT " . (int)$start . "," . (int)$limit);
		return $query->rows;
	}
	
	public function getTotalCouponHistories($coupon_id) {
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "coupon_history WHERE coupon_id = '" . (int)$coupon_id . "'");
		return $query->row['total'];
	}
}
