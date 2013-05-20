<?php
class ControllerCheckoutBlockCustomerInformation extends Controller {
   public function index(){
      $this->template->load('checkout/block/customer_information');
      $this->language->load('checkout/block/customer_information');
		
      if(!$this->customer->isLogged()){
      	$this->data['guest_checkout'] = true;
      
         $this->data['block_guest_information'] = $this->getBlock('checkout', 'guest_information');
      }
      else{
         $this->data['block_payment_address'] = $this->getBlock('checkout', 'payment_address');
         
         if($this->cart->hasShipping()){
            $this->data['block_shipping_address'] = $this->getBlock('checkout', 'shipping_address');
         }
      }
		
		if($this->cart->hasShipping()){
         $this->data['block_shipping_method'] = $this->getBlock('checkout', 'shipping_method');
      }
      
      $this->data['block_payment_method'] = $this->getBlock('checkout', 'payment_method');
      
      $this->response->setOutput($this->render());
   }
   
   public function validate(){
      $json = array();
      
      $this->language->load('checkout/block/customer_information');
      
      if(!$this->cart->hasPaymentAddress()){
         $json['error']['payment_address'] = $this->_('error_payment_address');
      }
		
		if(!$this->cart->hasPaymentMethod()){
         $json['error']['payment_method'] = $this->_('error_payment_method');
      }
      
		if($this->cart->hasShipping()){
	      if(!$this->cart->hasShippingAddress()){
	         $json['error']['shipping_address'] = $this->_('error_shipping_address');
	      }
	      
	      if(!$this->cart->hasShippingMethod()){
	         $json['error']['shipping_method'] = $this->_('error_shipping_method');
	      }
      }

      $this->response->setOutput(json_encode($json));
   }
}