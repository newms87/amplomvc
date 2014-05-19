<?php

class Order Extends Library
{
	public function add()
	{
		if (!$this->cart->validate()) {
			return false;
		}

		$data = array();

		//Validate Shipping Address & Method
		if ($this->cart->hasShipping()) {
			if (!$this->cart->hasShippingAddress()) {
				$this->error['shipping_address'] = _l('You must specify a Delivery Address!');
				return false;
			}

			if (!$this->cart->hasShippingMethod()) {
				$this->error['shipping_method'] = _l('There was no Delivery Method specified!');
			}
		}

		//Validate Payment Address & Method
		if (!$this->cart->hasPaymentAddress()) {
			$this->error['payment_address'] = _l('You must specify a Billing Address!');
			return false;
		}

		if (!$this->cart->hasPaymentMethod()) {
			$this->error['payment_method'] = _l('There was no Payment Method specified!');
			return false;
		}

		//Customer Checkout
		if ($this->customer->isLogged()) {
			$data = $this->customer->info();
		} elseif (option('config_guest_checkout')) {
			$data['customer_id']       = 0;
			$data['customer_group_id'] = option('config_customer_group_id');
			$data += $this->cart->loadGuestInfo();
		} else {
			//Guest checkout not allowed and customer not logged in
			$this->error['guest_checkout'] = "You must be logged in to complete the checkout process!";
			return false;
		}

		//Order Information
		$data['store_id']       = option('store_id');
		$data['language_id']    = option('config_language_id');
		$data['currency_code']  = $this->currency->getCode();
		$data['currency_value'] = $this->currency->getValue();

		//Payment info
		$transaction = array(
			'description'  => _l('Order payment'),
			'amount'       => $this->cart->getTotal(),
			'payment_code' => $this->cart->getPaymentCode(),
			'payment_key'  => $this->cart->getPaymentKey(),
			'address_id'   => $this->cart->getPaymentAddressId(),
		);

		$data['transaction_id'] = $this->transaction->add('order', $transaction);

		//Shipping info
		if ($this->cart->hasShipping()) {

			$shipping = array(
				'shipping_code' => $this->cart->getShippingCode(),
				'shipping_key'  => $this->cart->getShippingKey(),
				'tracking'      => '',
				'address_id'    => $this->cart->getShippingAddressId(),
			);

			$data['shipping_id'] = $this->shipping->add('order', $shipping);
		}

		//Totals
		$data['total']  = $this->cart->getTotal();
		$data['totals'] = $this->cart->getTotals();

		//Products
		$cart_products = $this->cart->getProducts();

		foreach ($cart_products as &$cart_product) {
			$cart_product['tax'] = $this->tax->getTax($cart_product['total'], $cart_product['product']['tax_class_id']);
		}
		unset($product);

		$data['products'] = $cart_products;

		// Gift Voucher
		if ($this->cart->hasVouchers()) {
			$data['vouchers'] = $this->cart->getVouchers();
		}

		//Comments
		$data['comment'] = $this->cart->getComment();

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

		$this->session->set('order_id', $order_id);

		return $order_id;
	}

	public function setPaymentMethod($order_id, $payment_code, $payment_key = null)
	{
		$order = $this->get($order_id);

		$data = array(
			'payment_code' => $payment_code,
			'payment_key'  => $payment_key,
		);

		$result = $this->transaction->edit($order['transaction_id'], $data);

		if (!$result) {
			$this->error = $this->transaction->getError();
		}

		return $result;
	}

	public function hasOrder()
	{
		return $this->session->has('order_id');
	}

	public function getId()
	{
		return $this->session->get('order_id');
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

		$where = array(
			'customer_id' => 0,
			'email '      => $customer['email'],
		);

		$this->update('order', array('customer_id' => $customer['customer_id']), $where);
	}

	public function countOrdersWithStatus($order_status_id)
	{
		$filter = array(
			'order_status_ids' => array($order_status_id),
		);

		return $this->System_Model_Order->getTotalOrders($filter);
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
			'confirmed'   => 1,
		);

		return $this->System_Model_Order->getTotalOrders($filter);
	}

	public function isEditable($order)
	{
		if (!is_array($order)) {
			$order = $this->get($order);
		}

		return strtotime($order['date_added']) > strtotime('-' . (int)option('config_order_edit') . ' day');
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

	public function getPaymentAddress($order_id)
	{
		$query = "SELECT * FROM " . DB_PREFIX . "address a" .
			" JOIN " . DB_PREFIX . "transaction t ON (t.address_id = a.address_id)" .
			" JOIN " . DB_PREFIX . "order o ON (o.transaction_id = t.transaction_id)" .
			" WHERE o.order_id = " . (int)$order_id . " LIMIT 1";

		return $this->queryRow($query);
	}

	public function getShippingAddress($order_id)
	{
		$query = "SELECT * FROM " . DB_PREFIX . "address a" .
			" JOIN " . DB_PREFIX . "shipping s ON (s.address_id = a.address_id)" .
			" JOIN " . DB_PREFIX . "order o ON (o.shipping_id = s.shipping_id)" .
			" WHERE o.order_id = " . (int)$order_id . " LIMIT 1";

		return $this->queryRow($query);
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

	public function confirmOrder($order_id = null, $comment = '', $notify = false)
	{
		if (!$order_id) {
			$order_id = $this->session->get('order_id');
		}

		return $this->updateOrder($order_id, option('config_order_complete_status_id'), $comment, $notify);
	}

	public function updateOrder($order_id, $order_status_id, $comment = '', $notify = false)
	{
		$order = $this->get($order_id);

		//order does not exist or has already been processed
		if (!$order || $order['order_status_id'] === $order_status_id) {
			$this->error['status'] = _l("The status was unchanged.");
			return false;
		}

		// Fraud Detection
		if (option('config_fraud_detection') && $this->fraud->atRisk($order)) {
			$order_status_id = option('config_order_fraud_status_id');
		}

		// Blacklist
		if ($order['customer_id'] && $this->customer->isBlacklisted($order['customer_id'], array($order['ip']))) {
			$order_status_id = option('config_order_blacklist_status_id');
		}

		if (!$order['confirmed'] && $order_status_id === option('config_order_complete_status_id')) {
			if (!$this->confirm($order)) {
				return false;
			}
		}

		$data = array(
			'order_status_id' => $order_status_id,
			'date_modified'   => $this->date->now(),
		);

		$this->update('order', $data, $order_id);

		$history_data = array(
			'order_status_id' => $order_status_id,
			'comment'         => $comment,
			'notify'          => $notify,
		);

		$this->addHistory($order_id, $history_data);

		if ($notify) {
			$this->mail->sendTemplate('order_update_notify', $comment, $order_status_id, $order);
		}

		return true;
	}

	private function confirm($order)
	{
		$order_id = (int)$order['order_id'];

		//Confirm the Transaction. If the transaction failed to make the payment, do not confirm the order.
		if (!$this->transaction->confirm($order['transaction_id'])) {
			$this->error = $this->transaction->getError();
			return false;
		}

		$order['transaction'] = $this->transaction->get($order['transaction_id']);

		//Confirm Shipping
		$this->shipping->confirm($order['shipping_id']);
		$order['shipping'] = $this->shipping->get($order['shipping_id']);

		//Confirm Order
		$this->query("UPDATE " . DB_PREFIX . "order SET confirmed = 1 WHERE order_id = $order_id");

		// Products
		$order_products = $this->queryRows("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = $order_id");

		foreach ($order_products as &$product) {
			//subtract Quantity from this product
			$this->query("UPDATE " . DB_PREFIX . "product SET quantity = (quantity - " . (int)$product['quantity'] . ") WHERE product_id = '" . (int)$product['product_id'] . "' AND subtract = '1'");

			//Subtract Quantities from Product Option Values and Restrictions
			$product_option_values = $this->queryRows("SELECT pov.product_option_value_id, pov.option_value_id FROM " . DB_PREFIX . "order_option oo LEFT JOIN " . DB_PREFIX . "product_option_value pov ON (pov.product_option_value_id=oo.product_option_value_id) WHERE oo.order_id = '$order_id' AND order_product_id = '" . (int)$product['order_product_id'] . "'");

			$pov_to_ov = array();

			foreach ($product_option_values as $option_value) {
				$pov_to_ov[$option_value['product_option_value_id']] = $option_value['option_value_id'];
			}

			$order_options = $this->System_Model_Order->getOrderProductOptions($order_id, $product['order_product_id']);

			foreach ($order_options as $option) {
				$this->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity - " . (int)$product['quantity'] . ") WHERE product_option_value_id = '" . (int)$option['product_option_value_id'] . "' AND subtract = '1'");

				$this->query(
					"UPDATE " . DB_PREFIX . "product_option_value_restriction SET quantity = (quantity - " . (int)$product['quantity'] . ")" .
					" WHERE product_option_value_id = '" . ($pov_to_ov[$option['product_option_value_id']]) . "' AND restrict_product_option_value_id IN (" . implode(',', $pov_to_ov) . ")"
				);
			}

			//Add Product Options to product data
			$product['options'] = $order_options;

			//We must invalidate the product for each order to keep the product quantities valid!
			$this->cache->delete('product.' . $product['product_id']);
		}
		unset($product);

		$order['order_products'] = $order_products;

		// Downloads
		$order['order_downloads'] = $this->queryRows("SELECT * FROM " . DB_PREFIX . "order_download WHERE order_id = '$order_id'");

		// Gift Voucher
		$order_vouchers = $this->queryRows("SELECT voucher_id FROM " . DB_PREFIX . "order_voucher WHERE order_id = '$order_id'");

		foreach ($order_vouchers as $voucher) {
			$this->System_Model_Voucher->activate($voucher['voucher_id']);
		}

		$order['order_vouchers'] = $order_vouchers;

		// Order Totals
		$order_totals = $this->queryRows("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '$order_id' ORDER BY sort_order ASC");

		foreach ($order_totals as &$order_total) {
			$extension = $this->System_Extension_Total->get($order_total['code']);

			if (method_exists($extension, 'confirm')) {
				$extension->confirm($order, $order_total);
			}
		}

		$order['order_totals'] = $order_totals;

		//Order Status
		$order['order_status'] = $this->order->getOrderStatus($order['order_status_id']);

		//Send Order Emails
		$this->mail->sendTemplate('order', $order);

		return true;
	}

	public function addHistory($order_id, $data)
	{
		$data['order_id']   = $order_id;
		$data['date_added'] = $this->date->now();

		$this->insert('order_history', $data);
	}

	public function clear()
	{
		$this->session->remove('order_id');
	}
}
