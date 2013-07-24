<?php
class Catalog_Controller_Checkout_Success extends Controller
{
	public function index()
	{
		$this->template->load('common/success');

		if ($this->cart->hasOrder()) {
			$this->cart->clear();
		}
										
		$this->language->load('checkout/success');
		
		$this->document->setTitle($this->_('heading_title'));
		
			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_basket'), $this->url->link('cart/cart'));
			$this->breadcrumb->add($this->_('text_checkout'), $this->url->link('checkout/checkout'));
			$this->breadcrumb->add($this->_('text_success'), $this->url->link('checkout/success'));

		if ($this->customer->isLogged()) {
			$this->data['text_message'] = $this->_('text_customer', $this->url->link('account/account'), $this->url->link('account/order'), $this->url->link('information/contact'), $this->config->get('config_name'));
		} else {
			$this->data['text_message'] = $this->_('text_guest', $this->url->link('information/contact'), $this->config->get('config_name'));
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
