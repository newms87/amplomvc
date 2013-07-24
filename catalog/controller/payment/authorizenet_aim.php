<?php
class Catalog_Controller_Payment_AuthorizenetAim extends Controller
{
	protected function index()
	{
		$this->template->load('payment/authorizenet_aim');

		$this->language->load('payment/authorizenet_aim');
		
		$this->data['months'] = array();
		
		for ($i = 1; $i <= 12; $i++) {
			$this->data['months'][] = array(
				'text'  => strftime('%B', mktime(0, 0, 0, $i, 1, 2000)),
				'value' => sprintf('%02d', $i)
			);
		}
		
		$today = getdate();

		$this->data['year_expire'] = array();

		for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
			$this->data['year_expire'][] = array(
				'text'  => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
				'value' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i))
			);
		}

		$this->render();
	}
	
	public function send()
	{
		if ($this->config->get('authorizenet_aim_server') == 'live') {
			$url = 'https://secure.authorize.net/gateway/transact.dll';
		} elseif ($this->config->get('authorizenet_aim_server') == 'test') {
			$url = 'https://test.authorize.net/gateway/transact.dll';
		}
		
		//$url = 'https://secure.networkmerchants.com/gateway/transact.dll';
		
		$order_info = $this->Model_Checkout_Order->getOrder($this->session->data['order_id']);
		
		$data = array();

		$data['x_login'] = $this->config->get('authorizenet_aim_login');
		$data['x_tran_key'] = $this->config->get('authorizenet_aim_key');
		$data['x_version'] = '3.1';
		$data['x_delim_data'] = 'true';
		$data['x_delim_char'] = ',';
		$data['x_encap_char'] = '"';
		$data['x_relay_response'] = 'false';
		$data['x_first_name'] = html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8');
		$data['x_last_name'] = html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
		$data['x_company'] = html_entity_decode($order_info['payment_company'], ENT_QUOTES, 'UTF-8');
		$data['x_address'] = html_entity_decode($order_info['payment_address_1'], ENT_QUOTES, 'UTF-8');
		$data['x_city'] = html_entity_decode($order_info['payment_city'], ENT_QUOTES, 'UTF-8');
		$data['x_state'] = html_entity_decode($order_info['payment_zone'], ENT_QUOTES, 'UTF-8');
		$data['x_zip'] = html_entity_decode($order_info['payment_postcode'], ENT_QUOTES, 'UTF-8');
		$data['x_country'] = html_entity_decode($order_info['payment_country'], ENT_QUOTES, 'UTF-8');
		$data['x_phone'] = $order_info['telephone'];
		$data['x_customer_ip'] = $_SERVER['REMOTE_ADDR'];
		$data['x_email'] = $order_info['email'];
		$data['x_description'] = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');
		$data['x_amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], 1.00000, false);
		$data['x_currency_code'] = $this->currency->getCode();
		$data['x_method'] = 'CC';
		$data['x_type'] = ($this->config->get('authorizenet_aim_method') == 'capture') ? 'AUTH_CAPTURE' : 'AUTH_ONLY';
		$data['x_card_num'] = str_replace(' ', '', $_POST['cc_number']);
		$data['x_exp_date'] = $_POST['cc_expire_date_month'] . $_POST['cc_expire_date_year'];
		$data['x_card_code'] = $_POST['cc_cvv2'];
		$data['x_invoice_num'] = $this->session->data['order_id'];
	
		if ($this->config->get('authorizenet_aim_mode') == 'test') {
			$data['x_test_request'] = 'true';
		}
				
		$curl = curl_init($url);

		curl_setopt($curl, CURLOPT_PORT, 443);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));

		$response = curl_exec($curl);
		
		$json = array();
		
		if (curl_error($curl)) {
			$json['error'] = 'CURL ERROR: ' . curl_errno($curl) . '::' . curl_error($curl);
			
			$this->error_log->write('AUTHNET AIM CURL ERROR: ' . curl_errno($curl) . '::' . curl_error($curl));
		} elseif ($response) {
			$i = 1;
			
			$response_data = array();
			
			$results = explode(',', $response);
			
			foreach ($results as $result) {
				$response_data[$i] = trim($result, '"');
				
				$i++;
			}
		
			if ($response_data[1] == '1') {
				if (strtoupper($response_data[38]) != strtoupper(md5($this->config->get('authorizenet_aim_hash') . $this->config->get('authorizenet_aim_login') . $response_data[6] . $this->currency->format($order_info['total'], $order_info['currency_code'], 1.00000, false)))) {
					$this->Model_Checkout_Order->confirm($this->session->data['order_id'], $this->config->get('config_order_status_id'));
					
					$message = '';
					
					if (isset($response_data['5'])) {
						$message .= 'Authorization Code: ' . $response_data['5'] . "\n";
					}
					
					if (isset($response_data['6'])) {
						$message .= 'AVS Response: ' . $response_data['6'] . "\n";
					}
			
					if (isset($response_data['7'])) {
						$message .= 'Transaction ID: ' . $response_data['7'] . "\n";
					}
	
					if (isset($response_data['39'])) {
						$message .= 'Card Code Response: ' . $response_data['39'] . "\n";
					}
					
					if (isset($response_data['40'])) {
						$message .= 'Cardholder Authentication Verification Response: ' . $response_data['40'] . "\n";
					}
	
					$this->Model_Checkout_Order->update_order($this->session->data['order_id'], $this->config->get('authorizenet_aim_order_status_id'), $message, false);
				}
				
				$json['success'] = $this->url->link('checkout/success');
			} else {
				$json['error'] = $response_data[4];
			}
		} else {
			$json['error'] = 'Empty Gateway Response';
			
			$this->error_log->write('AUTHNET AIM CURL ERROR: Empty Gateway Response');
		}
		
		curl_close($curl);
		
		$this->response->setOutput(json_encode($json));
	}
}