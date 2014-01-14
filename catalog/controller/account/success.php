<?php
class Catalog_Controller_Account_Success extends Controller
{
	public function index()
	{
		//Page Title
		$this->document->setTitle(_l("Your Account Has Been Created!"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Account"), $this->url->link('account/account'));
		$this->breadcrumb->add(_l("Your Account Has Been Created!"), $this->url->link('account/success'));

		//Template Data
		$this->data['approved'] = !$this->config->get('config_customer_approval');

		//Action Buttons
		$this->data['contact']  = $this->url->link('information/contact');
		$this->data['continue'] = $this->url->link('account/account');

		//The Template
		$this->template->load('common/success');

		//Dependencies
		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);

		//Render
		$this->response->setOutput($this->render());
	}
}
