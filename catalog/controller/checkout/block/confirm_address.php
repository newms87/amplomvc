<?php 
class ControllerCheckoutBlockConfirmAddress extends Controller {
   public function index($settings = array()) {
      $this->template->load('checkout/block/confirm_address');

      $this->language->load("checkout/block/confirm_address");
      
      $shipping_address = '';
      $payment_address = '';
      
      if($this->customer->isLogged()){
         if($this->cart->hasShipping() && isset($this->session->data['shipping_address_id'])){
            $shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);
         }
         
         if(isset($this->session->data['payment_address_id'])){
            $payment_address = $this->model_account_address->getAddress($this->session->data['payment_address_id']);
         }
      }
      elseif($this->config->get('config_guest_checkout')){
         if($this->cart->hasShipping() && isset($this->session->data['guest']['shipping_address'])){
            $shipping_address = $this->session->data['guest']['shipping_address'];
         }
         
         if(isset($this->session->data['guest']['payment_address'])){
            $payment_address = $this->session->data['guest']['payment_address'];
         }
      }
      
      //Format Shipping Addresses
      if($shipping_address){
      
         if($shipping_address['address_format']){
            $format = $shipping_address['address_format'];
         }
         else{
            $format = $this->config->get('config_address_format');
         }
         
         $format = preg_replace("/ /", "&nbsp;", $format);
         
         $this->data['shipping_address'] = $this->string_to_html($this->tool->insertables($shipping_address, $format, '{', '}'));
      }
      
      //Format Payment Address
      if($payment_address){
         
         if($payment_address['address_format']){
            $format = $payment_address['address_format'];
         }
         else{
            $format = $this->config->get('config_address_format');
         }
         
         $format = preg_replace("/ /", "&nbsp;", $format);
         
         $this->data['payment_address'] = $this->string_to_html($this->tool->insertables($payment_address, $format, '{', '}'));
      }
      
      $this->response->setOutput($this->render()); 
   }

   public function string_to_html($format){
      return preg_replace("/<br[^>]*>\s*<br[^>]*>/","<br>", nl2br($format));
   }
}
