<?php
class Catalog_Controller_Account_Success extends Controller
{
	public function index()
	{
		//Page Title
		$this->document->setTitle(_l("Your Account Has Been Created!"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Account"), site_url('account'));
		$this->breadcrumb->add(_l("Your Account Has Been Created!"), site_url('account/success'));

		//Template Data
		$data['approved'] = !option('config_customer_approval');

		//Action Buttons
		$data['contact']  = site_url('information/contact');
		$data['continue'] = site_url('account');

		//Render
		$this->response->setOutput($this->render('account/success', $data));
	}
}
