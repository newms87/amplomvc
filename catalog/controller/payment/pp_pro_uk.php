<?php
class ControllerPaymentPPProUK extends Controller {
	protected function index() {
		$this->template->load('payment/pp_pro_uk');

		$this->language->load('payment/pp_pro_uk');
		
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		
		$this->data['owner'] = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];
		
		$this->data['cards'] = array();

		$this->data['cards'][] = array(
			'text'  => 'Visa',
			'value' => '0'
		);

		$this->data['cards'][] = array(
			'text'  => 'MasterCard',
			'value' => '1'
		);

		$this->data['cards'][] = array(
			'text'  => 'Maestro',
			'value' => '9'
		);
		
		$this->data['cards'][] = array(
			'text'  => 'Solo',
			'value' => 'S'
		);
	
		$this->data['months'] = array();
		
		for ($i = 1; $i <= 12; $i++) {
			$this->data['months'][] = array(
				'text'  => strftime('%B', mktime(0, 0, 0, $i, 1, 2000)),
				'value' => sprintf('%02d', $i)
			);
		}
		
		$today = getdate();
		
		$this->data['year_valid'] = array();
		
		for ($i = $today['year'] - 10; $i < $today['year'] + 1; $i++) {
			$this->data['year_valid'][] = array(
				'text'  => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
				'value' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i))
			);
		}

		$this->data['year_expire'] = array();

		for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
			$this->data['year_expire'][] = array(
				'text'  => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
				'value' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i))
			);
		}

		$this->render();
	}

	public function send() {
		$this->language->load('payment/pp_pro_uk');
		
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
				
		if (!$this->config->get('pp_pro_uk_transaction')) {
			$payment_type = 'A';
		} else {
			$payment_type = 'S';
		}
		
		$request  = 'USER=' . urlencode($this->config->get('pp_pro_uk_user'));
		$request .= '&VENDOR=' . urlencode($this->config->get('pp_pro_uk_vendor'));
		$request .= '&PARTNER=' . urlencode($this->config->get('pp_pro_uk_partner'));
		$request .= '&PWD=' . urlencode($this->config->get('pp_pro_uk_password'));
		$request .= '&TENDER=C';
		$request .= '&TRXTYPE=' . $payment_type;
		$request .= '&AMT=' . $this->currency->format($order_info['total'], $order_info['currency_code'], false, false);
		$request .= '&CURRENCY=' . urlencode($order_info['currency_code']);
		$request .= '&NAME=' . urlencode($_POST['cc_owner']);
		$request .= '&STREET=' . urlencode($order_info['payment_address_1']);
		$request .= '&CITY=' . urlencode($order_info['payment_city']);
		$request .= '&STATE=' . urlencode(($order_info['payment_iso_code_2'] != 'US') ? $order_info['payment_zone'] : $order_info['payment_zone_code']);
		$request .= '&COUNTRY=' . urlencode($order_info['payment_iso_code_2']);
		$request .= '&ZIP=' . urlencode(str_replace(' ', '', $order_info['payment_postcode']));
		$request .= '&CLIENTIP=' . urlencode($_SERVER['REMOTE_ADDR']);
		$request .= '&EMAIL=' . urlencode($order_info['email']);
		$request .= '&ACCT=' . urlencode(str_replace(' ', '', $_POST['cc_number']));
		$request .= '&ACCTTYPE=' . urlencode($_POST['cc_type']);
		$request .= '&CARDSTART=' . urlencode($_POST['cc_start_date_month'] . substr($_POST['cc_start_date_year'], - 2, 2));
		$request .= '&EXPDATE=' . urlencode($_POST['cc_expire_date_month'] . substr($_POST['cc_expire_date_year'], - 2, 2));
		$request .= '&CVV2=' . urlencode($_POST['cc_cvv2']);
		$request .= '&CARDISSUE=' . urlencode($_POST['cc_issue']);
		
		if (!$this->config->get('pp_pro_uk_test')) {
			$curl = curl_init('https://payflowpro.verisign.com/transaction');
		} else {
			$curl = curl_init('https://pilot-payflowpro.verisign.com/transaction');
		}
		
		curl_setopt($curl, CURLOPT_PORT, 443);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('X-VPS-REQUEST-ID: ' . md5($this->session->data['order_id'] . rand())));

		$response = curl_exec($curl);
  		
		curl_close($curl);
		
		if (!$response) {
			$this->error_log->write('DoDirectPayment failed: ' . curl_error($curl) . '(' . curl_errno($curl) . ')');
		}
		
 		$response_data = array();
 
		parse_str($response, $response_data);

		$json = array();

		if ($response_data['RESULT'] == '0') {
			$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('config_order_status_id'));
			
			$message = '';
			
			if (isset($response_data['AVSCODE'])) {
				$message .= 'AVSCODE: ' . $response_data['AVSCODE'] . "\n";
			}

			if (isset($response_data['CVV2MATCH'])) {
				$message .= 'CVV2MATCH: ' . $response_data['CVV2MATCH'] . "\n";
			}

			if (isset($response_data['TRANSACTIONID'])) {
				$message .= 'TRANSACTIONID: ' . $response_data['TRANSACTIONID'] . "\n";
			}
			
			$this->model_checkout_order->update_order($this->session->data['order_id'], $this->config->get('pp_pro_uk_order_status_id'), $message, false);
		
			$json['success'] = $this->url->link('checkout/success');
		} else {
			switch ($response_data['RESULT']) {
				case '1':
				case '26':
					$json['error'] = $this->_('error_config');
					break;
				case '7':
					$json['error'] = $this->_('error_address');
					break;
				case '12':
					$json['error'] = $this->_('error_declined');
					break;
				case '23':
				case '24':
					$json['error'] = $this->_('error_invalid');
					break;
				default:
					$json['error'] = $this->_('error_general');
					break;
			}
		}
		
		$this->response->setOutput(json_encode($json));
	}
}