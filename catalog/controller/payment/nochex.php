<?php
// Nochex via form will work for both simple "Seller" account and "Merchant" account holders
// Nochex via APC maybe only avaiable to "Merchant" account holders only - site docs a bit vague on this point
class ControllerPaymentNochex extends Controller {
	protected function index() {
		$this->template->load('payment/nochex');

		$this->load->language('payment/nochex');
		
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		
		$this->data['action'] = 'https://secure.nochex.com/';
		
		// Nochex minimum requirements
		// The merchant ID is usually your Nochex registered email address but can be altered for "Merchant" accounts see below
			if ($this->config->get('nochex_email') != $this->config->get('nochex_merchant')){ // This MUST be changed on your Nochex account!!!!
				$this->data['merchant_id'] = $this->config->get('nochex_merchant');
		} else {
			$this->data['merchant_id'] = $this->config->get('nochex_email');
		}
		
		$this->data['amount'] = $this->currency->format($order_info['total'], 'GBP', false, false);
		$this->data['order_id'] = $this->session->data['order_id'];
		$this->data['description'] = $this->config->get('config_name');

		$this->data['billing_fullname'] = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];
		
		if ($order_info['payment_address_2']) {
				$this->data['billing_address']  = $order_info['payment_address_1'] . "\r\n" . $order_info['payment_address_2'] . "\r\n" . $order_info['payment_city'] . "\r\n" . $order_info['payment_zone'] . "\r\n";
		} else {
				$this->data['billing_address']  = $order_info['payment_address_1'] . "\r\n" . $order_info['payment_city'] . "\r\n" . $order_info['payment_zone'] . "\r\n";
		}
		
		$this->data['billing_postcode'] = $order_info['payment_postcode'];

		if ($this->cart->hasShipping()) {
			$this->data['delivery_fullname'] = $order_info['shipping_firstname'] . ' ' . $order_info['shipping_lastname'];
			
			if ($order_info['shipping_address_2']) {
				$this->data['delivery_address'] = $order_info['shipping_address_1'] . "\r\n" . $order_info['shipping_address_2'] . "\r\n" . $order_info['shipping_city'] . "\r\n" . $order_info['shipping_zone'] . "\r\n";
			} else {
				$this->data['delivery_address'] = $order_info['shipping_address_1'] . "\r\n" . $order_info['shipping_city'] . "\r\n" . $order_info['shipping_zone'] . "\r\n";
			}
		
			$this->data['delivery_postcode'] = $order_info['shipping_postcode'];
		} else {
			$this->data['delivery_fullname'] = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];
			
			if ($order_info['payment_address_2']) {
				$this->data['delivery_address'] = $order_info['payment_address_1'] . "\r\n" . $order_info['payment_address_2'] . "\r\n" . $order_info['payment_city'] . "\r\n" . $order_info['payment_zone'] . "\r\n";
			} else {
				$this->data['delivery_address'] = $order_info['shipping_address_1'] . "\r\n" . $order_info['payment_city'] . "\r\n" . $order_info['payment_zone'] . "\r\n";
			}
		
			$this->data['delivery_postcode'] = $order_info['payment_postcode'];			
		}
		
		$this->data['email_address'] = $order_info['email'];
		$this->data['customer_phone_number']= $order_info['telephone'];
		$this->data['test'] = $this->config->get('nochex_test');
		$this->data['success_url'] = $this->url->link('checkout/success');
		$this->data['cancel_url'] = $this->url->link('checkout/payment');
		$this->data['declined_url'] = $this->url->link('payment/nochex/callback', 'method=decline');
		$this->data['callback_url'] = $this->url->link('payment/nochex/callback', '&order=' . $this->session->data['order_id']);
		






		$this->render();
	}
	
	public function callback() {
		$this->load->language('payment/nochex');
		
		if (isset($_GET['method']) && $_GET['method'] == 'decline') {
			$this->session->data['error'] = $this->_('error_declined');
			
			$this->url->redirect($this->url->link('cart/cart')); 
		}
		
		if (isset($_POST['order_id'])) {
			$order_id = $_POST['order_id'];
		} else {
			$order_id = 0;
		}

		$order_info = $this->model_checkout_order->getOrder($order_id);
		
		if (!$order_info) {
			$this->session->data['error'] = $this->_('error_no_order');
			
			$this->url->redirect($this->url->link('cart/cart'));
		}
		
		// Fraud Verification Step.
		$request = '';
	
		foreach ($_POST as $key => $value) {
			$request .= '&' . $key . '=' . urlencode(stripslashes($value));
		}

		$curl = curl_init('https://www.nochex.com/nochex.dll/apc/apc');

		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		$response = curl_exec($curl);
		
		curl_close($curl);
				
		if (strcmp($response, 'AUTHORISED') == 0) {
			$this->model_checkout_order->confirm($order_id, $this->config->get('nochex_order_status_id'));
		} else {
			$this->model_checkout_order->confirm($order_id, $this->config->get('config_order_status_id'), 'Auto-Verification step failed. Manually check the transaction.');
		}
		
		// Since it returned, the customer should see success.
		// It's up to the store owner to manually verify payment.
		$this->url->redirect($this->url->link('checkout/success'));
	}
}