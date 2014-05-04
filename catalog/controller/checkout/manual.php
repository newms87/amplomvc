<?php

//TODO: This Process has been forsaken... Gotta fix at some point right?

class Catalog_Controller_Checkout_Manual extends Controller
{
	public function index()
	{

		$json = array();

		if ($this->user->isLogged() && $this->user->can('modify', 'sale/order')) {
			// Reset everything
			$this->cart->clear();
			$this->customer->logout();

			$this->session->remove('shipping_method');
			$this->session->remove('shipping_methods');
			$this->session->remove('payment_method');
			$this->session->remove('payment_methods');
			$this->session->remove('coupons');
			$this->session->remove('reward');
			$this->session->remove('voucher');
			$this->session->remove('vouchers');

			// Settings
			$settings = $this->config->loadGroup('config', $_POST['store_id']);

			foreach ($settings as $key => $value) {
				$this->config->set($key, $value);
			}

			// Customer
			if ($_POST['customer_id']) {
				$customer_info = $this->customer->getCustomer($_POST['customer_id']);

				if ($customer_info) {
					//TODO: Override disabled, find new approach
					$this->customer->login($customer_info['email'], '', true);
				} else {
					$json['error']['customer'] = _l("Warning: Can not find selected customer!");
				}
			}

			// Product
			if (isset($_POST['order_product'])) {
				foreach ($_POST['order_product'] as $order_product) {
					$product_info = $this->Model_Catalog_Product->getProduct($order_product['product_id']);

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

						$this->cart->addProduct($order_product['product_id'], $order_product['quantity'], $option_data);
					}
				}
			}

			if (isset($_POST['product_id'])) {
				$product_info = $this->Model_Catalog_Product->getProduct($_POST['product_id']);

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

					$product_options = $this->Model_Catalog_Product->getProductOptions($_POST['product_id']);

					foreach ($product_options as $product_option) {
						if ($product_option['required'] && empty($option[$product_option['product_option_id']])) {
							$json['error']['product']['option'][$product_option['product_option_id']] = sprintf(_l("Please select a %s."), $product_option['name']);
						}
					}

					if (!isset($json['error']['product']['option'])) {
						$this->cart->addProduct($_POST['product_id'], $quantity, $option);
					}
				}
			}

			if (!$this->cart->hasStock() && (!option('config_stock_checkout') || option('config_stock_warning'))) {
				$json['error']['product']['stock'] = _l("Products marked with *** are not available in the desired quantity or not in stock!");
			}

			// Tax
			if ($this->cart->hasShipping()) {
				$this->tax->setShippingAddress($_POST['shipping_country_id'], $_POST['shipping_zone_id'], $_POST['shipping_postcode']);
			} else {
				$this->tax->setShippingAddress(option('config_country_id'), option('config_zone_id'));
			}

			$this->tax->setPaymentAddress($_POST['payment_country_id'], $_POST['payment_zone_id'], $_POST['shipping_postcode']);
			$this->tax->setStoreAddress(option('config_country_id'), option('config_zone_id'));

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
					$json['error']['product']['minimum'][] = sprintf(_l("Minimum order amount for %s is %s!"), $product['name'], $product['minimum']);
				}

				$option_data = array();

				foreach ($product['option'] as $option) {
					$option_data[] = array(
						'product_option_id'       => $option['product_option_id'],
						'product_option_value_id' => $option['product_option_value_id'],
						'name'                    => $option['name'],
						'value'                   => $option['option_value'],
						'type'                    => $option['type']
					);
				}

				$download_data = array();

				foreach ($product['download'] as $download) {
					$download_data[] = array(
						'name'      => $download['name'],
						'filename'  => $download['filename'],
						'mask'      => $download['mask'],
						'remaining' => $download['remaining']
					);
				}

				$json['order_product'][] = array(
					'product_id' => $product['product_id'],
					'name'       => $product['name'],
					'model'      => $product['model'],
					'option'     => $option_data,
					'download'   => $download_data,
					'quantity'   => $product['quantity'],
					'price'      => $product['price'],
					'total'      => $product['total'],
					'tax'        => $this->tax->getTax($product['total'], $product['tax_class_id']),
					'reward'     => $product['reward']
				);
			}

			// Voucher
			$this->session->set('vouchers', array());

			if (isset($_POST['order_voucher'])) {
				foreach ($_POST['order_voucher'] as $voucher) {
					$this->session->get('vouchers')[] = array(
						'voucher_id'       => $voucher['voucher_id'],
						'description'      => $voucher['description'],
						'code'             => substr(md5(rand()), 0, 7),
						'from_name'        => $voucher['from_name'],
						'from_email'       => $voucher['from_email'],
						'to_name'          => $voucher['to_name'],
						'to_email'         => $voucher['to_email'],
						'voucher_theme_id' => $voucher['voucher_theme_id'],
						'message'          => $voucher['message'],
						'amount'           => $voucher['amount']
					);
				}
			}

			// Add a new voucher if set
			if (isset($_POST['from_name']) && isset($_POST['from_email']) && isset($_POST['to_name']) && isset($_POST['to_email']) && isset($_POST['amount'])) {
				if ((strlen($_POST['from_name']) < 1) || (strlen($_POST['from_name']) > 64)) {
					$json['error']['vouchers']['from_name'] = _l("Your Name must be between 1 and 64 characters!");
				}

				if ((strlen($_POST['from_email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $_POST['from_email'])) {
					$json['error']['vouchers']['from_email'] = _l("E-Mail Address does not appear to be valid!");
				}

				if ((strlen($_POST['to_name']) < 1) || (strlen($_POST['to_name']) > 64)) {
					$json['error']['vouchers']['to_name'] = _l("Recipient's Name must be between 1 and 64 characters!");
				}

				if ((strlen($_POST['to_email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $_POST['to_email'])) {
					$json['error']['vouchers']['to_email'] = _l("E-Mail Address does not appear to be valid!");
				}

				if (($_POST['amount'] < 1) || ($_POST['amount'] > 1000)) {
					$json['error']['vouchers']['amount'] = sprintf(_l("Amount must be between %s and %s!"), $this->currency->format(1, false, 1), $this->currency->format(1000, false, 1) . ' ' . option('config_currency'));
				}

				if (!isset($json['error']['vouchers'])) {
					$voucher_data = array(
						'order_id'         => 0,
						'code'             => substr(md5(rand()), 0, 7),
						'from_name'        => $_POST['from_name'],
						'from_email'       => $_POST['from_email'],
						'to_name'          => $_POST['to_name'],
						'to_email'         => $_POST['to_email'],
						'voucher_theme_id' => $_POST['voucher_theme_id'],
						'message'          => $_POST['message'],
						'amount'           => $_POST['amount'],
						'status'           => true
					);

					$voucher_id = $this->Model_Cart_Voucher->addVoucher(0, $voucher_data);

					$this->session->get('vouchers')[] = array(
						'voucher_id'       => $voucher_id,
						'description'      => sprintf(_l("%s Gift Certificate for %s"), $this->currency->format($_POST['amount'], option('config_currency')), $_POST['to_name']),
						'code'             => substr(md5(rand()), 0, 7),
						'from_name'        => $_POST['from_name'],
						'from_email'       => $_POST['from_email'],
						'to_name'          => $_POST['to_name'],
						'to_email'         => $_POST['to_email'],
						'voucher_theme_id' => $_POST['voucher_theme_id'],
						'message'          => $_POST['message'],
						'amount'           => $_POST['amount']
					);
				}
			}

			$json['order_voucher'] = array();

			foreach ($this->session->get('vouchers') as $voucher) {
				$json['order_voucher'][] = array(
					'voucher_id'       => $voucher['voucher_id'],
					'description'      => $voucher['description'],
					'code'             => $voucher['code'],
					'from_name'        => $voucher['from_name'],
					'from_email'       => $voucher['from_email'],
					'to_name'          => $voucher['to_name'],
					'to_email'         => $voucher['to_email'],
					'voucher_theme_id' => $voucher['voucher_theme_id'],
					'message'          => $voucher['message'],
					'amount'           => $voucher['amount']
				);
			}

			// Shipping
			$json['shipping_method'] = array();

			if ($this->cart->hasShipping()) {
				$country_info = $this->Model_Localisation_Country->getCountry($_POST['shipping_country_id']);

				if ($country_info && $country_info['postcode_required'] && (strlen($_POST['shipping_postcode']) < 2) || (strlen($_POST['shipping_postcode']) > 10)) {
					$json['error']['shipping']['postcode'] = _l("Postcode must be between 2 and 10 characters!");
				}

				if ($_POST['shipping_country_id'] == '') {
					$json['error']['shipping']['country'] = _l("Please select a country!");
				}

				if ($_POST['shipping_zone_id'] == '') {
					$json['error']['shipping']['zone'] = _l("Please select a region / state!");
				}

				$country_info = $this->Model_Localisation_Country->getCountry($_POST['shipping_country_id']);

				if ($country_info && $country_info['postcode_required'] && (strlen($_POST['shipping_postcode']) < 2) || (strlen($_POST['shipping_postcode']) > 10)) {
					$json['error']['shipping']['postcode'] = _l("Postcode must be between 2 and 10 characters!");
				}

				if (!isset($json['error']['shipping'])) {
					if ($country_info) {
						$country        = $country_info['name'];
						$iso_code_2     = $country_info['iso_code_2'];
						$iso_code_3     = $country_info['iso_code_3'];
						$address_format = $country_info['address_format'];
					} else {
						$country        = '';
						$iso_code_2     = '';
						$iso_code_3     = '';
						$address_format = '';
					}

					$zone_info = $this->Model_Localisation_Zone->getZone($_POST['shipping_zone_id']);

					if ($zone_info) {
						$zone = $zone_info['name'];
						$code = $zone_info['code'];
					} else {
						$zone = '';
						$code = '';
					}

					$address_data = array(
						'firstname'      => $_POST['shipping_firstname'],
						'lastname'       => $_POST['shipping_lastname'],
						'company'        => $_POST['shipping_company'],
						'address_1'      => $_POST['shipping_address_1'],
						'address_2'      => $_POST['shipping_address_2'],
						'postcode'       => $_POST['shipping_postcode'],
						'city'           => $_POST['shipping_city'],
						'zone_id'        => $_POST['shipping_zone_id'],
						'zone'           => $zone,
						'zone_code'      => $code,
						'country_id'     => $_POST['shipping_country_id'],
						'country'        => $country,
						'iso_code_2'     => $iso_code_2,
						'iso_code_3'     => $iso_code_3,
						'address_format' => $address_format
					);

					$results = $this->Model_Setting_Extension->getExtensions('shipping');

					foreach ($results as $result) {
						if (option($result['code'] . '_status')) {

							$quote = $this->{'model_shipping_' . $result['code']}->getQuote($address_data);

							if ($quote) {
								$json['shipping_method'][$result['code']] = array(
									'title'      => $quote['title'],
									'quote'      => $quote['quote'],
									'sort_order' => $quote['sort_order'],
									'error'      => $quote['error']
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
						$json['error']['shipping_method'] = _l("Warning: No Shipping options are available!");
					} else {
						if (!$_POST['shipping_code']) {
							$json['error']['shipping_method'] = _l("Warning: Shipping method required!");
						} else {
							$shipping = explode('.', $_POST['shipping_code']);

							if (!isset($shipping[0]) || !isset($shipping[1]) || !isset($json['shipping_method'][$shipping[0]]['quote'][$shipping[1]])) {
								$json['error']['shipping_method'] = _l("Warning: Shipping method required!");
							} else {
								$this->session->set('shipping_method', $json['shipping_method'][$shipping[0]]['quote'][$shipping[1]]);
							}
						}
					}
				}
			}

			// Coupon
			if (!empty($_POST['coupon'])) {
				$coupon_info = $this->Model_Cart_Coupon->getCoupon($_POST['coupon']);

				if ($coupon_info) {
					$this->session->get('coupons')[$_POST['coupon']] = $coupon_info;
				} else {
					$json['error']['coupon'] = _l("Warning: Coupon is either invalid, expired or reached it's usage limit!");
				}
			}

			// Voucher
			if (!empty($_POST['voucher'])) {
				$voucher_info = $this->Model_Cart_Voucher->getVoucher($_POST['voucher']);

				if ($voucher_info) {
					$this->session->set('voucher', $_POST['voucher']);
				} else {
					$json['error']['voucher'] = _l("Warning: Gift Voucher is either invalid or the balance has been used up!");
				}
			}

			// Reward Points
			if (!empty($_POST['reward'])) {
				$points = $this->customer->getRewardPoints();

				if ($_POST['reward'] > $points) {
					$json['error']['reward'] = sprintf(_l("Warning: You don't have %s reward points!"), $_POST['reward']);
				}

				if (!isset($json['error']['reward'])) {
					$points_total = 0;

					foreach ($this->cart->getProducts() as $product) {
						if ($product['points']) {
							$points_total += $product['points'];
						}
					}

					if ($_POST['reward'] > $points_total) {
						$json['error']['reward'] = sprintf(_l("Warning: The maximum number of points that can be applied is %s!"), $points_total);
					}

					if (!isset($json['error']['reward'])) {
						$this->session->set('reward', $_POST['reward']);
					}
				}
			}

			// Totals
			$json['order_total'] = array();
			$total               = 0;
			$taxes               = $this->cart->getTaxes();

			$sort_order = array();

			$results = $this->Model_Setting_Extension->getExtensions('total');

			foreach ($results as $key => $value) {
				$sort_order[$key] = option($value['code'] . '_sort_order');
			}

			array_multisort($sort_order, SORT_ASC, $results);

			foreach ($results as $result) {
				if (option($result['code'] . '_status')) {

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
				$json['error']['payment']['country'] = _l("Please select a country!");
			}

			if ($_POST['payment_zone_id'] == '') {
				$json['error']['payment']['zone'] = _l("Please select a region / state!");
			}

			if (!isset($json['error']['payment'])) {
				$json['payment_methods'] = array();

				$country_info = $this->Model_Localisation_Country->getCountry($_POST['payment_country_id']);

				if ($country_info) {
					$country        = $country_info['name'];
					$iso_code_2     = $country_info['iso_code_2'];
					$iso_code_3     = $country_info['iso_code_3'];
					$address_format = $country_info['address_format'];
				} else {
					$country        = '';
					$iso_code_2     = '';
					$iso_code_3     = '';
					$address_format = '';
				}

				$zone_info = $this->Model_Localisation_Zone->getZone($_POST['payment_zone_id']);

				if ($zone_info) {
					$zone = $zone_info['name'];
					$code = $zone_info['code'];
				} else {
					$zone = '';
					$code = '';
				}

				$address_data = array(
					'firstname'      => $_POST['payment_firstname'],
					'lastname'       => $_POST['payment_lastname'],
					'company'        => $_POST['payment_company'],
					'address_1'      => $_POST['payment_address_1'],
					'address_2'      => $_POST['payment_address_2'],
					'postcode'       => $_POST['payment_postcode'],
					'city'           => $_POST['payment_city'],
					'zone_id'        => $_POST['payment_zone_id'],
					'zone'           => $zone,
					'zone_code'      => $code,
					'country_id'     => $_POST['payment_country_id'],
					'country'        => $country,
					'iso_code_2'     => $iso_code_2,
					'iso_code_3'     => $iso_code_3,
					'address_format' => $address_format
				);

				$json['payment_method'] = array();

				$results = $this->Model_Setting_Extension->getExtensions('payment');

				foreach ($results as $result) {
					if (option($result['code'] . '_status')) {

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
					$json['error']['payment_method'] = _l("Warning: No Payment options are available!");
				} else {
					if (!$_POST['payment_code']) {
						$json['error']['payment_method'] = _l("Warning: Payment method required!");
					} else {
						if (!isset($json['payment_method'][$_POST['payment_code']])) {
							$json['error']['payment_method'] = _l("Warning: Payment method required!");
						}
					}
				}
			}

			if (!isset($json['error'])) {
				$json['success'] = _l("Order totals has been successfully re-calculated!");
			} else {
				$json['error']['warning'] = _l("Warning: Please check the form carefully for errors!");
			}

			// Reset everything
			$this->cart->clear();
			$this->customer->logout();

			$this->session->remove('shipping_method');
			$this->session->remove('shipping_methods');
			$this->session->remove('payment_method');
			$this->session->remove('payment_methods');
			$this->session->remove('coupons');
			$this->session->remove('reward');
			$this->session->remove('voucher');
			$this->session->remove('vouchers');
		} else {
			$json['error']['warning'] = _l("You do not have permission to access this page, please refer to your system administrator.");
		}

		$this->response->setOutput(json_encode($json));
	}
}
