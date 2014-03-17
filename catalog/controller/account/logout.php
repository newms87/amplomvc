<?php
class Catalog_Controller_Account_Logout extends Controller
{
	public function index()
	{
		//Only logout if customer is logged
		if ($this->customer->isLogged()) {

			$this->customer->logout();

			$this->cart->clear();
		}

		//Page Head
		$this->document->setTitle(_l("Account Logout"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Account"), $this->url->link('account/account'));
		$this->breadcrumb->add(_l("Logout"), $this->url->link('account/logout'));

		//Action Buttons
		$this->data['continue'] = $this->url->link('common/home');

		//The Template
		$this->view->load('account/logout');

		//Dependencies
		$this->children = array(
			'area/left',
			'area/right',
			'area/top',
			'area/bottom',
			'common/footer',
			'common/header'
		);

		//Render
		$this->response->setOutput($this->render());
	}
}
