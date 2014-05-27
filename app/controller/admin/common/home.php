<?php
class App_Controller_Admin_Common_Home extends Controller
{
	public function index()
	{
		$this->document->setTitle(_l("Dashboard"));

		$this->breadcrumb->add(_l("Home"), site_url('admin/common/home'));

		$data = array();

		$this->response->setOutput($this->render('common/home', $data));
	}
}