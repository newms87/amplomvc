<?php
class Order Extends Library
{
	private $error = array();
	
	public function __construct($registry)
  	{
  		parent::__construct($registry);

		$this->language->system('order');
	}
	
	public function _e($code, $key)
	{
		$this->error[$code] = $this->language->get($key);
	}
	
	public function getErrors($pop = false, $name_format = false)
	{
		if ($pop) {
			$this->error = array();
		}
		
		if ($name_format) {
			return $this->tool->name_format($name_format, $this->error);
		}
		
		return $this->error;
	}
	
	public function hasError($type)
	{
		return !empty($this->error);
	}
	
	public function add()
	{
		if (!$this->cart->validate()) {
			return false;
		}
		
		$data = array();
		
		//Validate Shipping Address & Method
		if ($this->cart->hasShipping()) {
			if (!$this->cart->hasShippingAddress()) {
				$this->_e('O-1', 'error_shipping_address');
				return false;
			}
			
			if (!$this->cart->hasShippingMethod()) {
				$this->_e('O-2', 'error_shipping_method');
			}
		}
		
		//Validate Payment Address & Method
		if (!$this->cart->hasPaymentAddress()) {
			$this->_e('O-3', 'error_payment_address');
			return false;
		}
		
		if (!$this->cart->hasPaymentMethod()) {
			$this->_e('O-4', 'error_payment_method');
			return false;
		}
		
		//Customer Checkout
		if ($this->customer->isLogged()) {
			$data = $this->customer->info();
		}
		elseif ($this->config->get('config_guest_checkout')) {
			$data['customer_id'] = 0;
			$data['customer_group_id'] = $this->config->get('config_customer_group_id');
		}
		//Guest checkout not allowed and customer not logged in
		else {
			$this->_e('O-5', 'error_checkout_guest');
			return false;
		}
		
		//Order Information
		$data['store_id'] = $this->config->get('config_store_id');
		$data['language_id'] = $this->config->get('config_language_id');
		$data['currency_code'] = $this->currency->getCode();
		$data['currency_value'] = $this->currency->getValue();
		
		//Payment info
		$payment_address = $this->cart->getPaymentAddress();
		
		foreach ($payment_address as $key => $value) {
			$data['payment_' . $key] = $value;
		}
		
		$data['payment_method_id'] = $this->cart->getPaymentMethodId();
		
		//Shipping info
		if ($this->cart->hasShipping()) {
			$shipping_address = $this->cart->getShippingAddress();
			
			foreach ($shipping_address as $key => $value) {
				$data['shipping_' . $key] = $value;
			}
			
			$data['shipping_method_id'] = $this->cart->getShippingMethodId();
		}
		
		//Totals
		$totals = $this->cart->getTotals();
		
		$data['total'] = $totals['total'];
		$data['totals'] = $totals['data'];
		
		//Products
		$products = $this->cart->getProducts();
		
		foreach ($products as &$product) {
			$product['tax'] = $this->tax->getTax($product['total'], $product['tax_class_id']);
		} unset($product);
		
		$data['products'] = $products;
		
		// Gift Voucher
		if ($this->cart->hasVouchers()) {
			$data['vouchers'] = $this->cart->getVouchers();
		}
		
		//Comments
		$data['comment'] = $this->cart->getComment();
		
		//TODO: We should track affiliates via the session, not Cookies!
		// (Eg: $this->affiliate->isTracking(); )
		$data['affiliate_id'] = 0;
		$data['commission'] = 0;
		
		if (isset($_COOKIE['tracking'])) {
			$affiliate_info = $this->Model_Affiliate_Affiliate->getAffiliateByCode($_COOKIE['tracking']);
			
			if ($affiliate_info) {
				$data['affiliate_id'] = $affiliate_info['affiliate_id'];
				$data['commission'] = ($total / 100) * $affiliate_info['commission'];
			}
		}
		
		//Client Location / Browser Info
		$data['ip'] = $_SERVER['REMOTE_ADDR'];
		
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$data['forwarded_ip'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$data['forwarded_ip'] = $_SERVER['HTTP_CLIENT_IP'];
		}
		
		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			$data['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
		}
		
		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			$data['accept_language'] = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
		}
		
		$order_id = $this->System_Model_Order->addOrder($data);
		
		$this->session->data['order_id'] = $order_id;
		
		return $order_id;
	}
	
	public function hasOrder()
	{
		return !empty($this->session->data['order_id']);
	}
	
	public function getId()
	{
		return !empty($this->session->data['order_id']) ? $this->session->data['order_id'] : false;
	}
	
	public function get($order_id = null)
	{
		if (!$order_id && !($order_id = $this->getId())) {
			return null;
		}
		
		return $this->System_Model_Order->getOrder($order_id);
	}
	
	public function getProducts($order_id)
	{
		return $this->System_Model_Order->getOrderProducts($order_id);
	}
	
	public function getVouchers($order_id)
	{
		return $this->System_Model_Order->getOrderVouchers($order_id);
	}
	
	public function synchronizeOrders($customer)
	{
		if (empty($customer) || empty($customer['customer_id']) || empty($customer['email'])) {
			return;
		}
		
		$this->db->query(
			"UPDATE " . DB_PREFIX . "order SET customer_id = '" . (int)$customer['customer_id'] . "'" .
			" WHERE customer_id = 0 AND email = '" . $this->db->escape($customer['email']) . "'"
		);
	}
	
	public function countOrdersWithStatus($order_status_id)
	{
		$filter = array(
			'order_status_ids' => array($order_status_id),
		);
		
		$order_total = $this->System_Model_Order->getTotalOrders($filter);
		
		return $order_total;
	}
	
	public function orderStatusInUse($order_status_id)
	{
		$filter = array(
			'order_status_ids' => array($order_status_id),
		);
		
		$order_total = $this->System_Model_Order->getTotalOrders($filter);
		
		if (!$order_total) {
			$order_total = $this->System_Model_Order->getTotalOrderHistories($filter);
		}
		
		return $order_total > 0;
	}
	
	public function productInConfirmedOrder($product_id)
	{
		$filter = array(
			'product_ids' => array($product_id),
			'confirmed' => 1,
		);
		
		return $this->System_Model_Order->getTotalOrders($filter);
	}
	
	public function isEditable($order)
	{
		if (!is_array($order)) {
			$order = $this->get($order);
		}
		
		return strtotime($order['date_added']) > strtotime('-' . (int)$this->config->get('config_order_edit') . ' day');
	}
	
	public function getOrderStatus($order_status_id)
	{
		$order_statuses = $this->getOrderStatuses();
		
		return isset($order_statuses[$order_status_id]) ? $order_statuses[$order_status_id] : null;
	}
		
	public function getOrderStatuses()
	{
		return $this->config->load('order', 'order_statuses', 0);
	}
	
	public function getReturnStatus($return_status_id)
	{
		$return_statuses = $this->getReturnStatuses();
		
		return isset($return_statuses[$return_status_id]) ? $return_statuses[$return_status_id] : null;
	}
	
	public function getReturnStatuses()
	{
		return $this->config->load('product_return', 'return_statuses', 0);
	}
	
	public function getReturnReason($return_reason_id)
	{
		$return_reasons = $this->getReturnReasons();
		
		return isset($return_reasons[$return_reason_id]) ? $return_reasons[$return_reason_id] : null;
	}
	
	public function getReturnReasons()
	{
		return $this->config->load('product_return', 'return_reasons', 0);
	}
	
	public function getReturnAction($return_action_id)
	{
		$return_actions = $this->getReturnActions();
		
		return isset($return_actions[$return_action_id]) ? $return_actions[$return_action_id] : null;
	}
	
	public function getReturnActions()
	{
		return $this->config->load('product_return', 'return_actions', 0);
	}
	
	public function extractShippingAddress($order)
	{
		$payment_address = array();
		
		foreach ($order as $key => $value) {
			if ($key === 'payment_method_id') continue;
			
			if (strpos($key, 'payment_') === 0) {
				$payment_address[substr($key,8)] = $value;
			}
		}
		
		return $payment_address;
	}

	public function extractPaymentAddress($order)
	{
		$shipping_address = array();
		
		foreach ($order as $key => $value) {
			if ($key === 'shipping_method_id') continue;
			
			if (strpos($key, 'shipping_') === 0) {
				$shipping_address[substr($key,9)] = $value;
			}
		}
		
		return $shipping_address;
	}
	
	/**
	 * Update or Confirm (when order status is complete) an order
	 *
	 * @param $order_id - The ID of the order to update
	 * @param $order_status_id - The status to update the order to (use $this->order->getOrderStatuses() for a list of valid statuses)
	 * @param $comment - A comment about the change in order status
	 * @param $notify - Notify the customer about the change in their order status
	 *
	 */
	
	public function update($order_id, $order_status_id, $comment = '', $notify = false)
	{
		$order = $this->get($order_id);
		
		//order does not exist or has already been processed
		if (!$order || $order['order_status_id'] === $order_status_id) {
			return false;
		}
		
		// Fraud Detection
		if ($this->config->get('config_fraud_detection') && $this->fraud->atRisk($order)) {
			$order_status_id = $this->config->get('config_order_fraud_status_id');
		}

		// Blacklist
		if ($order['customer_id'] && $this->customer->isBlacklisted($order['customer_id'], array($order['ip']))) {
			$order_status_id = $this->config->get('config_order_blacklist_status_id');
		}
		
		$this->System_Model_Order->updateOrderStatus($order_id, $order_status_id, $comment, $notify);
		
		if (!$order['confirmed'] && $order_status_id === $this->config->get('config_order_complete_status_id')) {
			$this->confirm($order);
		}
		
		if ($notify) {
			$this->mail->callController('order_update_notify', $order);
		}
	}
	
	private function confirm($order)
	{
		$order_id = (int)$order['order_id'];
		
		//Confirm Order
		$this->db->query("UPDATE " . DB_PREFIX . "order SET confirmed = 1 WHERE order_id = $order_id");
		
		// Products
		$order_products = $this->db->queryRows("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = $order_id");
		
		foreach ($order_products as &$product) {
			//subtract Quantity from this product
			$this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = (quantity - " . (int)$product['quantity'] . ") WHERE product_id = '" . (int)$product['product_id'] . "' AND subtract = '1'");
			
			//Subtract Quantities from Product Option Values and Restrictions
			$product_option_values = $this->db->queryRows("SELECT pov.product_option_value_id, pov.option_value_id FROM " . DB_PREFIX . "order_option oo LEFT JOIN " . DB_PREFIX . "product_option_value pov ON (pov.product_option_value_id=oo.product_option_value_id) WHERE oo.order_id = '$order_id' AND order_product_id = '" . (int)$product['order_product_id'] . "'");
			
			$pov_to_ov = array();
			
			foreach ($product_option_values as $option_value) {
				$pov_to_ov[$option_value['product_option_value_id']] = $option_value['option_value_id'];
			}
			
			$order_options = $this->System_Model_Order->getOrderProductOptions($order_id, $product['order_product_id']);
			
			foreach ($order_options as $option) {
				$this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity - " . (int)$product['quantity'] . ") WHERE product_option_value_id = '" . (int)$option['product_option_value_id'] . "' AND subtract = '1'");
					
				$this->db->query("UPDATE " . DB_PREFIX . "product_option_value_restriction SET quantity = (quantity - " . (int) $product['quantity'] . ")" .
					" WHERE option_value_id = '" . ($pov_to_ov[$option['product_option_value_id']]) . "' AND restrict_option_value_id IN (" . implode(',', $pov_to_ov) . ")");
			}
			
			//Add Product Options to product data
			$product['option'] = $order_options;
			
			$this->cache->delete('product.' . $product['product_id']);
		} unset($product);
		
		$order['order_products'] = $order_products;
		
		// Downloads
		$order['order_downloads'] = $this->db->queryRows("SELECT * FROM " . DB_PREFIX . "order_download WHERE order_id = '$order_id'");
		
		// Gift Voucher
		$order_vouchers = $this->db->queryRows("SELECT voucher_id FROM " . DB_PREFIX . "order_voucher WHERE order_id = '$order_id'");
		
		foreach ($order_vouchers as $voucher) {
			$this->System_Model_Voucher->activate($voucher['voucher_id']);
		}
		
		$order['order_vouchers'] = $order_vouchers;
		
		// Order Totals
		$order_totals = $this->db->queryRows("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '$order_id' ORDER BY sort_order ASC");
		
		foreach ($order_totals as &$order_total) {
			$total_class = 'System_Extension_Total_Model_' . $this->tool->formatClassname($order_total['code']);
			
			if (method_exists($this->$total_class, 'confirm')) {
				$this->$total_class->confirm($order, $order_total);
			}
		}
		
		$order['order_totals'] = $order_totals;
		
		//Order Status
		$order['order_status'] = $this->order->getOrderStatus($order['order_status_id']);
		
		//Send Order Emails
		$this->mail->callController('order', $order);
	}

	public function clear()
	{
		unset($this->session->data['order_id']);
	}
}