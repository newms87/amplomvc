<?php
class App_Controller_Admin_Error_Permission extends Controller
{
	public function index()
	{
		$this->document->setTitle(_l("Permission Denied!"));

		$this->breadcrumb->add(_l("Home"), site_url('admin'));
		$this->breadcrumb->add(_l("Permission Denied!"), site_url('admin/error/permission'));

		output($this->render('error/permission'));
	}
}
