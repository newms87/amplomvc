<?php
class App_Controller_Admin_Error extends Controller
{
	public function not_found($data = array())
	{
		$this->document->setTitle(_l("Page Not Found!"));

		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Page Not Found!"), site_url('admin/error/not-found'));

		output($this->render('error/not_found', $data));
	}

	public function permission()
	{
		$this->document->setTitle(_l("Permission Denied!"));

		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Permission Denied!"), site_url('admin/error/permission'));

		output($this->render('error/permission'));
	}
}
