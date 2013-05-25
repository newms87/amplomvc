<?php 
class ControllerCheckoutSuccess extends Controller { 
	public function index() { 
		$this->template->load('common/success');

		if (isset($this->session->data['order_id'])) {
			$this->cart->clear();
			
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['guest']);
			unset($this->session->data['comment']);
			unset($this->session->data['order_id']);	
			unset($this->session->data['coupons']);
			unset($this->session->data['reward']);
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);
		}	
										
		$this->language->load('checkout/success');
		
		$this->document->setTitle($this->_('heading_title'));
		
			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_basket'), $this->url->link('cart/cart'));
			$this->breadcrumb->add($this->_('text_checkout'), $this->url->link('checkout/checkout'));
			$this->breadcrumb->add($this->_('text_success'), $this->url->link('checkout/success'));

		if ($this->customer->isLogged()) {
			$this->data['text_message'] = $this->language->format('text_customer', $this->url->link('account/account'), $this->url->link('account/order'), $this->url->link('information/contact'), $this->config->get('config_name'));
		} else {
			$this->data['text_message'] = $this->language->format('text_guest', $this->url->link('information/contact'), $this->config->get('config_name'));
		}
		
		$this->data['continue'] = $this->url->link('common/home');







		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'			
		);
				
		$this->response->setOutput($this->render());
  	}
}
