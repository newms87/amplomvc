<?php
class ControllerCartBlockVoucher extends Controller{
   
   public function index($settings = null){
		$this->template->load('cart/block/voucher');

      $this->language->load('cart/block/shipping');
      
      if (isset($_POST['voucher']) && $this->validateVoucher()) { 
         $this->session->data['voucher'] = $_POST['voucher'];
            
         $this->message->add('success', $this->_('text_voucher'));
      }
      
      $defaults = array(
         'voucher' => '',
      );
         
      if (isset($_POST[$key])) {
         $this->data[$key] = $_POST[$key];
      } elseif (isset($this->session->data[$key])) {
         $this->data[$key] = $this->session->data[$key];
      } else{
         $this->data[$key] = $default;
      }
      

      
      $this->response->setOutput($this->render());
   }
   
   private function validateVoucher() {
      $voucher_info = $this->model_cart_voucher->getVoucher($_POST['voucher']);        
      
      if (!$voucher_info) {         
         $this->error['warning'] = $this->_('error_voucher');
      }
      
      if (!$this->error) {
         return true;
      } else {
         return false;
      }     
   }
}