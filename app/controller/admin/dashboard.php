<?php

class App_Controller_Admin_Dashboard extends Controller
{
	public function index()
	{
		//Page Head
		$this->document->setTitle(_l("Dashboard"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Dashboard"), site_url('admin/dashboard'));

		//Template Data
		$data['dashboards'] = $this->Model_Dashboard->getDashboards();

		//Render
		output($this->render('dashboard/list', $data));
	}

	public function view()
	{
		echo 'hello there';
	}
}
