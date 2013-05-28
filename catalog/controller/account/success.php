<?php
class ControllerAccountSuccess extends Controller {
	public function index() {
		$this->template->load('common/success');

		$this->language->load('account/success');
  
		$this->document->setTitle($this->_('heading_title'));
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_account'), $this->url->link('account/account'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('account/success'));
		
		if (!$this->config->get('config_customer_approval')) {
			$this->language->format('text_message', $this->url->link('information/contact'));
		} else {
			$this->data['text_message'] = $this->language->format('text_approval', $this->config->get('config_name'), $this->url->link('information/contact'));
		}
		
		if ($this->cart->hasProducts()) {
			$this->data['continue'] = $this->url->link('cart/cart');
		} else {
			$this->data['continue'] = $this->url->link('account/account');
		}

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
