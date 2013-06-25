<?php
class Catalog_Model_Account_Order extends Model 
{
	public function getOrder($order_id, $customer_id = null)
	{
		if ($customer_id !== false) {
			$customer_id = $customer_id ? $customer_id : $this->customer->getId();
			
			$customer_id_query = "AND customer_id = '" . (int)$customer_id . "'"; 
		} else {
			$customer_id_query = '';
		}
		
		$order = $this->queryRow("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "' $customer_id_query AND order_status_id > '0'");
		
		if ($order) {
			$shipping_country = $this->queryRow("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order['shipping_country_id'] . "'");
			
			$order['shipping_iso_code_2'] = $shipping_country ? $shipping_country['iso_code_2'] : '';
			$order['shipping_iso_code_3'] = $shipping_country ? $shipping_country['iso_code_3'] : '';
			
			$order['shipping_zone_code'] = $this->queryVar("SELECT code FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order['shipping_zone_id'] . "'");
			
			$payment_country = $this->queryRow("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order['payment_country_id'] . "'");
			
			$order['payment_iso_code_2'] = $payment_country ? $payment_country['iso_code_2'] : '';
			$order['payment_iso_code_3'] = $payment_country ? $payment_country['iso_code_3'] : '';
			
			$order['payment_zone_code'] = $this->queryVar("SELECT code FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order['payment_zone_id'] . "'");
			
			return $order;
		}
	
		return false;
	}
	
	public function getOrders($start = 0, $limit = 20)
	{
		if ($start < 0) {
			$start = 0;
		}
		
		$query = $this->query("SELECT o.order_id, o.firstname, o.lastname, os.name as status, o.date_added, o.total, o.currency_code, o.currency_value FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_status os ON (o.order_status_id = os.order_status_id) WHERE o.customer_id = '" . (int)$this->customer->getId() . "' AND o.order_status_id > '0' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY o.order_id DESC LIMIT " . (int)$start . "," . (int)$limit);
	
		return $query->rows;
	}
	
	public function getOrderProducts($order_id)
	{
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
	
		return $query->rows;
	}
	
	public function getOrderOptions($order_id, $order_product_id)
	{
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");
	
		return $query->rows;
	}
	
	public function getOrderVouchers($order_id)
	{
		$query = $this->query("SELECT * FROM `" . DB_PREFIX . "order_voucher` WHERE order_id = '" . (int)$order_id . "'");
		
		return $query->rows;
	}
	
	public function getOrderTotals($order_id)
	{
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order");
	
		return $query->rows;
	}

	public function getOrderHistories($order_id)
	{
		$query = $this->query("SELECT date_added, os.name AS status, oh.comment, oh.notify FROM " . DB_PREFIX . "order_history oh LEFT JOIN " . DB_PREFIX . "order_status os ON oh.order_status_id = os.order_status_id WHERE oh.order_id = '" . (int)$order_id . "' AND oh.notify = '1' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY oh.date_added");
	
		return $query->rows;
	}

	public function getOrderDownloads($order_id)
	{
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "order_download WHERE order_id = '" . (int)$order_id . "' ORDER BY name");
	
		return $query->rows;
	}

	public function getTotalOrders()
	{
			$query = $this->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE customer_id = '" . (int)$this->customer->getId() . "' AND order_status_id > '0'");
		
		return $query->row['total'];
	}
		
	public function getTotalOrderProductsByOrderId($order_id)
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
		
		return $query->row['total'];
	}
	
	public function getTotalOrderVouchersByOrderId($order_id)
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order_voucher` WHERE order_id = '" . (int)$order_id . "'");
		
		return $query->row['total'];
	}
	
}