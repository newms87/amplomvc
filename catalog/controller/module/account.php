<?php
class Catalog_Controller_Module_Account extends Controller 
{
	protected function index()
	{
		$this->template->load('module/account');

		$this->language->load('module/account');
		
		$this->data['logged'] = $this->customer->isLogged();
		$this->data['register'] = $this->url->link('account/register');
		$this->data['login'] = $this->url->link('account/login');
		$this->data['logout'] = $this->url->link('account/logout');
		$this->data['forgotten'] = $this->url->link('account/forgotten');
		$this->data['account'] = $this->url->link('account/account');
		$this->data['edit'] = $this->url->link('account/edit');
		$this->data['password'] = $this->url->link('account/password');
		$this->data['wishlist'] = $this->url->link('account/wishlist');
		$this->data['order'] = $this->url->link('account/order');
		$this->data['download'] = $this->url->link('account/download');
		$this->data['return'] = $this->url->link('account/return');
		$this->data['transaction'] = $this->url->link('account/transaction');
		$this->data['newsletter'] = $this->url->link('account/newsletter');

		$this->render();
	}
}