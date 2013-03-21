<?php 
class ControllerCheckoutBlockConfirm extends Controller {
   public function index($settings = array(), $details_only = false) {
      $this->template->load('checkout/block/confirm');

	   $this->language->load("checkout/block/confirm");
      
      if(!$this->cart->addOrder()){
         if($this->cart->has_error('cart')){
            $this->data['redirect'] = $this->url->link('cart/cart');
         }
         else{
            $this->data['redirect'] = $this->url->link('checkout/checkout');
            $this->message->add('warning', $this->cart->get_errors());
         }
      }
      else{
         $this->language->load('checkout/checkout');
         
         $this->data['details_only'] = $details_only;
         
         if(!$details_only){
            $this->data['block_confirm_address'] = $this->getBlock('checkout', 'confirm_address');
            
            $this->data['block_cart'] = $this->getBlock('cart', 'cart', array('ajax_cart' => true));
         }
         
         if($this->config->get('coupon_status')){
            $this->data['block_coupon'] = $this->getBlock('cart','coupon', array('ajax' => true));
         }
         
         $this->data['block_totals'] = $this->getBlock('cart', 'total');
         
         $this->data['load_details'] = $this->url->link('checkout/block/confirm/load_details');
         
         $this->data['checkout_url'] = $this->url->link('checkout/checkout');
         
			$this->data['payment'] = $this->getChild('payment/' . $this->session->data['payment_method']['code']);
		}
      
		$this->response->setOutput($this->render());	
  	}
   
   public function load_details(){
      if(!$this->cart->updateShippingQuote()){
         $this->data['redirect'] = $this->url->link('checkout/checkout');
         $this->message->add('warning', $this->cart->get_errors());
      }
      elseif(!$this->cart->verifyPaymentMethod()){
         $this->data['redirect'] = $this->url->link('checkout/checkout');
         $this->message->add('warning', $this->cart->get_errors());
      }
      
      $this->index(array(), true);
   }
   
   public function check_order_status(){
      $order_id = isset($_GET['order_id'])?$_GET['order_id']:0;
      if(!$order_id)return;
      
      $status = $this->model_checkout_order->getOrderStatus($order_id);
      
      if($status){
         $json = array('status'=>$status, 'redirect'=>$this->url->link('checkout/success'));
      }
      else{
         $json = array();
      }
      echo json_encode($json);
   }
}
