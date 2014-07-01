<?php

class App_Controller_Admin_Dashboard extends Controller
{
	public function index()
	{
		//Page Head
		$this->document->setTitle(_l("Dashboard"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('admin'));
		$this->breadcrumb->add(_l("Dashboard"), site_url('admin/dashboard'));

		//Template Data
		$data['dashboards'] = $this->Model_Dashboard->getDashboards();

		//Action Buttons
		$data['insert'] = site_url('admin/dashboard/form');

		//Render
		output($this->render('page/list', $data));
	}

	public function view()
	{
		echo 'hello there';
	}
}
