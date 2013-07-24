<?php
class Catalog_Controller_Affiliate_Account extends Controller
{
	public function index()
	{
		$this->template->load('affiliate/account');

		if (!$this->affiliate->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('affiliate/account');
	
			$this->url->redirect($this->url->link('affiliate/login'));
		}
	
		$this->language->load('affiliate/account');

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_account'), $this->url->link('affiliate/account'));

		$this->document->setTitle($this->_('heading_title'));

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		$this->data['edit'] = $this->url->link('affiliate/edit');
		$this->data['password'] = $this->url->link('affiliate/password');
		$this->data['payment'] = $this->url->link('affiliate/payment');
		$this->data['tracking'] = $this->url->link('affiliate/tracking');
		$this->data['transaction'] = $this->url->link('affiliate/transaction');

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