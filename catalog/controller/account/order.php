<?php
class Catalog_Controller_Account_Order extends Controller 
{
	public function index()
	{
		$this->template->load('account/order_list');

		if (!$this->customer->isLogged()) {
				$this->session->data['redirect'] = $this->url->link('account/order');

			$this->url->redirect($this->url->link('account/login'));
		}
		
		$this->language->load('account/order');
		
		$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : false;
		if ($order_id) {
			$order_info = $this->order->get($order_id);
			
			if ($order_info) {
				$order_products = $this->System_Model_Order->getOrderProducts($order_id);
						
				foreach ($order_products as $order_product) {
					$option_data = array();
							
					$order_options = $this->System_Model_Order->getOrderProductOptions($order_id, $order_product['order_product_id']);
					
					foreach ($order_options as $order_option) {
						$option_data[$order_option['product_option_id']][] = $order_option;
					}
					
					$this->message->add('success', $this->_('text_success', $order_id));
					
					$this->cart->add($order_product['product_id'], $order_product['quantity'], $option_data);
				}
									
				$this->url->redirect($this->url->link('cart/cart'));
			}
		}

		$this->document->setTitle($this->_('heading_title'));
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_account'), $this->url->link('account/account'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('account/order', $this->url->getQuery('page')));
		
		$sort = $this->sort->getQueryDefaults('order_id', 'ASC', 10);
		
		$order_total = $this->System_Model_Order->getTotalOrders($sort);
		$orders = $this->System_Model_Order->getOrders($sort);
		
		foreach ($orders as $order) {
			$product_total = $this->System_Model_Order->getTotalOrderProducts($order['order_id']);
			$voucher_total = $this->System_Model_Order->getTotalOrderVouchers($order['order_id']);

			$order['name'] = $order['firstname'] . ' ' . $order['lastname'];
			$order['date_added'] = $this->date->format($order['date_added'], 'short');
			$order['products'] = ($product_total + $voucher_total);
			$order['total'] = $this->currency->format($order['total'], $order['currency_code'], $order['currency_value']);
			$order['href'] = $this->url->link('account/order/info', 'order_id=' . $order['order_id']);
			$order['reorder'] = $this->url->link('account/order', 'order_id=' . $order['order_id']);
		}
		
		$this->data['orders'] = $orders;

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
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/order/info', 'order_id=' . $order_id);
			
			$this->url->redirect($this->url->link('account/login'));
		}
			
		$this->language->load('account/order');
		
		$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : 0;
		
		$order_info = $this->order->get($order_id);
		
		if ($order_info) {
			$this->template->load('account/order_info');

			$this->document->setTitle($this->_('text_order'));
			
			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_account'), $this->url->link('account/account'));
			
			$url = $this->url->getQuery('page');
			
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('account/order', $url));
			$this->breadcrumb->add($this->_('text_order'), $this->url->link('account/order/info', 'order_id=' . $order_id . $url));
			
			$this->language->set('heading_title', $this->_('text_order'));
		
			$this->_('final_sale_explanation', $this->url->link('information/information/info', 'information_id=7'));
			
			$this->data['order_id'] = $order_id;
			$this->data['date_added'] = $this->date->format($order_info['date_added'], 'short');
			
			$shipping_address = array();
			
			foreach ($order_info as $info) {
				if (preg_match("/^shipping_/", $key)) {
					$shipping_address[substr($key, 9)] = $info;
				}
			}
			
			html_dump($shipping_address, 'ship_add');
			exit;

			$this->data['shipping_address'] = $this->address->format($shipping_address);
			
			$payment_address = array();
			
			foreach ($order_info as $info) {
				if (preg_match("/^payment_/", $key)) {
					$payment_address[substr($key, 8)] = $info;
				}
			}
			
			$this->data['payment_address'] = $this->address->format($payment_address);
			
			$this->data['shipping_method'] = $this->cart->getShippingMethodTitle($order_info['shipping_code'] . '__' . $order_info['shipping_method']);
			$this->data['payment_method'] = $this->cart->getPaymentMethodTitle($order_info['payment_method']);
			
			//Order Products
			$products = $this->System_Model_Order->getOrderProducts($order_id);

			foreach ($products as &$product) {
				$options = $this->System_Model_Order->getOrderProductOptions($order_id, $product['order_product_id']);

				foreach ($options as &$option) {
					$option['value'] = $this->tool->limit_characters($option['value'], 20);
				} unset($option);
				
				$product['option'] = $options;
				$product['price'] = $this->currency->format($product['price'], $order_info['currency_code'], $order_info['currency_value']);
				$product['total'] = $this->currency->format($product['total'], $order_info['currency_code'], $order_info['currency_value']);
				
				$product['return_policy'] = $this->cart->getReturnPolicy($product['return_policy_id']);
				$product['shipping_policy'] = $this->cart->getShippingPolicy($product['shipping_policy_id']);
				
				if ($product['return_policy']['days'] >= 0) {
					$product['return'] = $this->url->link('account/return/insert', 'order_id=' . $order_info['order_id'] . '&product_id=' . $product['product_id']);
				}
				
			} unset($product);
			
			$this->data['products'] = $products;
			
			//Voucher
			$vouchers = $this->System_Model_Order->getOrderVouchers($order_id);
			
			foreach ($vouchers as &$voucher) {
				$voucher['amount'] = $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value']);
			} unset($voucher);

			$this->data['vouchers'] = $vouchers;
			
			$this->data['totals'] = $this->System_Model_Order->getOrderTotals($order_id);
			
			$this->data['comment'] = nl2br($order_info['comment']);
			
			//History
			$histories = $this->System_Model_Order->getOrderHistories($order_id);

			foreach ($histories as &$history) {
				$history['date_added'] = $this->date->format($history['date_added'], 'short');
				$history['comment'] = nl2br($history['comment']);
			} unset($history);

			$this->data['histories'] = $histories;
			
			$this->data['continue'] = $this->url->link('account/order');
		} else {
			$this->template->load('error/not_found');

			$this->document->setTitle($this->_('text_order'));
			
			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_account'), $this->url->link('account/account'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('account/order'));
			$this->breadcrumb->add($this->_('text_order'), $this->url->link('account/order/info', 'order_id=' . $order_id));
			
			$this->language->set('heading_title', $this->_('text_order'));
			
			$this->data['continue'] = $this->url->link('account/order');
		}
		
		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);
		
		$this->response->setOutput($this->render());
  	}
}