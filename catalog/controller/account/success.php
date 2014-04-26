<?php
class Catalog_Controller_Account_Success extends Controller
{
	public function index()
	{
		//Page Title
		$this->document->setTitle(_l("Your Account Has Been Created!"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Account"), $this->url->link('account'));
		$this->breadcrumb->add(_l("Your Account Has Been Created!"), $this->url->link('account/success'));

		//Template Data
		$data['approved'] = !$this->config->get('config_customer_approval');

		//Action Buttons
		$data['contact']  = $this->url->link('information/contact');
		$data['continue'] = $this->url->link('account');

		//Render
		$this->response->setOutput($this->render('account/success', $data));
	}
}
