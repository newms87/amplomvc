<?php
class App_Controller_Admin_Error_NotFound extends Controller
{
	public function index($data = array())
	{
		$this->document->setTitle(_l("Page Not Found!"));

		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Page Not Found!"), site_url('admin/error/not_found'));

		output($this->render('error/not_found', $data));
	}
}
