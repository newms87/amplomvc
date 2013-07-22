<?php
class Catalog_Controller_Payment_PpStandard extends Controller
{
	public function index()
	{
		$this->language->load('payment/pp_standard');
		
		$this->config->loadGroup('pp_standard');
		
		$testmode = $this->config->get('pp_standard_test');
		
		if ($testmode) {
			$this->data['action'] = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
  		} else {
			$this->data['action'] = 'https://www.paypal.com/cgi-bin/webscr';
		}

		$order = $this->order->get();

		if ($order) {
			$this->template->load('payment/pp_standard');

			$this->data['order_id'] = $order['order_id'];
			$this->data['business'] = $this->config->get('pp_standard_email');
			$this->data['item_name'] = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');
			
			$products = $this->cart->getProducts();
			
			foreach ($products as &$product) {
				foreach ($product['option'] as &$option) {
					$option['value'] = $this->tool->limit_characters($option['option_value'], 20);
				} unset($option);
				
				$product['price'] = $this->currency->format($product['price'], $order['currency_code'], false, false);
			} unset($product);
			
			$this->data['products'] = $products;
			
			$this->data['discount_amount_cart'] = 0;
			
			$total = $this->currency->format($order['total'] - $this->cart->getSubTotal(), $order['currency_code'], false, false);

			if ($total > 0) {
				$this->data['products'][] = array(
					'name'	=> $this->_('text_total'),
					'model'	=> '',
					'price'	=> $total,
					'quantity' => 1,
					'option'	=> array(),
					'weight'	=> 0
				);
			} else {
				$this->data['discount_amount_cart'] -= $total;
			}
			
			$payment_address_info = $this->Model_Localisation_Country->getCountry($order['payment_country_id']);
			
			$this->data['currency_code'] = $order['currency_code'];
			$this->data['first_name'] = html_entity_decode($order['payment_firstname'], ENT_QUOTES, 'UTF-8');
			$this->data['last_name'] = html_entity_decode($order['payment_lastname'], ENT_QUOTES, 'UTF-8');
			$this->data['address1'] = html_entity_decode($order['payment_address_1'], ENT_QUOTES, 'UTF-8');
			$this->data['address2'] = html_entity_decode($order['payment_address_2'], ENT_QUOTES, 'UTF-8');
			$this->data['city'] = html_entity_decode($order['payment_city'], ENT_QUOTES, 'UTF-8');
			$this->data['zip'] = html_entity_decode($order['payment_postcode'], ENT_QUOTES, 'UTF-8');
			$this->data['country'] = $payment_address_info['iso_code_2'];
			$this->data['email'] = $order['email'];
			$this->data['invoice'] = $order['invoice_id'] . ' - ' . html_entity_decode($order['payment_firstname'], ENT_QUOTES, 'UTF-8') . ' ' . html_entity_decode($order['payment_lastname'], ENT_QUOTES, 'UTF-8');
			$this->data['lc'] = $this->language->code();
			$this->data['notify_url'] = $this->url->link('payment/pp_standard/callback');
			$this->data['cancel_return'] = $this->url->link('checkout/checkout');
			$this->data['page_style'] = $this->config->get('pp_standard_page_style');
			
			if ($this->config->get('pp_standard_pdt_enabled')) {
				$this->data['return'] = $this->url->link('payment/pp_standard/auto_return');
			} else {
				$this->data['return'] = $this->url->link('checkout/success');
			}
			
			$server = $this->url->is_ssl() ? HTTPS_IMAGE : HTTP_IMAGE;
			
			//Ajax Urls
			$this->data['url_check_order_status'] = $this->url->ajax('block/checkout/confirm/check_order_status', 'order_id=' . $order['order_id']);
			
			//Additional Data
			$this->data['image_url'] = $server . $this->config->get('config_logo');
			$this->data['paymentaction'] = $this->config->get('pp_standard_transaction') ? 'sale' : 'authorization';
			$this->data['custom'] = $this->encryption->encrypt($order['order_id']);
			
			$this->data['testmode'] = $testmode;
		
			$this->render();
		}
	}
	
	public function callback()
	{
		if ($this->config->get('pp_standard_debug')) {
			$this->log->write('PP_STANDARD :: Callback called');
		}
		
		if (empty($_POST['custom'])) {
			return false;
		}
		
		$order_id = $this->encryption->decrypt($_POST['custom']);
		
		$order = $this->order->get($order_id);
		
		if ($order) {
			if (!$this->config->get('pp_standard_test')) {
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
					
			if ($this->config->get('pp_standard_debug')) {
				$this->log->write('PP_STANDARD :: IPN REQUEST: ' . $request);
				$this->log->write('PP_STANDARD :: IPN RESPONSE: ' . $response);
			}
			
			curl_close($curl);
			
			if ((strcmp($response, 'VERIFIED') === 0 || strcmp($response, 'UNVERIFIED') === 0) && isset($_POST['payment_status'])) {
				switch($_POST['payment_status']) {
					case 'Canceled_Reversal':
						$order_status_id = $this->config->get('pp_standard_canceled_reversal_status_id');
						break;
					case 'Completed':
						if ((float)$_POST['mc_gross'] == (float)$this->currency->format($order['total'], $order['currency_code'], $order['currency_value'], false)) {
							$order_status_id = $this->config->get('pp_standard_completed_status_id');
						}
						else {
							$this->log->write("PP_STANDARD :: Payment Status Complete Received, but order total did not match payment received: " . $_POST['mc_gross'] . ' !== ' . $order['total']);
							$order_status_id = false;
						}
						break;
					case 'Denied':
						$order_status_id = $this->config->get('pp_standard_denied_status_id');
						break;
					case 'Expired':
						$order_status_id = $this->config->get('pp_standard_expired_status_id');
						break;
					case 'Failed':
						$order_status_id = $this->config->get('pp_standard_failed_status_id');
						break;
					case 'Pending':
						$order_status_id = $this->config->get('pp_standard_pending_status_id');
						break;
					case 'Processed':
						$order_status_id = $this->config->get('pp_standard_processed_status_id');
						break;
					case 'Refunded':
						$order_status_id = $this->config->get('pp_standard_refunded_status_id');
						break;
					case 'Reversed':
						$order_status_id = $this->config->get('pp_standard_reversed_status_id');
						break;
					case 'Voided':
						$order_status_id = $this->config->get('pp_standard_voided_status_id');
						break;
					default:
						$order_status_id = false;
						$msg = "PP_STANDARD :: Unknown Order Payment Status Response: " . $_POST['payment_status'];
						$this->error_log->write($msg);
						
						if ($debug) {
							$this->log->write($msg);
						}
						break;
				}
				
				if ($order_status_id) {
					if ($debug) {
						$status = $this->order->getOrderStatus($order_status_id);
						
						$this->log->write("PP_STANDARD :: Updating Order ( order_id: $order_id ) to order status $status[title] ( order_status_id: $order_status_id )");
					}
					
					$this->order->update($order_id, $order_status_id);
					return true;
				}
			}
			else {
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
		
		if ($this->config->get('pp_standard_debug')) {
			$this->log->write('PP_STANDARD :: Auto Return called');
		}
		
		if (empty($_POST['custom'])) {
			return false;
		}
		
		$order_id = $this->encryption->decrypt($_POST['custom']);
		
		$order = $this->order->get($order_id);
		
		$pdt_token = $this->config->load('pp_standard', 'pp_standard_pdt_token');
		$tx = isset($_GET['tx']) ? $_GET['tx'] : $_POST['txn_id'];
		
		$response = '';
		
		if ($order) {
			$request = array(
				'cmd' => '_notify-synch',
				'tx' => $tx,
				'at' => $pdt_token,
				'submit' => 'PDT',
			);
			
			$request = http_build_query($request);
			
			if (!$this->config->get('pp_standard_test')) {
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
					
			if ($this->config->get('pp_standard_debug')) {
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
				
				$amount = 'Unknown Order';
				$order_id = 'Unknown Order';
			}
			else {
				$name = $order['payment_firstname'] . ' ' . $order['payment_lastname'];
				$amount = $order['total'];
				$email = $order['email'];
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
		
		//$this->url->redirect($this->url->link('checkout/success'));
	}
}
