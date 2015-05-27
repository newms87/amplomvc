<?php

class App_Controller_Admin_Index extends Controller
{
	public function index()
	{
		create_sprite($this->theme->getSprites());

		set_page_info('title', option('admin_title', _l("Amplo MVC Admin")));

		breadcrumb(_l("Home"), site_url('admin'));

		$data = array();

		output($this->render('index', $data));
	}
}
