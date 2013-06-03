<?php
class Catalog_Controller_Payment_PpPro extends Controller 
{
	protected function index()
	{
		$this->template->load('payment/pp_pro');

		$this->language->load('payment/pp_pro');
		
		$this->data['cards'] = array();

		$this->data['cards'][] = array(
			'text'  => 'Visa',
			'value' => 'VISA'
		);

		$this->data['cards'][] = array(
			'text'  => 'MasterCard',
			'value' => 'MASTERCARD'
		);

		$this->data['cards'][] = array(
			'text'  => 'Discover Card',
			'value' => 'DISCOVER'
		);
		
		$this->data['cards'][] = array(
			'text'  => 'American Express',
			'value' => 'AMEX'
		);

		$this->data['cards'][] = array(
			'text'  => 'Maestro',
			'value' => 'SWITCH'
		);
		
		$this->data['cards'][] = array(
			'text'  => 'Solo',
			'value' => 'SOLO'
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

	public function send()
	{
		if (!$this->config->get('pp_pro_transaction')) {
			$payment_type = 'Authorization';
		} else {
			$payment_type = 'Sale';
		}
		
		$order_info = $this->Model_Checkout_Order->getOrder($this->session->data['order_id']);
		
		$request  = 'METHOD=DoDirectPayment';
		$request .= '&VERSION=51.0';
		$request .= '&USER=' . urlencode($this->config->get('pp_pro_username'));
		$request .= '&PWD=' . urlencode($this->config->get('pp_pro_password'));
		$request .= '&SIGNATURE=' . urlencode($this->config->get('pp_pro_signature'));
		$request .= '&CUSTREF=' . (int)$order_info['order_id'];
		$request .= '&PAYMENTACTION=' . $payment_type;
		$request .= '&AMT=' . $this->currency->format($order_info['total'], $order_info['currency_code'], false, false);
		$request .= '&CREDITCARDTYPE=' . $_POST['cc_type'];
		$request .= '&ACCT=' . urlencode(str_replace(' ', '', $_POST['cc_number']));
		$request .= '&CARDSTART=' . urlencode($_POST['cc_start_date_month'] . $_POST['cc_start_date_year']);
		$request .= '&EXPDATE=' . urlencode($_POST['cc_expire_date_month'] . $_POST['cc_expire_date_year']);
		$request .= '&CVV2=' . urlencode($_POST['cc_cvv2']);
		
		if ($_POST['cc_type'] == 'SWITCH' || $_POST['cc_type'] == 'SOLO') {
			$request .= '&CARDISSUE=' . urlencode($_POST['cc_issue']);
		}
		
		$request .= '&FIRSTNAME=' . urlencode($order_info['payment_firstname']);
		$request .= '&LASTNAME=' . urlencode($order_info['payment_lastname']);
		$request .= '&EMAIL=' . urlencode($order_info['email']);
		$request .= '&PHONENUM=' . urlencode($order_info['telephone']);
		$request .= '&IPADDRESS=' . urlencode($_SERVER['REMOTE_ADDR']);
		$request .= '&STREET=' . urlencode($order_info['payment_address_1']);
		$request .= '&CITY=' . urlencode($order_info['payment_city']);
		$request .= '&STATE=' . urlencode(($order_info['payment_iso_code_2'] != 'US') ? $order_info['payment_zone'] : $order_info['payment_zone_code']);
		$request .= '&ZIP=' . urlencode($order_info['payment_postcode']);
		$request .= '&COUNTRYCODE=' . urlencode($order_info['payment_iso_code_2']);
		$request .= '&CURRENCYCODE=' . urlencode($order_info['currency_code']);
		
		if ($this->cart->hasShipping()) {
			$request .= '&SHIPTONAME=' . urlencode($order_info['shipping_firstname'] . ' ' . $order_info['shipping_lastname']);
			$request .= '&SHIPTOSTREET=' . urlencode($order_info['shipping_address_1']);
			$request .= '&SHIPTOCITY=' . urlencode($order_info['shipping_city']);
			$request .= '&SHIPTOSTATE=' . urlencode(($order_info['shipping_iso_code_2'] != 'US') ? $order_info['shipping_zone'] : $order_info['shipping_zone_code']);
			$request .= '&SHIPTOCOUNTRYCODE=' . urlencode($order_info['shipping_iso_code_2']);
			$request .= '&SHIPTOZIP=' . urlencode($order_info['shipping_postcode']);
		} else {
			$request .= '&SHIPTONAME=' . urlencode($order_info['payment_firstname'] . ' ' . $order_info['payment_lastname']);
			$request .= '&SHIPTOSTREET=' . urlencode($order_info['payment_address_1']);
			$request .= '&SHIPTOCITY=' . urlencode($order_info['payment_city']);
			$request .= '&SHIPTOSTATE=' . urlencode(($order_info['payment_iso_code_2'] != 'US') ? $order_info['payment_zone'] : $order_info['payment_zone_code']);
			$request .= '&SHIPTOCOUNTRYCODE=' . urlencode($order_info['payment_iso_code_2']);
			$request .= '&SHIPTOZIP=' . urlencode($order_info['payment_postcode']);
		}
		
		if (!$this->config->get('pp_pro_test')) {
			$curl = curl_init('https://api-3t.paypal.com/nvp');
		} else {
			$curl = curl_init('https://api-3t.sandbox.paypal.com/nvp');
		}
		
		curl_setopt($curl, CURLOPT_PORT, 443);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $request);

		$response = curl_exec($curl);
 		
		curl_close($curl);
 
		if (!$response) {
			$this->error_log->write('DoDirectPayment failed: ' . curl_error($curl) . '(' . curl_errno($curl) . ')');
		}
 
 		$response_data = array();
 
		parse_str($response, $response_data);

		$json = array();
		
		if (($response_data['ACK'] == 'Success') || ($response_data['ACK'] == 'SuccessWithWarning')) {
			$this->Model_Checkout_Order->confirm($this->session->data['order_id'], $this->config->get('config_order_status_id'));
			
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
			
			$this->Model_Checkout_Order->update_order($this->session->data['order_id'], $this->config->get('pp_pro_order_status_id'), $message, false);
		
			$json['success'] = $this->url->link('checkout/success');
		} else {
			$json['error'] = $response_data['L_LONGMESSAGE0'];
		}
		
		$this->response->setOutput(json_encode($json));
	}
}