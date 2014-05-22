<?php

class App_Controller_Account_Order extends Controller
{
	public function index()
	{
		//Login Validation
		if (!$this->customer->isLogged()) {
			$this->session->set('redirect', site_url('account/order'));

			redirect('customer/login');
		}

		//Page Head
		$this->document->setTitle(_l("Order History"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Account"), site_url('account'));
		$this->breadcrumb->add(_l("Order History"), site_url('account/order'));

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
			$order['products']     = ($product_total + $voucher_total);
			$order['order_status'] = $this->order->getOrderStatus($order['order_status_id']);
			$order['href']         = site_url('account/order/info', 'order_id=' . $order['order_id']);
			$order['reorder']      = site_url('account/order/reorder', 'order_id=' . $order['order_id']);

		}
		unset($order);

		$data['orders'] = $orders;

		//Render Limit Menu
		//$data['limits'] = $this->sort->renderLimits();

		//Pagination
		$this->pagination->init();
		$this->pagination->total = $order_total;

		$data['pagination'] = $this->pagination->render();

		//Render
		$this->response->setOutput($this->render('account/order_list', $data));
	}

	public function info()
	{
		//Order ID
		$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : 0;

		//Login Validation
		if (!$this->customer->isLogged()) {
			$this->request->setRedirect(site_url('account/order/info', 'order_id=' . $order_id), 'login');

			redirect('customer/login');
		}

		//Order Validation
		$order                = $this->order->get($order_id);
		$order['transaction'] = $this->transaction->get($order['transaction_id']);
		$order['shipping']    = $this->shipping->get($order['shipping_id']);

		if (!$order) {
			$this->message->add('warning', _l("Unable to find requested order. Please choose an order to view from the list."));

			redirect('account/order');
		}

		//Page Head
		$this->document->setTitle(_l("Order Information"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Account"), site_url('account'));
		$this->breadcrumb->add(_l("Order History"), site_url('account/order'));
		$this->breadcrumb->add(_l("Order Information"), $this->url->here());

		//Shipping / Payment Addresses
		$order['payment_address']  = $this->order->getPaymentAddress($order_id);
		$order['payment_method']   = $this->System_Extension_Payment->get($order['transaction']['payment_code'])->info();
		$order['shipping_address'] = $this->order->getShippingAddress($order_id);
		$order['shipping_method']  = $this->System_Extension_Shipping->get($order['shipping']['shipping_code'])->info();

		//Order Products
		$products = $this->System_Model_Order->getOrderProducts($order_id);

		foreach ($products as &$product) {
			$product += $this->Model_Catalog_Product->getProduct($product['product_id']);

			$options = $this->System_Model_Order->getOrderProductOptions($order_id, $product['order_product_id']);

			foreach ($options as &$option) {
				$option = $this->Model_Catalog_Product->getProductOptionValue($product['product_id'], $option['product_option_id'], $option['product_option_value_id']);
				$option += $this->Model_Catalog_Product->getProductOption($product['product_id'], $option['product_option_id']);
			}
			unset($option);

			$product['options'] = $options;

			$product['return_policy']   = $this->cart->getReturnPolicy($product['return_policy_id']);
			$product['shipping_policy'] = $this->cart->getShippingPolicy($product['shipping_policy_id']);

			if ($product['return_policy']['days'] >= 0) {
				$product['return'] = site_url('account/return/insert', 'order_id=' . $order['order_id'] . '&product_id=' . $product['product_id']);
			}
		}
		unset($product);

		$order['products'] = $products;

		//Voucher
		$order['vouchers'] = $this->System_Model_Order->getOrderVouchers($order_id);

		//History
		$history_filter = array(
			'order_id' => $order_id,
		);

		$histories = $this->System_Model_Order->getOrderHistories($history_filter);

		foreach ($histories as &$history) {
			$history['order_status'] = $this->order->getOrderStatus($history['order_status_id']);
		}
		unset($history);

		$order['histories'] = $histories;

		//Totals
		$order['totals'] = $this->System_Model_Order->getOrderTotals($order_id);

		//Render
		$this->response->setOutput($this->render('account/order_info', $order));
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

				redirect('cart');
			}
		}

		$this->message->add('warning', _l("Unable to locate the order you have requested to reorder."));

		$this->index();
	}
}
