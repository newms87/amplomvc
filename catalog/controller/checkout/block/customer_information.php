<?php
class ControllerCheckoutBlockCustomerInformation extends Controller {
   public function index(){
      $this->template->load('checkout/block/customer_information');
      
      $this->data['guest_checkout'] = !$this->customer->isLogged();
      
      if($this->data['guest_checkout']){
         if(isset($this->session->data['guest'])){
            $this->data['had_guest_info'] = true;
         }
         $this->data['guest_information'] = $this->getBlock('checkout', 'guest_information');
         
         if($this->cart->hasShipping()){
            $this->data['shipping_method'] = $this->getBlock('checkout', 'shipping_method');
         }
         
         $this->data['payment_method'] = $this->getBlock('checkout', 'payment_method');
      }
      elseif($this->customer->verifyDefaultAddress()){
         $this->data['payment_address'] = $this->getBlock('checkout', 'payment_address');
         
         $this->data['payment_method'] = $this->getBlock('checkout', 'payment_method');
         
         if($this->cart->hasShipping()){
            $this->data['shipping_address'] = $this->getBlock('checkout', 'shipping_address');
            
            $this->data['shipping_method'] = $this->getBlock('checkout', 'shipping_method');
         }
      }
      else{
         $this->data['no_address'] = true;
         
         $this->data['new_address'] = $this->getBlock('checkout', 'new_address');
         
      }
      
      //TODO - move this to top after updating language system to load new language objects for each controller instance.
      $this->language->load('checkout/block/customer_information');
      
      $this->response->setOutput($this->render());
   }
   
   public function validate(){
      $json = array();
      
      $this->language->load('checkout/block/customer_information');
      
      if(!isset($this->session->data['guest']['payment_address']) && !isset($this->session->data['payment_address_id'])){
         $json['error']['payment_address'] = $this->_('error_payment_address');
      }
      
      if(!isset($this->session->data['guest']['shipping_address']) && !isset($this->session->data['shipping_address_id'])){
         $json['error']['shipping_address'] = $this->_('error_shipping_address');
      }
      
      if(!isset($this->session->data['payment_method'])){
         $json['error']['payment_method'] = $this->_('error_payment_method');
      }

      if(!isset($this->session->data['shipping_method'])){
         $json['error']['shipping_method'] = $this->_('error_shipping_method');
      }

      $this->response->setOutput(json_encode($json));
   }
}