<?php
class Admin_Controller_Error_Permission extends Controller
{
	public function index()
	{
		$this->document->setTitle(_l("Permission Denied!"));

		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Permission Denied!"), site_url('error/permission'));

		$this->response->setOutput($this->render('error/permission'));
	}
}
