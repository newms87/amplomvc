<?php 
class ControllerCheckoutBlockConfirm extends Controller {
	public function index() {
		$this->language->load('checkout/checkout');
		$this->template->load('checkout/block/confirm');
		$this->language->load("checkout/block/confirm");
		
		//Verify the shipping details, if only the shipping method is invalid, choose a shipping method automatically
		if(!$this->cart->validateShippingDetails()){
			if($this->cart->hasShippingAddress() && !$this->cart->hasShippingMethod()){
				$methods = $this->cart->getShippingMethods();
				
				if(!empty($methods)){
					$this->cart->setShippingMethod(current($methods));
				}
				else{
					$this->data['redirect'] = $this->url->link('checkout/checkout');
					$this->message->add('warning', $this->cart->get_errors());
				}
			}
		}
		
		//Verify the payment details, if only the payment method is invalid, choose a payment method automatically
		if(!$this->cart->validatePaymentDetails()){
			if($this->cart->hasPaymentAddress() && !$this->cart->hasPaymentMethod()){
				$methods = $this->cart->getPaymentMethods();
				
				if(!empty($methods)){
					$this->cart->setPaymentMethod(current($methods));
				}
				else{
					$this->data['redirect'] = $this->url->link('checkout/checkout');
					$this->message->add('warning', $this->cart->get_errors());
				}
			}
		}
		
		if(empty($this->data['redirect'])){
			if(!$this->cart->addOrder()){
				if($this->cart->has_error('cart')){
					$this->message->add('warning', $this->cart->get_errorrs('cart'));
					$this->data['redirect'] = $this->url->link('cart/cart', 'newman=1');
				}
				else{
					$this->data['redirect'] = $this->url->link('checkout/checkout');
					$this->message->add('warning', $this->cart->get_errors());
				}
			}
			else{
				//If we are only reloading the totals section, do not include these other blocks
				if(empty($_GET['reload_totals'])){
					$this->data['block_confirm_address'] = $this->getBlock('checkout', 'confirm_address');
					
					$this->data['block_cart'] = $this->getBlock('cart', 'cart', array('ajax_cart' => true));
				}
				else{
					$this->data['totals_only'] = true;
				}
				
				
				if($this->config->get('coupon_status')){
					$this->data['block_coupon'] = $this->getBlock('cart','coupon', array('ajax' => true));
				}
				
				$this->data['block_totals'] = $this->getBlock('cart', 'total');
				
				$this->data['reload_totals'] = $this->url->link('checkout/block/confirm', 'reload_totals=1');
				
				$this->data['checkout_url'] = $this->url->link('checkout/checkout');
				
				$this->data['payment'] = $this->getChild('payment/' . $this->session->data['payment_method']['code']);
			}
		}

		$this->response->setOutput($this->render());	
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
		
		$this->response->setOutput(json_encode($json));
	}
}
