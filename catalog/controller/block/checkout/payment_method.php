<?php
class Catalog_Controller_Block_Checkout_PaymentMethod extends Controller
{
  	public function index()
  	{
  		$this->language->load('checkout/checkout');
		$this->template->load('block/checkout/payment_method');
		
		if ($this->cart->hasPaymentAddress()) {
			$payment_methods = $this->cart->getPaymentMethods();
			
			if (!$payment_methods) {
				$payment_methods = array();
			}
			else {
				if ($this->cart->hasPaymentMethod()) {
					$this->data['code'] = $this->cart->getPaymentMethodId();
				} else {
					$this->data['code'] = key($payment_methods);
				}
			}
			
			$this->data['payment_methods'] = $payment_methods;
		}
		else {
			$this->data['no_payment_address'] = true;
		}
		
		if ($this->config->get('config_checkout_terms_info_id')) {
			$information_info = $this->Model_Catalog_Information->getInformation($this->config->get('config_checkout_terms_info_id'));
			
			if ($information_info) {
				$this->_('text_agree', $this->url->link('information/information/info', 'information_id=' . $this->config->get('config_checkout_terms_info_id')), $information_info['title'], $information_info['title']);
				
				$this->data['agree_to_payment'] = true;
			}
		}
		
		$session_defaults = array(
			'comment' => '',
			'agree' => '',
		);
		
		foreach ($session_defaults as $key => $default) {
			if (isset($this->session->data[$key])) {
				$this->data[$key] = $this->session->data[$key];
			} else {
				$this->data[$key] = $default;
			}
		}
		
		$this->data['validate_payment_method'] = $this->url->link('block/checkout/payment_method/validate');
		
		$this->response->setOutput($this->render());
  	}
	
	public function validate()
	{
		$this->language->load('checkout/checkout');
		
		$json = array();
		
		// Validate if payment address has been set.
		if ($this->cart->hasPaymentAddress()) {
			$payment_address = $this->cart->getPaymentAddress();
		} else {
			$json['error']['payment_address'] = $this->_('error_payment_address');
		}
		
		if (!$this->cart->validate()) {
			$json['redirect'] = $this->url->link('cart/cart');
			$this->message->add('warning', $this->cart->get_errors());
		}
		
		if (!$json) {
			if ($this->config->get('config_checkout_terms_info_id')) {
				$information_info = $this->Model_Catalog_Information->getInformation($this->config->get('config_checkout_terms_info_id'));
				
				if ($information_info && empty($_POST['agree'])) {
					$json['error']['agree'] = sprintf($this->_('error_agree'), $information_info['title']);
				}
			}
			
			if (!isset($_POST['payment_method'])) {
				$json['error']['_payment_method'] = $this->_('error_payment_method'); //We use _payment_method to avoid builder->js('errors') adding error under radio input
			} elseif (!$this->cart->setPaymentMethod($_POST['payment_method'])) {
				$json['error'] = $this->cart->get_errors('payment_method');
			}
			
			if (!$json) {
				$this->cart->setComment($_POST['comment']);
			}
		}
		
		$this->response->setOutput(json_encode($json));
	}
}