<?php
class System_Extension_Payment_PpStandard extends PaymentExtension
{
	public function renderTemplate()
	{
		$this->language->system('extension/payment/pp_standard');

		if ($this->settings['test']) {
			$this->data['action']   = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
			$this->data['business'] = $this->settings['test_email'] ? $this->settings['test_email'] : $this->settings['email'];
		} else {
			$this->data['action']   = 'https://www.paypal.com/cgi-bin/webscr';
			$this->data['business'] = $this->settings['email'];
		}

		$order = $this->order->get();

		if (!$order) {
			$this->output = $this->_('error_payment_order');
			return;
		}

		$this->data['order_id']  = $order['order_id'];
		$this->data['item_name'] = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');

		$cart_products = $this->cart->getProducts();

		foreach ($cart_products as &$cart_product) {
			foreach ($cart_product['options'] as $product_option_id => &$product_option_values) {
				foreach ($product_option_values as &$product_option_value) {
					$product_option_value['display_value'] = $this->tool->limit_characters($product_option_value['display_value'], 20);
				}
				unset($product_option_value);
			}
			unset($product_option_values);

			$cart_product['price'] = $this->currency->format($cart_product['price'], $order['currency_code'], false, false);
		}
		unset($cart_product);

		$this->data['products'] = $cart_products;

		$this->data['discount_amount_cart'] = 0;

		$extra_total = $this->currency->format($order['total'] - $this->cart->getSubTotal(), $order['currency_code'], false, false);

		if ($extra_total > 0) {
			$this->data['extras'] = array(
				array(
					'name'     => _l('Shipping, Handling, Discounts & Taxes'),
					'model'    => '',
					'price'    => $extra_total,
					'quantity' => 1,
					'weight'   => 0,
				)
			);
		} else {
			$this->data['discount_amount_cart'] -= $extra_total;
		}

		$payment_address_info = $this->Model_Localisation_Country->getCountry($order['payment_country_id']);

		$this->data['currency_code'] = $order['currency_code'];
		$this->data['first_name']    = html_entity_decode($order['payment_firstname'], ENT_QUOTES, 'UTF-8');
		$this->data['last_name']     = html_entity_decode($order['payment_lastname'], ENT_QUOTES, 'UTF-8');
		$this->data['address1']      = html_entity_decode($order['payment_address_1'], ENT_QUOTES, 'UTF-8');
		$this->data['address2']      = html_entity_decode($order['payment_address_2'], ENT_QUOTES, 'UTF-8');
		$this->data['city']          = html_entity_decode($order['payment_city'], ENT_QUOTES, 'UTF-8');
		$this->data['zip']           = html_entity_decode($order['payment_postcode'], ENT_QUOTES, 'UTF-8');
		$this->data['country']       = $payment_address_info['iso_code_2'];
		$this->data['email']         = $order['email'];
		$this->data['invoice']       = $order['invoice_id'] . ' - ' . html_entity_decode($order['payment_firstname'], ENT_QUOTES, 'UTF-8') . ' ' . html_entity_decode($order['payment_lastname'], ENT_QUOTES, 'UTF-8');
		$this->data['lc']            = $this->language->code();
		$this->data['notify_url']    = $this->callbackUrl('notify');
		$this->data['cancel_return'] = $this->url->link('checkout/checkout');
		$this->data['page_style']    = $this->settings['page_style'];

		if ($this->settings['pdt_enabled']) {
			$this->data['return'] = $this->callbackUrl('auto_return');
		} else {
			$this->data['return'] = $this->url->link('checkout/success');
		}

		$server = $this->url->is_ssl() ? HTTPS_IMAGE : HTTP_IMAGE;

		//Ajax Urls
		$this->data['url_check_order_status'] = $this->url->ajax('block/checkout/confirm/check_order_status', 'order_id=' . $order['order_id']);

		//Additional Data
		$this->data['image_url']     = $server . $this->config->get('config_logo');
		$this->data['paymentaction'] = $this->settings['transaction'] ? 'sale' : 'authorization';
		$this->data['custom']        = $this->encryption->encrypt($order['order_id']);
		$this->data['testmode']      = $this->settings['test'];

		//The Template
		$this->template->load('payment/pp_standard');

		//Render
		return $this->render();
	}

	public function info()
	{
		if (!empty($this->settings['button_graphic'])) {
			$this->info['title'] = "<img src=\"{$this->settings['button_graphic']}\" border=\"0\" alt=\"Paypal\" />";
		}

		return $this->info;
	}

	public function subscribe()
	{
		$this->language->load('payment/pp_standard');

		$this->config->loadGroup('pp_standard');

		$testmode = $this->settings['test'];

		if ($testmode) {
			$this->data['action'] = 'https://www.sandbox.paypal.com/cgi-bin/webscr';

			if ($this->settings['test_email']) {
				$this->data['business'] = $this->settings['test_email'];
			} else {
				$this->data['business'] = $this->settings['email'];
			}
		} else {
			$this->data['action']   = 'https://www.paypal.com/cgi-bin/webscr';
			$this->data['business'] = $this->settings['email'];
		}

		$subscription = $this->subscription->get();

		if ($subscription) {
			$this->template->load('payment/pp_standard_subscribe');

			$this->data['order_id']  = $subscription['order_id'];
			$this->data['item_name'] = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');

			$products = $this->cart->getProducts();

			foreach ($products as &$product) {
				foreach ($product['selected_options'] as &$selected_option) {
					$selected_option['product_option'] = $this->Model_Catalog_Product->getProductOption($product['product_id'], $selected_option['product_option_id']);
					$selected_option['value']          = $this->tool->limit_characters($selected_option['value'], 20);
				}
				unset($product_option);

				$product['price'] = $this->currency->format($product['price'], $subscription['currency_code'], false, false);
			}
			unset($product);

			$this->data['subscriptions'] = $products;

			$this->data['discount_amount_cart'] = 0;

			$total = $this->currency->format($subscription['total'] - $this->cart->getSubTotal(), $subscription['currency_code'], false, false);

			if ($total > 0) {
				$this->data['products'][] = array(
					'name'     => $this->_('text_total'),
					'model'    => '',
					'price'    => $total,
					'quantity' => 1,
					'weight'   => 0
				);
			} else {
				$this->data['discount_amount_cart'] -= $total;
			}

			$payment_address_info = $this->Model_Localisation_Country->getCountry($subscription['payment_country_id']);

			$this->data['currency_code'] = $subscription['currency_code'];
			$this->data['first_name']    = html_entity_decode($subscription['payment_firstname'], ENT_QUOTES, 'UTF-8');
			$this->data['last_name']     = html_entity_decode($subscription['payment_lastname'], ENT_QUOTES, 'UTF-8');
			$this->data['address1']      = html_entity_decode($subscription['payment_address_1'], ENT_QUOTES, 'UTF-8');
			$this->data['address2']      = html_entity_decode($subscription['payment_address_2'], ENT_QUOTES, 'UTF-8');
			$this->data['city']          = html_entity_decode($subscription['payment_city'], ENT_QUOTES, 'UTF-8');
			$this->data['zip']           = html_entity_decode($subscription['payment_postcode'], ENT_QUOTES, 'UTF-8');
			$this->data['country']       = $payment_address_info['iso_code_2'];
			$this->data['email']         = $subscription['email'];
			$this->data['invoice']       = $subscription['invoice_id'] . ' - ' . html_entity_decode($subscription['payment_firstname'], ENT_QUOTES, 'UTF-8') . ' ' . html_entity_decode($subscription['payment_lastname'], ENT_QUOTES, 'UTF-8');
			$this->data['lc']            = $this->language->code();
			$this->data['notify_url']    = $this->url->link('payment/pp_standard/callback');
			$this->data['cancel_return'] = $this->url->link('checkout/checkout');
			$this->data['page_style']    = $this->settings['page_style'];

			if ($this->settings['pdt_enabled']) {
				$this->data['return'] = $this->url->link('payment/pp_standard/auto_return');
			} else {
				$this->data['return'] = $this->url->link('checkout/success');
			}

			$server = $this->url->is_ssl() ? HTTPS_IMAGE : HTTP_IMAGE;

			//Ajax Urls
			$this->data['url_check_order_status'] = $this->url->ajax('block/checkout/confirm/check_order_status', 'order_id=' . $subscription['order_id']);

			//Additional Data
			$this->data['image_url']     = $server . $this->config->get('config_logo');
			$this->data['paymentaction'] = $this->settings['transaction'] ? 'sale' : 'authorization';
			$this->data['custom']        = $this->encryption->encrypt($subscription['order_id']);

			$this->data['testmode'] = $testmode;

			$this->render();
		}
	}

	public function notify()
	{
		if ($this->settings['debug']) {
			$this->log->write('PP_STANDARD :: Callback called');
		}

		if (empty($_POST['custom'])) {
			return false;
		}

		$order_id = $this->encryption->decrypt($_POST['custom']);

		$order = $this->order->get($order_id);

		if ($order) {
			if (!$this->settings['test']) {
				$curl = curl_init('https://www.paypal.com/cgi-bin/webscr');
			} else {
				$curl = curl_init('https://www.sandbox.paypal.com/cgi-bin/webscr');
			}

			//IPN Response must be an exactly the same as the response received with cmd=_notify-validate prepended (same charset as well!)
			$request = 'cmd=_notify-validate';

			foreach ($_POST as $key => $value) {
				$request .= '&' . $key . '=' . urlencode(html_entity_decode($value, ENT_QUOTES, 'UTF-8'));
			}

			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_TIMEOUT, 30);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

			$response = curl_exec($curl);

			if (!$response) {
				$this->error_log->write('PP_STANDARD :: CURL failed ' . curl_error($curl) . '(' . curl_errno($curl) . ')');
			}

			if ($this->settings['debug']) {
				$this->log->write('PP_STANDARD :: IPN REQUEST: ' . $request);
				$this->log->write('PP_STANDARD :: IPN RESPONSE: ' . $response);
			}

			curl_close($curl);

			if ((strcmp($response, 'VERIFIED') === 0 || strcmp($response, 'UNVERIFIED') === 0) && isset($_POST['payment_status'])) {
				switch ($_POST['payment_status']) {
					case 'Canceled_Reversal':
						$order_status_id = $this->settings['canceled_reversal_status_id'];
						break;
					case 'Completed':
						if ((float)$_POST['mc_gross'] == (float)$this->currency->format($order['total'], $order['currency_code'], $order['currency_value'], false)) {
							$order_status_id = $this->settings['completed_status_id'];
						} else {
							$this->log->write("PP_STANDARD :: Payment Status Complete Received, but order total did not match payment received: " . $_POST['mc_gross'] . ' !== ' . $order['total']);
							$order_status_id = false;
						}
						break;
					case 'Denied':
						$order_status_id = $this->settings['denied_status_id'];
						break;
					case 'Expired':
						$order_status_id = $this->settings['expired_status_id'];
						break;
					case 'Failed':
						$order_status_id = $this->settings['failed_status_id'];
						break;
					case 'Pending':
						$order_status_id = $this->settings['pending_status_id'];
						break;
					case 'Processed':
						$order_status_id = $this->settings['processed_status_id'];
						break;
					case 'Refunded':
						$order_status_id = $this->settings['refunded_status_id'];
						break;
					case 'Reversed':
						$order_status_id = $this->settings['reversed_status_id'];
						break;
					case 'Voided':
						$order_status_id = $this->settings['voided_status_id'];
						break;
					default:
						$order_status_id = false;
						$msg             = "PP_STANDARD :: Unknown Order Payment Status Response: " . $_POST['payment_status'];
						$this->error_log->write($msg);
						break;
				}

				if ($msg && $debug) {
					$this->log->write($msg);
				}

				if ($order_status_id) {
					if ($debug) {
						$status = $this->order->getOrderStatus($order_status_id);

						$this->log->write("PP_STANDARD :: Updating Order ( order_id: $order_id ) to order status $status[title] ( order_status_id: $order_status_id )");
					}

					$this->order->update($order_id, $order_status_id);
					return true;
				}
			} else {
				$msg = "PP_STANDARD :: Invalid Response from PayPal on callback for order ID $order_id. Request: $request";
				$this->error_log->write($msg);

				if ($debug) {
					$this->log->write($msg);
				}
			}
		}

		return false;
	}

	//TODO: This is not working. Need to verify PDT process?
	public function auto_return()
	{
		$this->language->load('payment/pp_standard');

		if ($this->settings['debug']) {
			$this->log->write('PP_STANDARD :: Auto Return called');
		}

		if (empty($_POST['custom'])) {
			return false;
		}

		$order_id = $this->encryption->decrypt($_POST['custom']);

		$order = $this->order->get($order_id);

		$pdt_token = $this->config->load('pp_standard', 'pp_standard_pdt_token');
		$tx        = isset($_GET['tx']) ? $_GET['tx'] : $_POST['txn_id'];

		$response = '';

		if ($order) {
			$request = array(
				'cmd'    => '_notify-synch',
				'tx'     => $tx,
				'at'     => $pdt_token,
				'submit' => 'PDT',
			);

			$request = http_build_query($request);

			if (!$this->settings['test']) {
				$curl = curl_init('https://www.paypal.com/cgi-bin/webscr');
			} else {
				$curl = curl_init('https://www.sandbox.paypal.com/cgi-bin/webscr');
			}

			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_TIMEOUT, 30);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

			$response = curl_exec($curl);

			if (!$response) {
				$this->error_log->write('PP_STANDARD :: CURL failed ' . curl_error($curl) . '(' . curl_errno($curl) . ')');
			}

			if ($this->settings['debug']) {
				$this->log->write('PP_STANDARD :: IPN REQUEST: ' . $request);
				$this->log->write('PP_STANDARD :: IPN RESPONSE: ' . $response);
			}

			curl_close($curl);

			echo 'repsonse: ' . $response . '<br>';
		}

		if (empty($response) || strpos($response, "FAIL") === 0) {
			$this->message->add('warning', $this->_('error_checkout_callback', $this->config->get('config_email')));

			if (!$order) {
				if (!empty($_POST['first_name'])) {
					$name = $_POST['first_name'] . ' ' . $_POST['last_name'];
				} else {
					$name = $this->customer->info('firstname') . ' ' . $this->customer->info('lastname');
				}

				$email = !empty($_POST['payer_email']) ? $_POST['payer_email'] : $this->customer->info('email');

				$amount   = 'Unknown Order';
				$order_id = 'Unknown Order';
			} else {
				$name   = $order['payment_firstname'] . ' ' . $order['payment_lastname'];
				$amount = $order['total'];
				$email  = $order['email'];
			}

			$customer_id = !empty($order['customer_id']) ? $order['customer_id'] : $this->customer->getId();

			$subject = $this->_('error_checkout_callback_email_subject');
			$message = $this->_('error_checkout_callback_email', $name, $order_id, $amount, $customer_id, $email);

			$this->mail->init();

			$this->mail->setTo($this->config->get('config_email'));
			$this->mail->setFrom($this->config->get('config_email'));
			$this->mail->setSender($this->config->get('config_name'));
			$this->mail->setSubject($subject);
			$this->mail->setText($message);

			$this->mail->send();
		}

		//$this->url->redirect('checkout/success');
	}

	public function validate($address, $total)
	{
		$this->language->system('extension/payment/pp_standard');

		if (!parent::validate($address, $total)) {
			return false;
		}

		$currencies = array(
			'AUD',
			'CAD',
			'EUR',
			'GBP',
			'JPY',
			'USD',
			'NZD',
			'CHF',
			'HKD',
			'SGD',
			'SEK',
			'DKK',
			'PLN',
			'NOK',
			'HUF',
			'CZK',
			'ILS',
			'MXN',
			'MYR',
			'BRL',
			'PHP',
			'TWD',
			'THB',
			'TRY',
		);

		if (!in_array(strtoupper($this->currency->getCode()), $currencies)) {
			return false;
		}

		return true;
	}
}
