<?php
class App_Controller_Admin_Common_Home extends Controller
{
	public function index()
	{
		$this->document->setTitle(_l("Dashboard"));

		breadcrumb(_l("Home"), site_url('admin'));

		$data = array();

		output($this->render('common/home', $data));
	}
}
