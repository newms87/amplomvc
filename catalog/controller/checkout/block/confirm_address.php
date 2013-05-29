<?php
class ControllerCheckoutBlockConfirmAddress extends Controller 
{
	public function index($settings = array()) {
		$this->template->load('checkout/block/confirm_address');

		$this->language->load("checkout/block/confirm_address");
		
		$shipping_address = '';
		$payment_address = '';
		
		if ($this->cart->hasShipping() && $this->cart->hasShippingAddress()) {
			$shipping_address = $this->cart->getShippingAddress();
		}
		
		if ($this->cart->hasPaymentAddress()) {
			$payment_address = $this->cart->getPaymentAddress();
		}
		
		//Format Shipping Addresses
		if ($shipping_address) {
			if ($shipping_address['address_format']) {
				$format = $shipping_address['address_format'];
			}
			else {
				$format = $this->config->get('config_address_format');
			}
			
			$format = preg_replace("/ /", "&nbsp;", $format);
			
			$this->data['shipping_address'] = $this->string_to_html($this->tool->insertables($shipping_address, $format, '{', '}'));
		}
		
		
		//Format Payment Address
		if ($payment_address) {
			
			if ($payment_address['address_format']) {
				$format = $payment_address['address_format'];
			}
			else {
				$format = $this->config->get('config_address_format');
			}
			
			$format = preg_replace("/ /", "&nbsp;", $format);
			
			$this->data['payment_address'] = $this->string_to_html($this->tool->insertables($payment_address, $format, '{', '}'));
		}
		
		$this->response->setOutput($this->render());
	}

	public function string_to_html($format)
	{
		return preg_replace("/<br[^>]*>\s*<br[^>]*>/","<br>", nl2br($format));
	}
}
