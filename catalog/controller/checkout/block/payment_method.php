<?php
class ControllerCheckoutBlockPaymentMethod extends Controller {
  	public function index() {
  		$this->language->load('checkout/checkout');
		$this->template->load('checkout/block/payment_method');
		
		if($this->cart->hasPaymentAddress()){
			$payment_methods = $this->cart->getPaymentMethods();
			
			if (!$payment_methods) {
				$this->message->add('error', $this->language->format('error_no_payment', $this->url->link('information/contact')));
			}
			
			if ($this->cart->hasPaymentMethod()) {
				$this->data['code'] = $this->cart->getPaymentMethodId();
			} elseif(count($payment_methods) == 1){
				$method = current($payment_methods);
				$this->data['code'] = $method['code'];
			}
			else{
				$this->data['code'] = '';
			}
			
			$this->data['payment_methods'] = $payment_methods;
		}
		else{
			$this->data['no_payment_address'] = true;
		}
		
		if ($this->config->get('config_checkout_id')) {
			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_checkout_id'));
			
			if ($information_info) {
				$this->language->format('text_agree', $this->url->link('information/information/info', 'information_id=' . $this->config->get('config_checkout_id')), $information_info['title'], $information_info['title']);
				
				$this->data['agree_to_payment'] = true;
			}
		}
		
		$session_defaults = array(
			'comment' => '',
			'agree' => '',
		);
		
		foreach($session_defaults as $key => $default){
			if(isset($this->session->data[$key])){
				$this->data[$key] = $this->session->data[$key];
			}else{
				$this->data[$key] = $default;
			}
		}
		
		$this->data['validate_payment_method'] = $this->url->link('checkout/block/payment_method/validate');
		
		$this->response->setOutput($this->render());
  	}
	
	public function validate() {
		$this->language->load('checkout/checkout');
		
		$json = array();
		
		// Validate if payment address has been set.
		if ($this->cart->hasPaymentAddress()) {
			$payment_address = $this->cart->getPaymentAddress();
		}else{
			$json['error']['payment_address'] = $this->_('error_payment_address');
		}
		
		if(!$this->cart->validate()){
			$json['redirect'] = $this->url->link('cart/cart');
			$this->message->add('warning', $this->cart->get_errors());
		}
		
		if (!$json) {
			if ($this->config->get('config_checkout_id')) {
				$information_info = $this->model_catalog_information->getInformation($this->config->get('config_checkout_id'));
				
				if ($information_info && empty($_POST['agree'])) {
					$json['error']['agree'] = sprintf($this->_('error_agree'), $information_info['title']);
				}
			}
			
			if(!$json && !$this->cart->setPaymentMethod($_POST['payment_method'])){
				$json['error'] = $this->cart->get_errors('payment_method');
			}
			
			if(!$json){
				$this->cart->setComment($_POST['comment']);
			}
		}
		
		$this->response->setOutput(json_encode($json));
	}
}