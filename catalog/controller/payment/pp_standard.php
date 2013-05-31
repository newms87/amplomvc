<?php
class Catalog_Controller_Payment_PpStandard extends Controller 
{
	protected function index()
	{
		$this->language->load('payment/pp_standard');
		
		$this->data['testmode'] = $this->config->get('pp_standard_test');
		
		if (!$this->config->get('pp_standard_test')) {
			$this->data['action'] = 'https://www.paypal.com/cgi-bin/webscr';
  		} else {
			$this->data['action'] = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		}

		$order_info = $this->Model_Checkout_Order->getOrder($this->session->data['order_id']);

		if ($order_info) {
		$this->template->load('payment/pp_standard');

			$this->data['order_id'] = $this->session->data['order_id'];
			$this->data['business'] = $this->config->get('pp_standard_email');
			$this->data['item_name'] = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');
			
			$this->data['products'] = array();
			
			foreach ($this->cart->getProducts() as $product) {
				$option_data = array();
	
				foreach ($product['option'] as $option) {
					if ($option['type'] != 'file') {
						$value = $option['option_value'];
					} else {
						$filename = $this->encryption->decrypt($option['option_value']);
						
						$value = substr($filename, 0, strrpos($filename, '.'));
					}
										
					$option_data[] = array(
						'name'  => $option['name'],
						'value' => (strlen($value) > 20 ? substr($value, 0, 20) . '..' : $value)
					);
				}
				
				$this->data['products'][] = array(
					'name'	=> $product['name'],
					'model'	=> $product['model'],
					'price'	=> $this->currency->format($product['price'], $order_info['currency_code'], false, false),
					'quantity' => $product['quantity'],
					'option'	=> $option_data,
					'weight'	=> $product['weight']
				);
			}
			
			$this->data['discount_amount_cart'] = 0;
			
			$total = $this->currency->format($order_info['total'] - $this->cart->getSubTotal(), $order_info['currency_code'], false, false);

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
			
			$this->data['currency_code'] = $order_info['currency_code'];
			$this->data['first_name'] = html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8');
			$this->data['last_name'] = html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
			$this->data['address1'] = html_entity_decode($order_info['payment_address_1'], ENT_QUOTES, 'UTF-8');
			$this->data['address2'] = html_entity_decode($order_info['payment_address_2'], ENT_QUOTES, 'UTF-8');
			$this->data['city'] = html_entity_decode($order_info['payment_city'], ENT_QUOTES, 'UTF-8');
			$this->data['zip'] = html_entity_decode($order_info['payment_postcode'], ENT_QUOTES, 'UTF-8');
			$this->data['country'] = $order_info['payment_iso_code_2'];
			$this->data['email'] = $order_info['email'];
			$this->data['invoice'] = $this->session->data['order_id'] . ' - ' . html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8') . ' ' . html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
			$this->data['lc'] = $this->language->code();
			$this->data['return'] = $this->url->link('checkout/success');
			$this->data['notify_url'] = $this->url->link('payment/pp_standard/callback');
			$this->data['cancel_return'] = $this->url->link('checkout/checkout');
			$this->data['page_style'] = $this->config->get('pp_standard_page_style');
			
			$server = $this->url->is_ssl() ? HTTPS_IMAGE : HTTP_IMAGE;
			
			$this->data['image_url'] = $server . $this->config->get('config_logo');
			
			if (!$this->config->get('pp_standard_transaction')) {
				$this->data['paymentaction'] = 'authorization';
			} else {
				$this->data['paymentaction'] = 'sale';
			}
			
			$this->data['custom'] = $this->encryption->encrypt($this->session->data['order_id']);
			
			$this->render();
		}
	}
	
	public function callback()
	{
		if ($this->config->get('pp_standard_debug')) {
			$this->error_log->write('PP_STANDARD :: Callback called');
		}
		
		if (isset($_POST['custom'])) {
			$order_id = $this->encryption->decrypt($_POST['custom']);
		} else {
			$order_id = 0;
		}
		
		$order_info = $this->Model_Checkout_Order->getOrder($order_id);
		
		if ($order_info) {
			$request = 'cmd=_notify-validate';
		
			foreach ($_POST as $key => $value) {
				$request .= '&' . $key . '=' . urlencode(html_entity_decode($value, ENT_QUOTES, 'UTF-8'));
			}
			
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
				$this->error_log->write('PP_STANDARD :: IPN REQUEST: ' . $request);
				$this->error_log->write('PP_STANDARD :: IPN RESPONSE: ' . $response);
			}
						
			if ((strcmp($response, 'VERIFIED') == 0 || strcmp($response, 'UNVERIFIED') == 0) && isset($_POST['payment_status'])) {
				$order_status_id = $this->config->get('config_order_status_id');
				
				switch($_POST['payment_status']) {
					case 'Canceled_Reversal':
						$order_status_id = $this->config->get('pp_standard_canceled_reversal_status_id');
						break;
					case 'Completed':
						if ((float)$_POST['mc_gross'] == $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false)) {
							$order_status_id = $this->config->get('pp_standard_completed_status_id');
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
				}
				
				if (!$order_info['order_status_id']) {
					$this->Model_Checkout_Order->confirm($order_id, $order_status_id);
				} else {
					$this->Model_Checkout_Order->update_order($order_id, $order_status_id);
				}
			} else {
				$this->Model_Checkout_Order->confirm($order_id, $this->config->get('config_order_status_id'));
			}
			
			curl_close($curl);
		}
	}
}
