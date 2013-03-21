<?php
class ControllerPaymentSagepayUS extends Controller {
	protected function index() {
$this->template->load('payment/sagepay_us');

    	$this->language->load('payment/sagepay_us');
		
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
	
	public function send() {
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		
		$url = 'https://www.sagepayments.net/cgi-bin/eftbankcard.dll?transaction';
		
		$data  = 'm_id=' . $this->config->get('sagepay_us_merchant_id');
		$data .= '&m_key=' . $this->config->get('sagepay_us_merchant_key');
		$data .= '&T_amt=' . urlencode($this->currency->format($order_info['total'], $order_info['currency_code'], 1.00000, false));
		$data .= '&T_ordernum=' . $this->session->data['order_id'];
		$data .= '&C_name=' . urlencode($_POST['cc_owner']);
		$data .= '&C_address=' . urlencode($order_info['payment_address_1']);
		$data .= '&C_state=' . urlencode($order_info['payment_zone']);
		$data .= '&C_city=' . urlencode($order_info['payment_city']);
		$data .= '&C_cardnumber=' . urlencode($_POST['cc_number']);
		$data .= '&C_exp=' . urlencode($_POST['cc_expire_date_month'] . substr($_POST['cc_expire_date_year'], '2'));
		$data .= '&C_cvv=' . urlencode($_POST['cc_cvv2']);
		$data .= '&C_zip=' . urlencode($order_info['payment_postcode']);
		$data .= '&C_email=' . urlencode($order_info['email']);
		$data .= '&T_code=02';
		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$response = curl_exec($ch);
		
		curl_close($ch);

		$json = array();
															
		if ($response[1] == 'A') {
			$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('config_order_status_id'));

			$message  = 'Approval Indicator: ' . $response[1] . "\n";
			$message .= 'Approval/Error Code: ' . substr($response, 2, 6) . "\n";
			$message .= 'Approval/Error Message: ' . substr($response, 8, 32) . "\n";
			$message .= 'Front-End Indicator: ' . substr($response, 40, 2) . "\n";
			$message .= 'CVV Indicator: ' . $response[42] . "\n";
			$message .= 'AVS Indicator: ' . $response[43] . "\n";
			$message .= 'Risk Indicator: ' . substr($response, 44, 2) . "\n";
			$message .= 'Reference: ' . substr($response, 46, 10) . "\n";
			$message .= 'Order Number: ' . substr($response, strpos($response, chr(28)) + 1, strrpos($response, chr(28) - 1)) . "\n";
			
			$this->model_checkout_order->update_order($this->session->data['order_id'], $this->config->get('sagepay_us_order_status_id'), $message, false);

			$json['success'] = $this->url->link('checkout/success');
		} else {
			$json['error'] = substr($response, 8, 32);
		}
		
		$this->response->setOutput(json_encode($json));		
	}	
}