<?php
class Catalog_Controller_Block_Checkout_Confirm extends Controller
{
	public function index()
	{
		$this->language->load('checkout/checkout');
		$this->template->load('block/checkout/confirm');
		$this->language->load("block/checkout/confirm");
		
		//Verify the shipping details, if only the shipping method is invalid, choose a shipping method automatically
		if (!$this->cart->validateShippingDetails()) {
			if ($this->cart->hasShippingAddress() && !$this->cart->hasShippingMethod()) {
				$methods = $this->cart->getShippingMethods();
				
				if (!empty($methods)) {
					$this->cart->setShippingMethod(current($methods));
				}
				else {
					$this->data['redirect'] = $this->url->link('checkout/checkout');
					$this->message->add('warning', $this->cart->get_errors());
				}
			}
		}
		
		//Verify the payment details, if only the payment method is invalid, choose a payment method automatically
		if (!$this->cart->validatePaymentDetails()) {
			if ($this->cart->hasPaymentAddress() && !$this->cart->hasPaymentMethod()) {
				$methods = $this->cart->getPaymentMethods();
				
				if (!empty($methods)) {
					$this->cart->setPaymentMethod(current($methods));
				}
				else {
					$this->data['redirect'] = $this->url->link('checkout/checkout');
					$this->message->add('warning', $this->cart->get_errors());
				}
			}
		}
		
		if (empty($this->data['redirect'])) {
			if (!$this->cart->validateCheckout()) {
				$this->message->add('warning', $this->cart->get_errors('checkout'));
				
				if ($this->cart->get_error_code() === 'CKO-1') {
					$this->data['redirect'] = $this->url->link('cart/cart');
				} else {
					$this->data['redirect'] = $this->url->link('checkout/checkout');
				}
			}
			elseif (!$this->order->add()) {
				if ($this->order->hasError()) {
					$this->message->add('warning', $this->order->getErrors());
					$this->data['redirect'] = $this->url->link('cart/cart');
				}
				else {
					$this->data['redirect'] = $this->url->link('checkout/checkout');
				}
			}
			else {
				//If we are only reloading the totals section, do not include these other blocks
				if (empty($_GET['reload_totals'])) {
					$this->data['block_confirm_address'] = $this->getBlock('checkout/confirm_address');
					
					$this->data['block_cart'] = $this->getBlock('cart/cart', array('ajax_cart' => true));
				}
				else {
					$this->data['totals_only'] = true;
				}
				
				
				if ($this->config->get('coupon_status')) {
					$this->data['block_coupon'] = $this->getBlock('cart/coupon', array('ajax' => true));
				}
				
				$this->data['block_totals'] = $this->getBlock('cart/total');
				
				$this->data['reload_totals'] = $this->url->link('block/checkout/confirm', 'reload_totals=1');
				
				$this->data['checkout_url'] = $this->url->link('checkout/checkout');
				
				$this->data['payment'] = $this->getChild('payment/' . $this->cart->getPaymentMethodId());
			}
		}

		$this->response->setOutput($this->render());
  	}
	
	public function check_order_status()
	{
		$json = array();
		
		if (isset($_GET['order_id'])) {
			$order = $this->order->get($_GET['order_id']);
			
			if ($order['confirmed']) {
				$json = array(
					'status' => $this->order->getOrderStatus($order['order_status_id']),
					'redirect' => $this->url->link('checkout/success'),
				);
			}
		}
		
		$this->response->setOutput(json_encode($json));
	}
}
