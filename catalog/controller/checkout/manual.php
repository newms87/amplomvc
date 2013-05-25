<?php 

//TODO: This Process has been forsaken... Gotta fix at some point right?

class ControllerCheckoutManual extends Controller {
	public function index() {
		
		$this->load->language('checkout/manual');
		
		$json = array();
		
		if ($this->user->isLogged() && $this->user->hasPermission('modify', 'sale/order')) {	
			// Reset everything
			$this->cart->clear();
			$this->customer->logout();
			
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);			
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['coupons']);
			unset($this->session->data['reward']);
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);

			// Settings
			$settings = $this->model_setting_setting->getSetting('config', $_POST['store_id']);
			
			foreach ($settings as $key => $value) {
				$this->config->set($key, $value);
			}
			
			// Customer
			if ($_POST['customer_id']) {
				$customer_info = $this->model_account_customer->getCustomer($_POST['customer_id']);

				if ($customer_info) {
					$this->customer->login($customer_info['email'], '', true);
				} else {
					$json['error']['customer'] = $this->_('error_customer');
				}
			}
				
			// Product
			if (isset($_POST['order_product'])) {
				foreach ($_POST['order_product'] as $order_product) {
					$product_info = $this->model_catalog_product->getProduct($order_product['product_id']);
				
					if ($product_info) {	
						$option_data = array();
						
						if (isset($order_product['order_option'])) {
							foreach ($order_product['order_option'] as $option) {
								if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'image') { 
									$option_data[$option['product_option_id']] = $option['product_option_value_id'];
								} elseif ($option['type'] == 'checkbox') {
									$option_data[$option['product_option_id']][] = $option['product_option_value_id'];
								} elseif ($option['type'] == 'text' || $option['type'] == 'textarea' || $option['type'] == 'file' || $option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {
									$option_data[$option['product_option_id']] = $option['value'];						
								}
							}
						}
															
						$this->cart->add($order_product['product_id'], $order_product['quantity'], $option_data);
					}
				}
			}
			
			if (isset($_POST['product_id'])) {
				$product_info = $this->model_catalog_product->getProduct($_POST['product_id']);
				
				if ($product_info) {
					if (isset($_POST['quantity'])) {
						$quantity = $_POST['quantity'];
					} else {
						$quantity = 1;
					}
																
					if (isset($_POST['option'])) {
						$option = array_filter($_POST['option']);
					} else {
						$option = array();	
					}
					
					$product_options = $this->model_catalog_product->getProductOptions($_POST['product_id']);
					
					foreach ($product_options as $product_option) {
						if ($product_option['required'] && empty($option[$product_option['product_option_id']])) {
							$json['error']['product']['option'][$product_option['product_option_id']] = sprintf($this->_('error_required'), $product_option['name']);
						}
					}
					
					if (!isset($json['error']['product']['option'])) {
						$this->cart->add($_POST['product_id'], $quantity, $option);
					}
				}
			}
			
			if (!$this->cart->hasStock() && (!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning'))) {
				$json['error']['product']['stock'] = $this->_('error_stock');
			}
		
			// Tax
			if ($this->cart->hasShipping()) {
				$this->tax->setShippingAddress($_POST['shipping_country_id'], $_POST['shipping_zone_id'], $_POST['shipping_postcode']);
			} else {
				$this->tax->setShippingAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));
			}
			
			$this->tax->setPaymentAddress($_POST['payment_country_id'], $_POST['payment_zone_id'], $_POST['shipping_postcode']);				
			$this->tax->setStoreAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));	
			
			// Products
			$json['order_product'] = array();
			
			$products = $this->cart->getProducts();
			
			foreach ($products as $product) {
				$product_total = 0;
					
				foreach ($products as $product_2) {
					if ($product_2['product_id'] == $product['product_id']) {
						$product_total += $product_2['quantity'];
					}
				}	
								
				if ($product['minimum'] > $product_total) {
					$json['error']['product']['minimum'][] = sprintf($this->_('error_minimum'), $product['name'], $product['minimum']);
				}	
								
				$option_data = array();

				foreach ($product['option'] as $option) {
					$option_data[] = array(
						'product_option_id'		=> $option['product_option_id'],
						'product_option_value_id' => $option['product_option_value_id'],
						'name'						=> $option['name'],
						'value'						=> $option['option_value'],
						'type'						=> $option['type']
					);
				}
		
				$download_data = array();
				
				foreach ($product['download'] as $download) {
					$download_data[] = array(
						'name'		=> $download['name'],
						'filename'  => $download['filename'],
						'mask'		=> $download['mask'],
						'remaining' => $download['remaining']
					);
				}
								
				$json['order_product'][] = array(
					'product_id' => $product['product_id'],
					'name'		=> $product['name'],
					'model'		=> $product['model'], 
					'option'	=> $option_data,
					'download'	=> $download_data,
					'quantity'	=> $product['quantity'],
					'price'		=> $product['price'],	
					'total'		=> $product['total'],	
					'tax'		=> $this->tax->getTax($product['total'], $product['tax_class_id']),
					'reward'	=> $product['reward']				
				);
			}

			// Voucher
			$this->session->data['vouchers'] = array();
			
			if (isset($_POST['order_voucher'])) {
				foreach ($_POST['order_voucher'] as $voucher) {
					$this->session->data['vouchers'][] = array(
						'voucher_id'		=> $voucher['voucher_id'],
						'description'		=> $voucher['description'],
						'code'				=> substr(md5(rand()), 0, 7),
						'from_name'		=> $voucher['from_name'],
						'from_email'		=> $voucher['from_email'],
						'to_name'			=> $voucher['to_name'],
						'to_email'			=> $voucher['to_email'],
						'voucher_theme_id' => $voucher['voucher_theme_id'], 
						'message'			=> $voucher['message'],
						'amount'			=> $voucher['amount']	
					);
				}
			}

			// Add a new voucher if set
			if (isset($_POST['from_name']) && isset($_POST['from_email']) && isset($_POST['to_name']) && isset($_POST['to_email']) && isset($_POST['amount'])) {
				if ((strlen($_POST['from_name']) < 1) || (strlen($_POST['from_name']) > 64)) {
					$json['error']['vouchers']['from_name'] = $this->_('error_from_name');
				}  
			
				if ((strlen($_POST['from_email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $_POST['from_email'])) {
					$json['error']['vouchers']['from_email'] = $this->_('error_email');
				}
			
				if ((strlen($_POST['to_name']) < 1) || (strlen($_POST['to_name']) > 64)) {
					$json['error']['vouchers']['to_name'] = $this->_('error_to_name');
				}		
			
				if ((strlen($_POST['to_email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $_POST['to_email'])) {
					$json['error']['vouchers']['to_email'] = $this->_('error_email');
				}
			
				if (($_POST['amount'] < 1) || ($_POST['amount'] > 1000)) {
					$json['error']['vouchers']['amount'] = sprintf($this->_('error_amount'), $this->currency->format(1, false, 1), $this->currency->format(1000, false, 1) . ' ' . $this->config->get('config_currency'));
				}
			
				if (!isset($json['error']['vouchers'])) { 
					$voucher_data = array(
						'order_id'			=> 0,
						'code'				=> substr(md5(rand()), 0, 7),
						'from_name'		=> $_POST['from_name'],
						'from_email'		=> $_POST['from_email'],
						'to_name'			=> $_POST['to_name'],
						'to_email'			=> $_POST['to_email'],
						'voucher_theme_id' => $_POST['voucher_theme_id'], 
						'message'			=> $_POST['message'],
						'amount'			=> $_POST['amount'],
						'status'			=> true				
					); 
					
					$voucher_id = $this->model_cart_voucher->addVoucher(0, $voucher_data);  
									
					$this->session->data['vouchers'][] = array(
						'voucher_id'		=> $voucher_id,
						'description'		=> sprintf($this->_('text_for'), $this->currency->format($_POST['amount'], $this->config->get('config_currency')), $_POST['to_name']),
						'code'				=> substr(md5(rand()), 0, 7),
						'from_name'		=> $_POST['from_name'],
						'from_email'		=> $_POST['from_email'],
						'to_name'			=> $_POST['to_name'],
						'to_email'			=> $_POST['to_email'],
						'voucher_theme_id' => $_POST['voucher_theme_id'], 
						'message'			=> $_POST['message'],
						'amount'			=> $_POST['amount']				
					); 
				}
			}
			
			$json['order_voucher'] = array();
					
			foreach ($this->session->data['vouchers'] as $voucher) {
				$json['order_voucher'][] = array(
					'voucher_id'		=> $voucher['voucher_id'],
					'description'		=> $voucher['description'],
					'code'				=> $voucher['code'],
					'from_name'		=> $voucher['from_name'],
					'from_email'		=> $voucher['from_email'],
					'to_name'			=> $voucher['to_name'],
					'to_email'			=> $voucher['to_email'],
					'voucher_theme_id' => $voucher['voucher_theme_id'], 
					'message'			=> $voucher['message'],
					'amount'			=> $voucher['amount']	
				);
			}
						
			// Shipping
			$json['shipping_method'] = array();
			
			if ($this->cart->hasShipping()) {		
				$country_info = $this->model_localisation_country->getCountry($_POST['shipping_country_id']);
				
				if ($country_info && $country_info['postcode_required'] && (strlen($_POST['shipping_postcode']) < 2) || (strlen($_POST['shipping_postcode']) > 10)) {
					$json['error']['shipping']['postcode'] = $this->_('error_postcode');
				}
		
				if ($_POST['shipping_country_id'] == '') {
					$json['error']['shipping']['country'] = $this->_('error_country');
				}
				
				if ($_POST['shipping_zone_id'] == '') {
					$json['error']['shipping']['zone'] = $this->_('error_zone');
				}
							
				$country_info = $this->model_localisation_country->getCountry($_POST['shipping_country_id']);
				
				if ($country_info && $country_info['postcode_required'] && (strlen($_POST['shipping_postcode']) < 2) || (strlen($_POST['shipping_postcode']) > 10)) {
					$json['error']['shipping']['postcode'] = $this->_('error_postcode');
				}

				if (!isset($json['error']['shipping'])) {
					if ($country_info) {
						$country = $country_info['name'];
						$iso_code_2 = $country_info['iso_code_2'];
						$iso_code_3 = $country_info['iso_code_3'];
						$address_format = $country_info['address_format'];
					} else {
						$country = '';
						$iso_code_2 = '';
						$iso_code_3 = '';	
						$address_format = '';
					}
				
					$zone_info = $this->model_localisation_zone->getZone($_POST['shipping_zone_id']);
					
					if ($zone_info) {
						$zone = $zone_info['name'];
						$code = $zone_info['code'];
					} else {
						$zone = '';
						$code = '';
					}					
	
					$address_data = array(
						'firstname'		=> $_POST['shipping_firstname'],
						'lastname'		=> $_POST['shipping_lastname'],
						'company'		=> $_POST['shipping_company'],
						'address_1'		=> $_POST['shipping_address_1'],
						'address_2'		=> $_POST['shipping_address_2'],
						'postcode'		=> $_POST['shipping_postcode'],
						'city'			=> $_POST['shipping_city'],
						'zone_id'		=> $_POST['shipping_zone_id'],
						'zone'			=> $zone,
						'zone_code'		=> $code,
						'country_id'	=> $_POST['shipping_country_id'],
						'country'		=> $country,	
						'iso_code_2'	=> $iso_code_2,
						'iso_code_3'	=> $iso_code_3,
						'address_format' => $address_format
					);
					
					$results = $this->model_setting_extension->getExtensions('shipping');
					
					foreach ($results as $result) {
						if ($this->config->get($result['code'] . '_status')) {
							
							$quote = $this->{'model_shipping_' . $result['code']}->getQuote($address_data); 
				
							if ($quote) {
								$json['shipping_method'][$result['code']] = array( 
									'title'		=> $quote['title'],
									'quote'		=> $quote['quote'], 
									'sort_order' => $quote['sort_order'],
									'error'		=> $quote['error']
								);
							}
						}
					}
			
					$sort_order = array();
				
					foreach ($json['shipping_method'] as $key => $value) {
						$sort_order[$key] = $value['sort_order'];
					}
			
					array_multisort($sort_order, SORT_ASC, $json['shipping_method']);

					if (!$json['shipping_method']) {
						$json['error']['shipping_method'] = $this->_('error_no_shipping');
					} else {
						if (!$_POST['shipping_code']) {
							$json['error']['shipping_method'] = $this->_('error_shipping');
						} else {
							$shipping = explode('.', $_POST['shipping_code']);
							
							if (!isset($shipping[0]) || !isset($shipping[1]) || !isset($json['shipping_method'][$shipping[0]]['quote'][$shipping[1]])) {		
								$json['error']['shipping_method'] = $this->_('error_shipping');
							} else {
								$this->session->data['shipping_method'] = $json['shipping_method'][$shipping[0]]['quote'][$shipping[1]];
							}				
						}
					}					
				}
			}
			
			// Coupon
			if (!empty($_POST['coupon'])) {
				$coupon_info = $this->model_cart_coupon->getCoupon($_POST['coupon']);			
			
				if ($coupon_info) {					
					$this->session->data['coupons'][$_POST['coupon']] = $coupon_info;
				} else {
					$json['error']['coupon'] = $this->_('error_coupon');
				}
			}
			
			// Voucher
			if (!empty($_POST['voucher'])) {
				$voucher_info = $this->model_cart_voucher->getVoucher($_POST['voucher']);			
			
				if ($voucher_info) {					
					$this->session->data['voucher'] = $_POST['voucher'];
				} else {
					$json['error']['voucher'] = $this->_('error_voucher');
				}
			}
						
			// Reward Points
			if (!empty($_POST['reward'])) {
				$points = $this->customer->getRewardPoints();
				
				if ($_POST['reward'] > $points) {
					$json['error']['reward'] = sprintf($this->_('error_points'), $_POST['reward']);
				}
				
				if (!isset($json['error']['reward'])) {
					$points_total = 0;
					
					foreach ($this->cart->getProducts() as $product) {
						if ($product['points']) {
							$points_total += $product['points'];
						}
					}				
					
					if ($_POST['reward'] > $points_total) {
						$json['error']['reward'] = sprintf($this->_('error_maximum'), $points_total);
					}
					
					if (!isset($json['error']['reward'])) {		
						$this->session->data['reward'] = $_POST['reward'];
					}
				}
			}

			// Totals
			$json['order_total'] = array();					
			$total = 0;
			$taxes = $this->cart->getTaxes();
			
			$sort_order = array(); 
			
			$results = $this->model_setting_extension->getExtensions('total');
			
			foreach ($results as $key => $value) {
				$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
			}
			
			array_multisort($sort_order, SORT_ASC, $results);
			
			foreach ($results as $result) {
				if ($this->config->get($result['code'] . '_status')) {
		
					$this->{'model_total_' . $result['code']}->getTotal($json['order_total'], $total, $taxes);
				}
				
				$sort_order = array(); 
			
				foreach ($json['order_total'] as $key => $value) {
					$sort_order[$key] = $value['sort_order'];
				}
	
				array_multisort($sort_order, SORT_ASC, $json['order_total']);				
			}
		
			// Payment
			if ($_POST['payment_country_id'] == '') {
				$json['error']['payment']['country'] = $this->_('error_country');
			}
			
			if ($_POST['payment_zone_id'] == '') {
				$json['error']['payment']['zone'] = $this->_('error_zone');
			}		
			
			if (!isset($json['error']['payment'])) {
				$json['payment_methods'] = array();
				
				$country_info = $this->model_localisation_country->getCountry($_POST['payment_country_id']);
				
				if ($country_info) {
					$country = $country_info['name'];
					$iso_code_2 = $country_info['iso_code_2'];
					$iso_code_3 = $country_info['iso_code_3'];
					$address_format = $country_info['address_format'];
				} else {
					$country = '';
					$iso_code_2 = '';
					$iso_code_3 = '';	
					$address_format = '';
				}
				
				$zone_info = $this->model_localisation_zone->getZone($_POST['payment_zone_id']);
				
				if ($zone_info) {
					$zone = $zone_info['name'];
					$code = $zone_info['code'];
				} else {
					$zone = '';
					$code = '';
				}					
				
				$address_data = array(
					'firstname'		=> $_POST['payment_firstname'],
					'lastname'		=> $_POST['payment_lastname'],
					'company'		=> $_POST['payment_company'],
					'address_1'		=> $_POST['payment_address_1'],
					'address_2'		=> $_POST['payment_address_2'],
					'postcode'		=> $_POST['payment_postcode'],
					'city'			=> $_POST['payment_city'],
					'zone_id'		=> $_POST['payment_zone_id'],
					'zone'			=> $zone,
					'zone_code'		=> $code,
					'country_id'	=> $_POST['payment_country_id'],
					'country'		=> $country,	
					'iso_code_2'	=> $iso_code_2,
					'iso_code_3'	=> $iso_code_3,
					'address_format' => $address_format
				);
				
				$json['payment_method'] = array();
								
				$results = $this->model_setting_extension->getExtensions('payment');
		
				foreach ($results as $result) {
					if ($this->config->get($result['code'] . '_status')) {

						$method = $this->{'model_payment_' . $result['code']}->getMethod($address_data, $total); 
						
						if ($method) {
							$json['payment_method'][$result['code']] = $method;
						}
					}
				}
							
				$sort_order = array(); 
			
				foreach ($json['payment_method'] as $key => $value) {
					$sort_order[$key] = $value['sort_order'];
				}
		
				array_multisort($sort_order, SORT_ASC, $json['payment_method']);	
				
				if (!$json['payment_method']) {
					$json['error']['payment_method'] = $this->_('error_no_payment');
				} else {			
					if (!$_POST['payment_code']) {
						$json['error']['payment_method'] = $this->_('error_payment');
					} else {
						if (!isset($json['payment_method'][$_POST['payment_code']])) {
							$json['error']['payment_method'] = $this->_('error_payment');
						}
					}	
				}
			}
			
			if (!isset($json['error'])) { 
				$json['success'] = $this->_('text_success');
			} else {
				$json['error']['warning'] = $this->_('error_warning');
			}
			
			// Reset everything
			$this->cart->clear();
			$this->customer->logout();
			
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['coupons']);
			unset($this->session->data['reward']);
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);
		} else {
				$json['error']['warning'] = $this->_('error_permission');
		}
	
		$this->response->setOutput(json_encode($json));	
	}
}