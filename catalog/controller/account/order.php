<?php

class Catalog_Controller_Account_Order extends Controller
{
	public function index()
	{
		//Template and Language
		$this->template->load('account/order_list');
		//Login Validation
		if (!$this->customer->isLogged()) {
			$this->session->set('redirect', $this->url->link('account/order'));

			$this->url->redirect('account/login');
		}

		//Page Head
		$this->document->setTitle(_l("Order History"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Account"), $this->url->link('account/account'));
		$this->breadcrumb->add(_l("Order History"), $this->url->link('account/order'));

		//Get Sorted / Filtered Data
		$sort = $this->sort->getQueryDefaults('order_id', 'DESC', 10);

		$filter = array(
			'customer_ids' => array($this->customer->getId()),
		);

		$order_total = $this->System_Model_Order->getTotalConfirmedOrders($filter);
		$orders      = $this->System_Model_Order->getConfirmedOrders($filter + $sort);

		foreach ($orders as &$order) {
			$product_total = $this->System_Model_Order->getTotalOrderProducts($order['order_id']);
			$voucher_total = $this->System_Model_Order->getTotalOrderVouchers($order['order_id']);

			$order['name']         = $order['firstname'] . ' ' . $order['lastname'];
			$order['date_added']   = $this->date->format($order['date_added'], 'short');
			$order['products']     = ($product_total + $voucher_total);
			$order['total']        = $this->currency->format($order['total'], $order['currency_code'], $order['currency_value']);
			$order['order_status'] = $this->order->getOrderStatus($order['order_status_id']);
			$order['href']         = $this->url->link('account/order/info', 'order_id=' . $order['order_id']);
			$order['reorder']      = $this->url->link('account/order/reorder', 'order_id=' . $order['order_id']);

		}
		unset($order);

		$this->data['orders'] = $orders;

		//Render Limit Menu
		//$this->data['limits'] = $this->sort->renderLimits();

		//Pagination
		$this->pagination->init();
		$this->pagination->total = $order_total;

		$this->data['pagination'] = $this->pagination->render();

		//Action Buttons
		$this->data['continue'] = $this->url->link('account/account');

		//Dependencies
		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);

		//Render
		$this->response->setOutput($this->render());
	}

	public function info()
	{
		//Order ID
		$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : 0;

		//Login Validation
		if (!$this->customer->isLogged()) {
			$this->session->set('redirect', $this->url->link('account/order/info', 'order_id=' . $order_id));

			$this->url->redirect('account/login');
		}

		//Order Validation
		$order                = $this->order->get($order_id);
		$order['transaction'] = $this->transaction->get($order['transaction_id']);
		$order['shipping']    = $this->shipping->get($order['shipping_id']);

		if (!$order) {
			$this->message->add('warning', _l("Unable to find requested order. Please choose an order to view from the list."));

			$this->url->redirect('account/order');
		}

		//Page Head
		$this->document->setTitle(_l("Order Information"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Account"), $this->url->link('account/account'));
		$this->breadcrumb->add(_l("Order History"), $this->url->link('account/order'));
		$this->breadcrumb->add(_l("Order Information"), $this->url->here());

		$this->data['policies'] = $this->url->link('information/information/info', 'information_id=' . $this->config->get('config_shipping_return_info_id'));

		$order['date_added'] = $this->date->format($order['date_added'], 'datetime_long');

		//Shipping / Payment Addresses
		$this->data['payment_address']  = $this->address->format($this->order->getPaymentAddress($order_id));
		$this->data['shipping_address'] = $this->address->format($this->order->getShippingAddress($order_id));

		//Shipping / Payment Methods
		$this->data['payment_method']  = $this->System_Extension_Payment->get($order['transaction']['payment_code'])->info();
		$this->data['shipping_method'] = $this->System_Extension_Shipping->get($order['shipping']['shipping_code'])->info();

		$order['comment'] = nl2br($order['comment']);

		$this->data += $order;

		//Order Products
		$products = $this->System_Model_Order->getOrderProducts($order_id);

		foreach ($products as &$product) {
			$product += $this->Model_Catalog_Product->getProduct($product['product_id'], true);

			$options = $this->System_Model_Order->getOrderProductOptions($order_id, $product['order_product_id']);

			foreach ($options as &$option) {
				$option = $this->Model_Catalog_Product->getProductOptionValue($product['product_id'], $option['product_option_id'], $option['product_option_value_id']);
				$option += $this->Model_Catalog_Product->getProductOption($product['product_id'], $option['product_option_id']);

				if (!$option['display_value']) {
					$option['display_value'] = ($option['name'] ? $option['name'] . ': ' : '') . $option['value'];
				}

				$option['display_value'] = $this->tool->limit_characters($option['display_value'], 30);
			}
			unset($option);

			$product['options'] = $options;
			$product['price']   = $this->currency->format($product['price'], $order['currency_code'], $order['currency_value']);
			$product['total']   = $this->currency->format($product['total'], $order['currency_code'], $order['currency_value']);

			$product['return_policy']   = $this->cart->getReturnPolicy($product['return_policy_id']);
			$product['shipping_policy'] = $this->cart->getShippingPolicy($product['shipping_policy_id']);

			if ($product['return_policy']['days'] >= 0) {
				$product['return'] = $this->url->link('account/return/insert', 'order_id=' . $order['order_id'] . '&product_id=' . $product['product_id']);
			}
		}
		unset($product);

		$this->data['products'] = $products;

		//Voucher
		$vouchers = $this->System_Model_Order->getOrderVouchers($order_id);

		foreach ($vouchers as &$voucher) {
			$voucher['amount'] = $this->currency->format($voucher['amount'], $order['currency_code'], $order['currency_value']);
		}
		unset($voucher);

		$this->data['vouchers'] = $vouchers;

		//History
		$history_filter = array(
			'order_id' => $order_id,
		);

		$histories = $this->System_Model_Order->getOrderHistories($history_filter);

		foreach ($histories as &$history) {
			$history['order_status'] = $this->order->getOrderStatus($history['order_status_id']);
			$history['date_added']   = $this->date->format($history['date_added'], 'short');
			$history['comment']      = nl2br($history['comment']);
		}
		unset($history);

		$this->data['histories'] = $histories;

		//Totals
		$totals = $this->System_Model_Order->getOrderTotals($order_id);

		foreach ($totals as &$total) {
			$total['text'] = $this->currency->format($total['value'], $order['currency_code'], $order['currency_value']);
		}
		unset($total);

		$this->data['totals'] = $totals;

		//Action Buttons
		$this->data['continue'] = $this->url->link('account/order');

		//The Template
		$this->template->load('account/order_info');

		//Dependencies
		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);

		//Render
		$this->response->setOutput($this->render());
	}

	public function reorder()
	{
		$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : false;

		if ($order_id) {
			$order = $this->order->get($order_id);

			if ($order) {
				$order_products = $this->System_Model_Order->getOrderProducts($order_id);

				foreach ($order_products as $order_product) {
					$order_options = $this->System_Model_Order->getOrderProductOptions($order_id, $order_product['order_product_id']);

					$options = array();

					foreach ($order_options as $order_option) {
						$options[$order_option['product_option_id']][$order_option['product_option_value_id']] = $order_option;
					}

					$this->message->add('success', _l("You have successfully added the products from order ID #%s to your cart!", $order_id));

					$this->cart->addProduct($order_product['product_id'], $order_product['quantity'], $options);
				}

				$this->url->redirect('cart/cart');
			}
		}

		$this->message->add('warning', _l("Unable to locate the order you have requested to reorder."));

		$this->index();
	}
}
