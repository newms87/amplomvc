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
		
		$url = $this->url->get_query('page');
		
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
		
		if (isset($_GET['order_id'])) {
			$order_id = $_GET['order_id'];
		} else {
			$order_id = 0;
		}
				
		$order_info = $this->Model_Account_Order->getOrder($order_id);
		
		if ($order_info) {
		$this->template->load('account/order_info');

			$this->document->setTitle($this->_('text_order'));
			
			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_account'), $this->url->link('account/account'));
			
			$url = $this->url->get_query('page');
			
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('account/order', $url));
			$this->breadcrumb->add($this->_('text_order'), $this->url->link('account/order/info', 'order_id=' . $order_id . $url));
			
			$this->language->set('heading_title', $this->_('text_order'));
		
			$this->_('final_sale_explanation',$this->url->link('information/information', 'information_id=7'));
			
			if ($order_info['invoice_no']) {
				$this->data['invoice_no'] = $order_info['invoice_prefix'] . $order_info['invoice_no'];
			} else {
				$this->data['invoice_no'] = '';
			}
			
			$this->data['order_id'] = $_GET['order_id'];
			$this->data['date_added'] = date($this->language->getInfo('date_format_short'), strtotime($order_info['date_added']));
			
			if ($order_info['shipping_address_format']) {
					$format = $order_info['shipping_address_format'];
			} else {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
			}
		
			$find = array(
				'{firstname}',
				'{lastname}',
				'{company}',
					'{address_1}',
					'{address_2}',
				'{city}',
					'{postcode}',
					'{zone}',
				'{zone_code}',
					'{country}'
			);
	
			$replace = array(
				'firstname' => $order_info['shipping_firstname'],
				'lastname'  => $order_info['shipping_lastname'],
				'company'	=> $order_info['shipping_company'],
					'address_1' => $order_info['shipping_address_1'],
					'address_2' => $order_info['shipping_address_2'],
					'city'		=> $order_info['shipping_city'],
					'postcode'  => $order_info['shipping_postcode'],
					'zone'		=> $order_info['shipping_zone'],
				'zone_code' => $order_info['shipping_zone_code'],
					'country'	=> $order_info['shipping_country']
			);

			$this->data['shipping_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

			$this->data['shipping_method'] = $order_info['shipping_method'];

			if ($order_info['payment_address_format']) {
					$format = $order_info['payment_address_format'];
			} else {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
			}
		
			$find = array(
				'{firstname}',
				'{lastname}',
				'{company}',
					'{address_1}',
					'{address_2}',
				'{city}',
					'{postcode}',
					'{zone}',
				'{zone_code}',
					'{country}'
			);
	
			$replace = array(
				'firstname' => $order_info['payment_firstname'],
				'lastname'  => $order_info['payment_lastname'],
				'company'	=> $order_info['payment_company'],
					'address_1' => $order_info['payment_address_1'],
					'address_2' => $order_info['payment_address_2'],
					'city'		=> $order_info['payment_city'],
					'postcode'  => $order_info['payment_postcode'],
					'zone'		=> $order_info['payment_zone'],
				'zone_code' => $order_info['payment_zone_code'],
					'country'	=> $order_info['payment_country']
			);
			
			$this->data['payment_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

				$this->data['payment_method'] = $order_info['payment_method'];
			
			$this->data['products'] = array();
			
			$products = $this->Model_Account_Order->getOrderProducts($_GET['order_id']);

				foreach ($products as $product) {
				$option_data = array();
				
				$options = $this->Model_Account_Order->getOrderOptions($_GET['order_id'], $product['order_product_id']);

					foreach ($options as $option) {
						if ($option['type'] != 'file') {
						$value = $option['value'];
					} else {
						$value = substr($option['value'], 0, strrpos($option['value'], '.'));
					}
					
					$option_data[] = array(
						'name'  => $option['name'],
						'value' => (strlen($value) > 20 ? substr($value, 0, 20) . '..' : $value)
					);
				}

				$this->data['products'][] = array(
						'name'	=> $product['name'],
						'model'	=> $product['model'],
						'is_final' => (int)$product['is_final'],
						'option'	=> $option_data,
						'quantity' => $product['quantity'],
						'price'	=> $this->currency->format($product['price'], $order_info['currency_code'], $order_info['currency_value']),
					'total'	=> $this->currency->format($product['total'], $order_info['currency_code'], $order_info['currency_value']),
					'return'	=> $this->url->link('account/return/insert', 'order_id=' . $order_info['order_id'] . '&product_id=' . $product['product_id'])
				);
				}

			// Voucher
			$this->data['vouchers'] = array();
			
			$vouchers = $this->Model_Account_Order->getOrderVouchers($_GET['order_id']);
			
			foreach ($vouchers as $voucher) {
				$this->data['vouchers'][] = array(
					'description' => $voucher['description'],
					'amount'		=> $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value'])
				);
			}
			
				$this->data['totals'] = $this->Model_Account_Order->getOrderTotals($_GET['order_id']);
			
			$this->data['comment'] = nl2br($order_info['comment']);
			
			$this->data['histories'] = array();

			$results = $this->Model_Account_Order->getOrderHistories($_GET['order_id']);

				foreach ($results as $result) {
				$this->data['histories'][] = array(
						'date_added' => $this->date->format($result['date_added'], $this->language->getInfo('date_format_short')),
						'status'	=> $result['status'],
						'comment'	=> nl2br($result['comment'])
				);
				}

				$this->data['continue'] = $this->url->link('account/order');

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