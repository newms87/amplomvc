<?php
class Catalog_Controller_Extension_Payment_PpStandard
{
	public function renderTemplate()
	{
		if ($this->settings['test']) {
			$data['action']   = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
			$data['business'] = $this->settings['test_email'] ? $this->settings['test_email'] : $this->settings['email'];
		} else {
			$data['action']   = 'https://www.paypal.com/cgi-bin/webscr';
			$data['business'] = $this->settings['email'];
		}

		$order = $this->order->get();

		if (!$order) {
			$this->output = _l("There was a problem processing your order. Please verify you order and try checking out again.");
			return;
		}

		$data['order_id']  = $order['order_id'];
		$data['item_name'] = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');

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

		$data['products'] = $cart_products;

		$data['discount_amount_cart'] = 0;

		$extra_total = $this->currency->format($order['total'] - $this->cart->getSubTotal(), $order['currency_code'], false, false);

		if ($extra_total > 0) {
			$data['extras'] = array(
				array(
					'name'     => _l('Shipping, Handling, Discounts & Taxes'),
					'model'    => '',
					'price'    => $extra_total,
					'quantity' => 1,
					'weight'   => 0,
				)
			);
		} else {
			$data['discount_amount_cart'] -= $extra_total;
		}

		$payment_address_info = $this->Model_Localisation_Country->getCountry($order['payment_country_id']);

		$data['currency_code'] = $order['currency_code'];
		$data['first_name']    = html_entity_decode($order['payment_firstname'], ENT_QUOTES, 'UTF-8');
		$data['last_name']     = html_entity_decode($order['payment_lastname'], ENT_QUOTES, 'UTF-8');
		$data['address1']      = html_entity_decode($order['payment_address_1'], ENT_QUOTES, 'UTF-8');
		$data['address2']      = html_entity_decode($order['payment_address_2'], ENT_QUOTES, 'UTF-8');
		$data['city']          = html_entity_decode($order['payment_city'], ENT_QUOTES, 'UTF-8');
		$data['zip']           = html_entity_decode($order['payment_postcode'], ENT_QUOTES, 'UTF-8');
		$data['country']       = $payment_address_info['iso_code_2'];
		$data['email']         = $order['email'];
		$data['invoice']       = $order['invoice_id'] . ' - ' . html_entity_decode($order['payment_firstname'], ENT_QUOTES, 'UTF-8') . ' ' . html_entity_decode($order['payment_lastname'], ENT_QUOTES, 'UTF-8');
		$data['lc']            = $this->language->info('code');
		$data['notify_url']    = $this->callbackUrl('notify');
		$data['cancel_return'] = $this->url->link('checkout/checkout');
		$data['page_style']    = $this->settings['page_style'];

		if ($this->settings['pdt_enabled']) {
			$data['return'] = $this->callbackUrl('auto_return');
		} else {
			$data['return'] = $this->url->link('checkout/success');
		}

		$server = URL_IMAGE;

		//Ajax Urls
		$data['url_check_order_status'] = $this->url->link('block/checkout/confirm/check_order_status', 'order_id=' . $order['order_id']);

		//Template Data
		$data['image_url']     = $server . $this->config->get('config_logo');
		$data['paymentaction'] = $this->settings['transaction'] ? 'sale' : 'authorization';
		$data['custom']        = $this->encryption->encrypt($order['order_id']);
		$data['testmode']      = $this->settings['test'];

		//Render
		return $this->render('payment/pp_standard', $data);
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

				if ($msg && $this->settings['debug']) {
					$this->log->write($msg);
				}

				if ($order_status_id) {
					if ($this->settings['debug']) {
						$status = $this->order->getOrderStatus($order_status_id);

						$this->log->write("PP_STANDARD :: Updating Order ( order_id: $order_id ) to order status $status[title] ( order_status_id: $order_status_id )");
					}

					$this->order->updateOrder($order_id, $order_status_id);
					return true;
				}
			} else {
				$msg = "PP_STANDARD :: Invalid Response from PayPal on callback for order ID $order_id. Request: $request";
				$this->error_log->write($msg);

				if ($this->settings['debug']) {
					$this->log->write($msg);
				}
			}
		}

		return false;
	}

	//TODO: This is not working. Need to verify PDT process?
	public function auto_return()
	{
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
			$this->message->add('warning', _l("There was an error while verifying your payment from Paypal. Please contact <a href=\"%s\">Customer Support</a> to resolve the payment.", $this->config->get('config_email')));

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


			//TODO: Move this to mail Controller
			$subject = _l("ATTENTION: There was a critical error while resolving an order payment!");
			$message = _l("There was an error while verifying the payment for %s from Paypal.", $name) .
				_l("The transaction completed, but payment status their order information could not be resolved.") .
				_l("<br />Order ID: %s<br />Paid Amount: %s<br />Customer ID: %s<br />Customer Email: %s<br />", $order_id, $amount, $customer_id, $email);

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
}
