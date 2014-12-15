<?php

class App_Controller_Admin_Settings extends Controller
{
	public function index($data = array())
	{
		//Page Head
		$this->document->setTitle(_l("Settings"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Settings"), site_url('admin/settings'));

		//Settings Items
		$data['widgets'] = $this->Model_Setting_Setting->getWidgets();

		//Render
		output($this->render('settings/list', $data));
	}
}
