<?php
class ControllerPaymentWorldPay extends Controller {
	protected function index() {
		$this->template->load('payment/worldpay');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		
		$this->data['action'] = 'https://select.worldpay.com/wcc/purchase';

		$this->data['merchant'] = $this->config->get('worldpay_merchant');
		$this->data['order_id'] = $order_info['order_id'];
		$this->data['amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
		$this->data['currency'] = $order_info['currency_code'];
		$this->data['description'] = $this->config->get('config_name') . ' - #' . $order_info['order_id'];
		$this->data['name'] = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];
		
		if (!$order_info['payment_address_2']) {
			$this->data['address'] = $order_info['payment_address_1'] . ', ' . $order_info['payment_city'] . ', ' . $order_info['payment_zone'];
		} else {
			$this->data['address'] = $order_info['payment_address_1'] . ', ' . $order_info['payment_address_2'] . ', ' . $order_info['payment_city'] . ', ' . $order_info['payment_zone'];
		}
		
		$this->data['postcode'] = $order_info['payment_postcode'];
		$this->data['country'] = $order_info['payment_iso_code_2'];
		$this->data['telephone'] = $order_info['telephone'];
		$this->data['email'] = $order_info['email'];
		$this->data['test'] = $this->config->get('worldpay_test');
		






		$this->render();
	}
	
	public function callback() {
		$this->language->load('payment/worldpay');
	
		$this->data['title'] = $this->language->format('heading_title', $this->config->get('config_name'));

		if (!isset($_SERVER['HTTPS']) || ($_SERVER['HTTPS'] != 'on')) {
			$this->data['base'] = $this->config->get('config_url');
		} else {
			$this->data['base'] = $this->config->get('config_ssl');
		}
	  
		$this->language->set('language', $this->language->getInfo('code'));
		$this->language->format('heading_title', $this->config->get('config_name'));
		
		$this->language->format('text_success_wait', $this->url->link('checkout/success'));
		$this->language->format('text_failure_wait', $this->url->link('checkout/checkout'));
	   
		if (isset($_POST['transStatus']) && $_POST['transStatus'] == 'Y') { 
		$this->template->load('payment/worldpay_success');

			// If returned successful but callbackPW doesn't match, set order to pendind and record reason
			if (isset($_POST['callbackPW']) && ($_POST['callbackPW'] == $this->config->get('worldpay_password'))) {
				$this->model_checkout_order->confirm($_POST['cartId'], $this->config->get('worldpay_order_status_id'));
			} else {
				$this->model_checkout_order->confirm($_POST['cartId'], $this->config->get('config_order_status_id'), $this->_('text_pw_mismatch'));
			}
	
			$message = '';

			if (isset($_POST['transId'])) {
				$message .= 'transId: ' . $_POST['transId'] . "\n";
			}
		
			if (isset($_POST['transStatus'])) {
				$message .= 'transStatus: ' . $_POST['transStatus'] . "\n";
			}
		
			if (isset($_POST['countryMatch'])) {
				$message .= 'countryMatch: ' . $_POST['countryMatch'] . "\n";
			}
		
			if (isset($_POST['AVS'])) {
				$message .= 'AVS: ' . $_POST['AVS'] . "\n";
			}	

			if (isset($_POST['rawAuthCode'])) {
				$message .= 'rawAuthCode: ' . $_POST['rawAuthCode'] . "\n";
			}	

			if (isset($_POST['authMode'])) {
				$message .= 'authMode: ' . $_POST['authMode'] . "\n";
			}	

			if (isset($_POST['rawAuthMessage'])) {
				$message .= 'rawAuthMessage: ' . $_POST['rawAuthMessage'] . "\n";
			}	
		
			if (isset($_POST['wafMerchMessage'])) {
				$message .= 'wafMerchMessage: ' . $_POST['wafMerchMessage'] . "\n";
			}				

			$this->model_checkout_order->update_order($_POST['cartId'], $this->config->get('worldpay_order_status_id'), $message, false);
	
			$this->data['continue'] = $this->url->link('checkout/success');
			






			$this->response->setOutput($this->render());				
		} else {
		$this->template->load('payment/worldpay_failure');

			$this->data['continue'] = $this->url->link('cart/cart');
	






			$this->response->setOutput($this->render());					
		}
	}
}