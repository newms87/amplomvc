<?php
class App_Controller_Admin_Error_NotFound extends Controller
{
	public function index($data = array())
	{
		$this->document->setTitle(_l("Page Not Found!"));

		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Page Not Found!"), site_url('error/not_found'));

		$this->response->setOutput($this->render('error/not_found', $data));
	}
}
