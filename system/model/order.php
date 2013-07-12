<?php
class System_Model_Order extends Model 
{
	public function addOrder($data)
	{
		//TODO Move this to plugin, Should optionally have form for guest info (eg: name, email, phone, etc.)
		if (!isset($data['customer_id'])) {
			$data['customer_id'] = 0;
			$data['customer_group_id'] = 0;
			
			if (empty($data['firstname'])) {
				$data['firstname'] = 'guest';
			}
			
			if (empty($data['email'])) {
				$data['email'] = 'guest';
			}
		}
		
		$data['date_added'] = $this->date->now();
		$data['date_modified'] = $this->date->now();
		
		$order_id = $this->insert('order', $data);
		
		foreach ($data['products'] as $product) {
			$product['order_id'] = $order_id;
			
			$order_product_id = $this->insert('order_product', $product);
 
			foreach ($product['option'] as $option) {
				$option['order_id'] = $order_id;
				$option['order_product_id'] = $order_product_id;
				$option['value'] = $option['option_value'];
				
				$this->insert('order_option', $option);
			}
			
			foreach ($product['download'] as $download) {
				$download['order_id'] = $order_id;
				$download['order_product_id'] = $order_product_id;
				$download['remaining'] = $download['remaining'] * $product['quantity'];
				
				$this->insert('order_download', $download);
			}
		}
		
		foreach ($data['vouchers'] as $voucher) {
			$voucher['order_id'] = $order_id;
			
			$this->insert('order_voucher', $voucher);
		}
		
		foreach ($data['totals'] as $total) {
			$total['order_id'] = $order_id;
			
			$this->insert('order_total', $total);
		}

		return $order_id;
	}
	
	public function updateOrderStatus($order_id, $order_status_id, $comment = '', $notify = false)
	{
		$data = array(
			'order_status_id' => $order_status_id,
			'date_modified' => $this->date->now(),
		);
		
		$this->update('order', $data, $order_id);
		
		$history_data = array(
			'order_id' => $order_id,
			'order_status_id' => $order_status_id,
			'comment' => $comment,
			'notify' => $notify,
			'date_added' => $this->date->now(),
		);
		
		$this->insert('order_history', $history_data);
	}
	
	public function getOrder($order_id)
	{
		$order_query = $this->query("SELECT *, (SELECT os.name FROM `" . DB_PREFIX . "order_status` os WHERE os.order_status_id = o.order_status_id AND os.language_id = o.language_id) AS order_status FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int)$order_id . "'");
			
		if ($order_query->num_rows) {
			$country_query = $this->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['shipping_country_id'] . "'");
			
			if ($country_query->num_rows) {
				$shipping_iso_code_2 = $country_query->row['iso_code_2'];
				$shipping_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$shipping_iso_code_2 = '';
				$shipping_iso_code_3 = '';
			}
			
			$zone_query = $this->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['shipping_zone_id'] . "'");
			
			if ($zone_query->num_rows) {
				$shipping_zone_code = $zone_query->row['code'];
			} else {
				$shipping_zone_code = '';
			}
			
			$country_query = $this->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['payment_country_id'] . "'");
			
			if ($country_query->num_rows) {
				$payment_iso_code_2 = $country_query->row['iso_code_2'];
				$payment_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$payment_iso_code_2 = '';
				$payment_iso_code_3 = '';
			}
			
			$zone_query = $this->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['payment_zone_id'] . "'");
			
			if ($zone_query->num_rows) {
				$payment_zone_code = $zone_query->row['code'];
			} else {
				$payment_zone_code = '';
			}

			$language_info = $this->language->getInfo(null, $order_query->row['language_id']);
			
			if ($language_info) {
				$language_code = $language_info['code'];
				$language_filename = $language_info['filename'];
				$language_directory = $language_info['directory'];
			} else {
				$language_code = '';
				$language_filename = '';
				$language_directory = '';
			}
			
			$additional_info = array(
				'shipping_zone_code'		=> $shipping_zone_code,
				'shipping_iso_code_2'	=> $shipping_iso_code_2,
				'shipping_iso_code_3'	=> $shipping_iso_code_3,
				'payment_zone_code'		=> $payment_zone_code,
				'payment_iso_code_2'		=> $payment_iso_code_2,
				'payment_iso_code_3'		=> $payment_iso_code_3,
				'language_code'			=> $language_code,
				'language_filename'		=> $language_filename,
				'language_directory'		=> $language_directory
			);
			
			return array_merge($order_query->row, $additional_info);
			
		} else {
			return false;
		}
	}
	
	public function getOrderStatus($order_id)
	{
		$query = $this->query("SELECT o.order_status_id, os.name FROM " . DB_PREFIX . "order o LEFT JOIN " . DB_PREFIX . "order_status os ON (o.order_status_id=os.order_status_id) WHERE o.order_id = '" . (int)$order_id . "' AND os.language_id = o.language_id");
		
		return $query->row;
	}
	
	public function getOrderProducts($order_id)
	{
		return $this->queryRows("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
	}
	
	public function getOrderProductOptions($order_id, $order_product_id)
	{
		$query = $this->get('order_option', '*', array('order_id' => $order_id, 'order_product_id' => $order_product_id));
		
		return $query->rows;
	}
	
	public function getTotalOrderProducts($order_id)
	{
		return $this->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
	}
}
