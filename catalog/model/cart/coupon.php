<?php
class ModelCartCoupon extends Model 
{
	public function getCoupon($code)
	{
		$code = $this->db->escape($code);
		
		$coupon_query = $this->query("SELECT * FROM " . DB_PREFIX . "coupon WHERE (LCASE(coupon_id) = LCASE('$code') OR LCASE(code) = LCASE('$code')) AND ((date_start = '0000-00-00' OR date_start <= NOW()) AND (date_end = '0000-00-00' OR date_end >= NOW())) AND status = '1'");
		
		if (!$coupon_query->num_rows) {
			return false;
		}
		
		if ($coupon_query->row['total'] > $this->cart->getSubTotal()) {
			return false;
		}
	
		$coupon_history_query = $this->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "coupon_history` ch WHERE ch.coupon_id = '" . (int)$coupon_query->row['coupon_id'] . "'");

		if ($coupon_query->row['uses_total'] > 0 && ($coupon_history_query->row['total'] >= $coupon_query->row['uses_total'])) {
			return false;
		}
		
		if ($coupon_query->row['logged'] && !$this->customer->getId()) {
			return false;
		}
		
		if ($this->customer->getId()) {
			$coupon_history_query = $this->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "coupon_history` ch WHERE ch.coupon_id = '" . (int)$coupon_query->row['coupon_id'] . "' AND ch.customer_id = '" . (int)$this->customer->getId() . "'");
			
			if ($coupon_query->row['uses_customer'] > 0 && ($coupon_history_query->row['total'] >= $coupon_query->row['uses_customer'])) {
				return false;
			}
		}
		
		$coupon_products = array();
		
		$coupon_product_query = $this->query("SELECT product_id FROM " . DB_PREFIX . "coupon_product WHERE coupon_id = '" . (int)$coupon_query->row['coupon_id'] . "'");
		
		if ($coupon_product_query->num_rows) {
			
			foreach ($coupon_product_query->rows as $row) {
				$coupon_products[] = $row['product_id'];
			}
			
			$coupon_product = false;
				
			foreach ($this->cart->getProducts() as $product) {
				if (in_array($product['product_id'], $coupon_products)) {
					$coupon_product = true;
						
					break;
				}
			}
				
			if (!$coupon_product) {
				return false;
			}
		}
		
		$coupon_query->row['product'] = $coupon_products;
		
		return $coupon_query->row;
	}

	public function loadAutoCoupons()
	{
		$customer_id = $this->customer->getId();
		
		$query = $this->query("SELECT DISTINCT * FROM " . DB_PREFIX . "coupon_customer WHERE customer_id = '" . (int)$customer_id . "'");
		
		if ($query->num_rows) {
			if (!isset($this->session->data['coupons'])) {
				$this->session->data['coupons'] = array();
			}
			
			foreach ($query->rows as $cc) {
				$coupon = $this->getCoupon((int)$cc['coupon_id']);
				if ($coupon) {
					$this->session->data['coupons'][$coupon['code']] = $coupon;
				}
			}
		}
	}
	
	public function redeem($coupon_id, $order_id, $customer_id, $amount)
	{
		$this->query("INSERT INTO `" . DB_PREFIX . "coupon_history` SET coupon_id = '" . (int)$coupon_id . "', order_id = '" . (int)$order_id . "', customer_id = '" . (int)$customer_id . "', amount = '" . (float)$amount . "', date_added = NOW()");
	}
}
