<?php
class App_Controller_Admin_Error_NotFound extends Controller
{
	public function index($data = array())
	{
		$this->document->setTitle(_l("Page Not Found!"));

		$this->breadcrumb->add(_l("Home"), site_url('admin'));
		$this->breadcrumb->add(_l("Page Not Found!"), site_url('admin/error/not_found'));

		output($this->render('error/not_found', $data));
	}
}
