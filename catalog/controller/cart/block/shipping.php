<?php
class ControllerCartBlockShipping extends Controller{
	
	public function index($settings = null){
		$this->language->load('cart/block/shipping');
		
		if (isset($_POST['shipping_method'])) {
			if($this->cart->setShippingMethod($_POST['shipping_method'])){
				$this->message->add('success', $this->_('text_shipping'));
				
				if($_POST['redirect']){
					$this->url->redirect(urldecode($_POST['redirect']));
				}
			}
			else{
				$this->message->add('warning', $this->cart->get_errors('shipping_method'));
			}
		}
		
		$this->data['action'] = '';
		
		$defaults = array(
			'country_id' => $this->config->get('config_country_id'),
			'zone_id' => '',
			'postcode' => '',
			'shipping_method' => '',
		);
		
		foreach($defaults as $key=>$default){
			if (isset($_POST[$key])) {
				$this->data[$key] = $_POST[$key];
			} elseif (isset($this->session->data[$key])) {
				$this->data[$key] = $this->session->data[$key];
			}else{
				$this->data[$key] = $default;
			}
		}
		
		$this->data['shipping_method'] = !empty($this->data['shipping_method']) ? $this->data['shipping_method'] : false;
		
		$this->data['apply_shipping'] = $this->url->link('cart/block/shipping');
		
		$this->data['redirect'] = $this->url->here();
		
		$this->data['countries'] = $this->model_localisation_country->getCountries();
		
		$this->response->setOutput($this->render());
	}
	
	public function quote() {
		$this->language->load('cart/block/shipping');
		
		$json = array();
		
		if (!$this->cart->hasProducts()) {
			$json['error']['warning'] = $this->_('error_product');
		}

		if (!$this->cart->hasShipping()) {
			$json['error']['warning'] = sprintf($this->_('error_no_shipping'), $this->url->link('information/contact'));
		}
		
		if(!$json){
			$this->form->init('address');
			
			if(!$this->form->validate($_POST)){
				$json['error'] = $this->form->get_errors();
			}
			elseif(!$this->cart->validateShippingAddress($_POST)){
				$json['error']['shipping_addres'] = $this->cart->get_errors('shipping_address');
			}
		}
						
		if (!$json) {
			$shipping_methods = $this->cart->getShippingMethods($_POST);
			
			if ($shipping_methods) {
				$json['shipping_method'] = $shipping_methods;
			} else {
				$json['error']['warning'] = sprintf($this->_('error_no_shipping'), $this->url->link('information/contact'));
			}
		}
		
		$this->response->setOutput(json_encode($json));
	}
}