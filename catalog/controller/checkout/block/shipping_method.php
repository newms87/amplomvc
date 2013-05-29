<?php
class ControllerCheckoutBlockShippingMethod extends Controller 
{
  	public function index()
  	{
  		$this->language->load('checkout/checkout');
		$this->template->load('checkout/block/shipping_method');
		
		if (isset($_POST['shipping_method'])) {
			$this->validate();
		}
		
		if ($this->cart->hasShippingAddress()) {
			$shipping_methods = $this->cart->getShippingMethods();
			
			if (!empty($shipping_methods)) {
				$this->data['shipping_methods'] = $shipping_methods;
				
				$shipping_method_id = '';
				
				if ($this->cart->hasShippingMethod()) {
					$shipping_method_id = $this->cart->getShippingMethodId();
				} else {
					//Check the first shipping method, if not selected
					$shipping_method_id = key($shipping_methods);
				}
				
				$this->data['shipping_method_id'] = $shipping_method_id;
			}
			else {
				$this->data['cart_error_shipping_method'] = $this->cart->get_errors('shipping_method');
				$this->data['allowed_shipping_zones'] = $this->cart->getAllowedShippingZones();
			}
		}
		else {
			$this->data['no_shipping_address'] = true;
		}
			
		$this->data['validate_shipping_method'] = $this->url->link('checkout/block/shipping_method/validate');
		
		$this->response->setOutput($this->render());
  	}
	
	public function validate()
	{
		$this->language->load('checkout/checkout');
		
		$json = array();
		
		// Validate cart contents
		if (!$this->cart->validate()) {
			$this->message->add('warning', $this->cart->get_errors());
			$json['redirect'] = $this->url->link('cart/cart');
		}
		elseif (!$this->cart->hasShipping()) {
			$this->message->add('warning', $this->_('error_no_shipping_required'));
			$json['redirect'] = $this->url->link('checkout/checkout');
		}
		
		if (!$json) {
			if (!isset($_POST['shipping_method'])) {
				$json['error']['warning'] = $this->_('error_shipping_method');
			} else {
				if (!$this->cart->setShippingMethod($_POST['shipping_method'])) {
					$json['error']['shipping_method'] = $this->cart->get_errors('shipping_method');
				}
			}
		}

		$this->response->setOutput(json_encode($json));
	}
}