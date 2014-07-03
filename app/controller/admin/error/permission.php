<?php
class App_Controller_Admin_Error_Permission extends Controller
{
	public function index()
	{
		$this->document->setTitle(_l("Permission Denied!"));

		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Permission Denied!"), site_url('admin/error/permission'));

		output($this->render('error/permission'));
	}
}
