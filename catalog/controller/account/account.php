<?php
class Catalog_Controller_Account_Account extends Controller
{
	public function index()
	{
		$this->template->load('account/account');

		if (!$this->customer->isLogged()) {
			$this->request->setRedirect('account/account');

			$this->url->redirect('account/login');
		}

		$this->document->setTitle(_l("My Account"));

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("My Account"), $this->url->link('account/account'));

		$this->data['update']         = $this->url->link('account/update');
		$this->data['password']       = $this->url->link('account/password');
		$this->data['address']        = $this->url->link('account/address');
		$this->data['wishlist']       = $this->url->link('account/wishlist');
		$this->data['order']          = $this->url->link('account/order');
		$this->data['download']       = $this->url->link('account/download');
		$this->data['return_view']    = $this->url->link('account/return');
		$this->data['return_request'] = $this->url->link('account/return/insert');
		$this->data['transaction']    = $this->url->link('account/transaction');
		$this->data['newsletter']     = $this->url->link('account/newsletter');

		if ($this->config->get('reward_status')) {
			$this->data['reward'] = $this->url->link('account/reward');
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
