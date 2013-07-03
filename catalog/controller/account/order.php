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
			$order_info = $this->Model_Account_Order->getOrder($order_id);
			
			if ($order_info) {
				$order_products = $this->Model_Account_Order->getOrderProducts($order_id);
						
				foreach ($order_products as $order_product) {
					$option_data = array();
							
					$order_options = $this->Model_Account_Order->getOrderOptions($order_id, $order_product['order_product_id']);
					
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
		
		$url = $this->url->getQuery('page');
		
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('account/order', $url));
		
		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = 1;
		}
		
		$this->data['orders'] = array();
		
		$order_total = $this->Model_Account_Order->getTotalOrders();
		
		$results = $this->Model_Account_Order->getOrders(($page - 1) * 10, 10);
		
		foreach ($results as $result) {
			$product_total = $this->Model_Account_Order->getTotalOrderProductsByOrderId($result['order_id']);
			$voucher_total = $this->Model_Account_Order->getTotalOrderVouchersByOrderId($result['order_id']);

			$this->data['orders'][] = array(
				'order_id'	=> $result['order_id'],
				'name'		=> $result['firstname'] . ' ' . $result['lastname'],
				'status'	=> $result['status'],
				'date_added' => $this->date->format($result['date_added'], $this->language->getInfo('date_format_short')),
				'products'	=> ($product_total + $voucher_total),
				'total'		=> $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
				'href'		=> $this->url->link('account/order/info', 'order_id=' . $result['order_id']),
				'reorder'	=> $this->url->link('account/order', 'order_id=' . $result['order_id'])
			);
		}

		$this->pagination->init();
		$this->pagination->total = $order_total;
		$this->data['pagination'] = $this->pagination->render();

		$this->data['continue'] = $this->url->link('account/account');

		$this->data['breadcrumbs'] = $this->breadcrumb->render();

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
	
	public function info()
	{
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/order/info', 'order_id=' . $order_id);
			
			$this->url->redirect($this->url->link('account/login'));
		}
			
		$this->language->load('account/order');
		
		$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : 0;
		
		$order_info = $this->Model_Account_Order->getOrder($order_id);
		
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
			
			if ($order_info['invoice_no']) {
				$this->data['invoice_no'] = $order_info['invoice_prefix'] . $order_info['invoice_no'];
			}
			
			$this->data['order_id'] = $order_id;
			$this->data['date_added'] = $this->date->format($order_info['date_added'], 'short');
			
			$shipping_address = array(
				'firstname' => $order_info['shipping_firstname'],
				'lastname'  => $order_info['shipping_lastname'],
				'company'	=> $order_info['shipping_company'],
				'address_1' => $order_info['shipping_address_1'],
				'address_2' => $order_info['shipping_address_2'],
				'city'		=> $order_info['shipping_city'],
				'postcode'  => $order_info['shipping_postcode'],
				'zone'		=> $order_info['shipping_zone'],
				'zone_code' => $order_info['shipping_zone_code'],
				'country'	=> $order_info['shipping_country'],
				'country_id'=> $order_info['shipping_country_id'],
			);

			$this->data['shipping_address'] = $this->tool->formatAddress($shipping_address);
			
			$payment_address = array(
				'firstname' => $order_info['payment_firstname'],
				'lastname'  => $order_info['payment_lastname'],
				'company'	=> $order_info['payment_company'],
				'address_1' => $order_info['payment_address_1'],
				'address_2' => $order_info['payment_address_2'],
				'city'		=> $order_info['payment_city'],
				'postcode'  => $order_info['payment_postcode'],
				'zone'		=> $order_info['payment_zone'],
				'zone_code' => $order_info['payment_zone_code'],
				'country'	=> $order_info['payment_country'],
				'country_id'=> $order_info['payment_country_id'],
			);
			
			$this->data['payment_address'] = $this->tool->formatAddress($payment_address);
			
			$this->data['shipping_method'] = $this->cart->getShippingMethodTitle($order_info['shipping_code'] . '__' . $order_info['shipping_method']);
			$this->data['payment_method'] = $this->cart->getPaymentMethodTitle($order_info['payment_method']);
			
			//Order Products
			$products = $this->Model_Account_Order->getOrderProducts($order_id);

			foreach ($products as &$product) {
				$options = $this->Model_Account_Order->getOrderOptions($order_id, $product['order_product_id']);

				foreach ($options as &$option) {
					$option['value'] = $this->tool->limit_characters($option['value'], 20);
				} unset($option);
				
				$product['is_final'] = (int)$product['is_final'];
				$product['option'] = $options;
				$product['price'] = $this->currency->format($product['price'], $order_info['currency_code'], $order_info['currency_value']);
				$product['total'] = $this->currency->format($product['total'], $order_info['currency_code'], $order_info['currency_value']);
				$product['return'] = $this->url->link('account/return/insert', 'order_id=' . $order_info['order_id'] . '&product_id=' . $product['product_id']);
			} unset($product);
			
			$this->data['products'] = $products;
			
			//Voucher
			$vouchers = $this->Model_Account_Order->getOrderVouchers($order_id);
			
			foreach ($vouchers as &$voucher) {
				$voucher['amount'] = $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value']);
			} unset($voucher);

			$this->data['vouchers'] = $vouchers;
			
			$this->data['totals'] = $this->Model_Account_Order->getOrderTotals($order_id);
			
			$this->data['comment'] = nl2br($order_info['comment']);
			
			//History
			$histories = $this->Model_Account_Order->getOrderHistories($order_id);

			foreach ($histories as &$history) {
				$history['date_added'] = $this->date->format($history['date_added'], 'short');
				$history['comment'] = nl2br($history['comment']);
			} unset($history);

			$this->data['histories'] = $histories;
			
			$this->data['continue'] = $this->url->link('account/order');

			$this->data['breadcrumbs'] = $this->breadcrumb->render();

			$this->children = array(
				'common/column_left',
				'common/column_right',
				'common/content_top',
				'common/content_bottom',
				'common/footer',
				'common/header'
			);
								
			$this->response->setOutput($this->render());
		} else {
			$this->template->load('error/not_found');

			$this->document->setTitle($this->_('text_order'));
			
			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_account'), $this->url->link('account/account'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('account/order'));
			$this->breadcrumb->add($this->_('text_order'), $this->url->link('account/order/info', 'order_id=' . $order_id));
			
			$this->language->set('heading_title', $this->_('text_order'));
															
			$this->data['continue'] = $this->url->link('account/order');

			$this->data['breadcrumbs'] = $this->breadcrumb->render();

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
}