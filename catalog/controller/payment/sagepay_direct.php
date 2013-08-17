<?php
class Catalog_Controller_Payment_SagepayDirect extends Controller
{
	protected function index()
	{
		$this->template->load('payment/sagepay_direct');

		$this->language->load('payment/sagepay_direct');

		$this->data['cards'] = array();

		$this->data['cards'][] = array(
			'text'  => 'Visa',
			'value' => 'VISA'
		);

		$this->data['cards'][] = array(
			'text'  => 'MasterCard',
			'value' => 'MC'
		);

		$this->data['cards'][] = array(
			'text'  => 'Visa Delta/Debit',
			'value' => 'DELTA'
		);

		$this->data['cards'][] = array(
			'text'  => 'Solo',
			'value' => 'SOLO'
		);

		$this->data['cards'][] = array(
			'text'  => 'Maestro',
			'value' => 'MAESTRO'
		);

		$this->data['cards'][] = array(
			'text'  => 'Visa Electron UK Debit',
			'value' => 'UKE'
		);

		$this->data['cards'][] = array(
			'text'  => 'American Express',
			'value' => 'AMEX'
		);

		$this->data['cards'][] = array(
			'text'  => 'Diners Club',
			'value' => 'DC'
		);

		$this->data['cards'][] = array(
			'text'  => 'Japan Credit Bureau',
			'value' => 'JCB'
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
		if ($this->config->get('sagepay_direct_test') == 'live') {
			$url = 'https://live.sagepay.com/gateway/service/vspdirect-register.vsp';
		} elseif ($this->config->get('sagepay_direct_test') == 'test') {
			$url = 'https://test.sagepay.com/gateway/service/vspdirect-register.vsp';
		} elseif ($this->config->get('sagepay_direct_test') == 'sim') {
			$url = 'https://test.sagepay.com/Simulator/VSPDirectGateway.asp';
		}

		$order_info = $this->order->get($this->session->data['order_id']);

		$data = array();

		$data['VPSProtocol']  = '2.23';
		$data['ReferrerID']   = 'E511AF91-E4A0-42DE-80B0-09C981A3FB61';
		$data['Vendor']       = $this->config->get('sagepay_direct_vendor');
		$data['VendorTxCode'] = $this->session->data['order_id'];
		$data['Amount']       = $this->currency->format($order_info['total'], $order_info['currency_code'], 1.00000, false);
		$data['Currency']     = $this->currency->getCode();
		$data['Description']  = substr($this->config->get('config_name'), 0, 100);
		$data['CardHolder']   = $_POST['cc_owner'];
		$data['CardNumber']   = $_POST['cc_number'];
		$data['ExpiryDate']   = $_POST['cc_expire_date_month'] . substr($_POST['cc_expire_date_year'], 2);
		$data['CardType']     = $_POST['cc_type'];
		$data['TxType']       = $this->config->get('sagepay_direct_transaction');
		$data['StartDate']    = $_POST['cc_start_date_month'] . substr($_POST['cc_start_date_year'], 2);
		$data['IssueNumber']  = $_POST['cc_issue'];
		$data['CV2']          = $_POST['cc_cvv2'];

		$data['BillingSurname']    = substr($order_info['payment_lastname'], 0, 20);
		$data['BillingFirstnames'] = substr($order_info['payment_firstname'], 0, 20);
		$data['BillingAddress1']   = substr($order_info['payment_address_1'], 0, 100);

		if ($order_info['payment_address_2']) {
			$data['BillingAddress2'] = $order_info['payment_address_2'];
		}

		$data['BillingCity']     = substr($order_info['payment_city'], 0, 40);
		$data['BillingPostCode'] = substr($order_info['payment_postcode'], 0, 10);
		$data['BillingCountry']  = $order_info['payment_iso_code_2'];

		if ($order_info['payment_iso_code_2'] == 'US') {
			$data['BillingState'] = $order_info['payment_zone_code'];
		}

		$data['BillingPhone'] = substr($order_info['telephone'], 0, 20);

		if ($this->cart->hasShipping()) {
			$data['DeliverySurname']    = substr($order_info['shipping_lastname'], 0, 20);
			$data['DeliveryFirstnames'] = substr($order_info['shipping_firstname'], 0, 20);
			$data['DeliveryAddress1']   = substr($order_info['shipping_address_1'], 0, 100);

			if ($order_info['shipping_address_2']) {
				$data['DeliveryAddress2'] = $order_info['shipping_address_2'];
			}

			$data['DeliveryCity']     = substr($order_info['shipping_city'], 0, 40);
			$data['DeliveryPostCode'] = substr($order_info['shipping_postcode'], 0, 10);
			$data['DeliveryCountry']  = $order_info['shipping_iso_code_2'];

			if ($order_info['shipping_iso_code_2'] == 'US') {
				$data['DeliveryState'] = $order_info['shipping_zone_code'];
			}

			$data['CustomerName']  = substr($order_info['firstname'] . ' ' . $order_info['lastname'], 0, 100);
			$data['DeliveryPhone'] = substr($order_info['telephone'], 0, 20);
		} else {
			$data['DeliveryFirstnames'] = $order_info['payment_firstname'];
			$data['DeliverySurname']    = $order_info['payment_lastname'];
			$data['DeliveryAddress1']   = $order_info['payment_address_1'];

			if ($order_info['payment_address_2']) {
				$data['DeliveryAddress2'] = $order_info['payment_address_2'];
			}

			$data['DeliveryCity']     = $order_info['payment_city'];
			$data['DeliveryPostCode'] = $order_info['payment_postcode'];
			$data['DeliveryCountry']  = $order_info['payment_iso_code_2'];

			if ($order_info['payment_iso_code_2'] == 'US') {
				$data['DeliveryState'] = $order_info['payment_zone_code'];
			}

			$data['DeliveryPhone'] = $order_info['telephone'];
		}

		$data['CustomerEMail']   = substr($order_info['email'], 0, 255);
		$data['Apply3DSecure']   = '0';
		$data['ClientIPAddress'] = $_SERVER['REMOTE_ADDR'];

		$curl = curl_init($url);

		curl_setopt($curl, CURLOPT_PORT, 443);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));

		$response = curl_exec($curl);

		curl_close($curl);

		$data = array();

		$response_data = explode(chr(10), $response);

		foreach ($response_data as $string) {
			if (strpos($string, '=')) {
				$parts = explode('=', $string, 2);

				$data[trim($parts[0])] = trim($parts[1]);
			}
		}

		$json = array();

		if ($data['Status'] == '3DAUTH') {
			$json['ACSURL']  = $data['ACSURL'];
			$json['MD']      = $data['MD'];
			$json['PaReq']   = $data['PAReq'];
			$json['TermUrl'] = $this->url->link('payment/sagepay_direct/callback');
		} elseif ($data['Status'] == 'OK' || $data['Status'] == 'AUTHENTICATED' || $data['Status'] == 'REGISTERED') {
			$this->order->update($this->session->data['order_id'], $this->config->get('config_order_complete_status_id'));

			$message = '';

			if (isset($data['TxAuthNo'])) {
				$message .= 'TxAuthNo: ' . $data['TxAuthNo'] . "\n";
			}

			if (isset($data['AVSCV2'])) {
				$message .= 'AVSCV2: ' . $data['AVSCV2'] . "\n";
			}

			if (isset($data['AddressResult'])) {
				$message .= 'AddressResult: ' . $data['AddressResult'] . "\n";
			}

			if (isset($data['PostCodeResult'])) {
				$message .= 'PostCodeResult: ' . $data['PostCodeResult'] . "\n";
			}

			if (isset($data['CV2Result'])) {
				$message .= 'CV2Result: ' . $data['CV2Result'] . "\n";
			}

			if (isset($data['3DSecureStatus'])) {
				$message .= '3DSecureStatus: ' . $data['3DSecureStatus'] . "\n";
			}

			if (isset($data['CAVV'])) {
				$message .= 'CAVV: ' . $data['CAVV'] . "\n";
			}

			$this->Model_Checkout_Order->update_order($this->session->data['order_id'], $this->config->get('sagepay_direct_order_status_id'), $message, false);

			$json['success'] = $this->url->link('checkout/success');
		} else {
			$json['error'] = $data['StatusDetail'];
		}

		$this->response->setOutput(json_encode($json));
	}

	public function callback()
	{
		if (isset($this->session->data['order_id'])) {
			if ($this->config->get('sagepay_direct_test') == 'live') {
				$url = 'https://live.sagepay.com/gateway/service/direct3dcallback.vsp';
			} elseif ($this->config->get('sagepay_direct_test') == 'test') {
				$url = 'https://test.sagepay.com/gateway/service/direct3dcallback.vsp';
			} elseif ($this->config->get('sagepay_direct_test') == 'sim') {
				$url = 'https://test.sagepay.com/Simulator/VSPDirectCallback.asp';
			}

			$curl = curl_init($url);

			curl_setopt($curl, CURLOPT_PORT, 443);
			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
			curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($_POST));

			$response = curl_exec($curl);

			curl_close($curl);

			$data = array();

			$response_data = explode(chr(10), $response);

			foreach ($response_data as $string) {
				if (strpos($string, '=')) {
					$parts = explode('=', $string, 2);

					$data[trim($parts[0])] = trim($parts[1]);
				}
			}

			if ($data['Status'] == 'OK' || $data['Status'] == 'AUTHENTICATED' || $data['Status'] == 'REGISTERED') {
				$this->order->update($this->session->data['order_id'], $this->config->get('config_order_complete_status_id'));

				$message = '';

				if (isset($data['TxAuthNo'])) {
					$message .= 'TxAuthNo: ' . $data['TxAuthNo'] . "\n";
				}

				if (isset($data['AVSCV2'])) {
					$message .= 'AVSCV2: ' . $data['AVSCV2'] . "\n";
				}

				if (isset($data['AddressResult'])) {
					$message .= 'AddressResult: ' . $data['AddressResult'] . "\n";
				}

				if (isset($data['PostCodeResult'])) {
					$message .= 'PostCodeResult: ' . $data['PostCodeResult'] . "\n";
				}

				if (isset($data['CV2Result'])) {
					$message .= 'CV2Result: ' . $data['CV2Result'] . "\n";
				}

				if (isset($data['3DSecureStatus'])) {
					$message .= '3DSecureStatus: ' . $data['3DSecureStatus'] . "\n";
				}

				if (isset($data['CAVV'])) {
					$message .= 'CAVV: ' . $data['CAVV'] . "\n";
				}

				$this->Model_Checkout_Order->update_order($this->session->data['order_id'], $this->config->get('sagepay_direct_order_status_id'), $message, false);

				$this->url->redirect($this->url->link('checkout/success'));
			} else {
				$this->session->data['error'] = $data['StatusDetail'];

				$this->url->redirect($this->url->link('checkout/checkout'));
			}
		} else {
			$this->url->redirect($this->url->link('account/login'));
		}
	}
}
