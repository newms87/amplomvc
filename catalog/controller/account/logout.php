<?php
class Catalog_Controller_Account_Logout extends Controller
{
	public function index()
	{
		$this->template->load('common/success');

		if ($this->customer->isLogged()) {

			$this->customer->logout();

			$this->cart->clear();
		}

		$this->document->setTitle(_l("Account Logout"));

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Account"), $this->url->link('account/account'));
		$this->breadcrumb->add(_l("Logout"), $this->url->link('account/logout'));

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
