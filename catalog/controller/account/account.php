<?php
class ControllerAccountAccount extends Controller {
	public function index() {
		$this->template->load('account/account');

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/account');
	
			$this->url->redirect($this->url->link('account/login'));
		}
		
		$this->language->load('account/account');

		$this->document->setTitle($this->_('heading_title'));
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('account/account'));
		
		$this->data['edit'] = $this->url->link('account/edit');
		$this->data['password'] = $this->url->link('account/password');
		$this->data['address'] = $this->url->link('account/address');
		$this->data['wishlist'] = $this->url->link('account/wishlist');
		$this->data['order'] = $this->url->link('account/order');
		$this->data['download'] = $this->url->link('account/download');
		$this->data['return'] = $this->url->link('account/return');
		$this->data['transaction'] = $this->url->link('account/transaction');
		$this->data['newsletter'] = $this->url->link('account/newsletter');
		
		if ($this->config->get('reward_status')) {
			$this->data['reward'] = $this->url->link('account/reward');
		} else {
			$this->data['reward'] = '';
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
