<?php
class Catalog_Controller_Module_Affiliate extends Controller
{
	protected function index()
	{
		$this->template->load('module/affiliate');

		$this->language->load('module/affiliate');

		$this->data['logged']      = $this->affiliate->isLogged();
		$this->data['register']    = $this->url->link('affiliate/register');
		$this->data['login']       = $this->url->link('affiliate/login');
		$this->data['logout']      = $this->url->link('affiliate/logout');
		$this->data['forgotten']   = $this->url->link('affiliate/forgotten');
		$this->data['account']     = $this->url->link('affiliate/account');
		$this->data['edit']        = $this->url->link('affiliate/edit');
		$this->data['password']    = $this->url->link('affiliate/password');
		$this->data['payment']     = $this->url->link('affiliate/payment');
		$this->data['tracking']    = $this->url->link('affiliate/tracking');
		$this->data['transaction'] = $this->url->link('affiliate/transaction');

		$this->render();
	}
}