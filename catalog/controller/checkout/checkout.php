<?php
class ControllerCheckoutCheckout extends Controller 
{
	public function index()
	{
		$this->template->load('checkout/checkout');

		if (!$this->cart->validate()) {
			$this->message->add('warning', $this->cart->get_errors());
			$this->url->redirect($this->url->link('cart/cart'));
		}
		
		$this->language->load('checkout/checkout');
		
		$this->document->setTitle($this->_('heading_title'));
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_cart'), $this->url->link('cart/cart'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('checkout/checkout'));

		$this->data['logged'] = $this->customer->isLogged();
		$this->data['shipping_required'] = $this->cart->hasShipping();
		
		$this->language->format('error_page_load', $this->config->get('config_email'));
		
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