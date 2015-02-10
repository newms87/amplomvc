<?php

class App_Controller_Common_Maintenance extends Controller
{
	public function index()
	{
		//Page Head
		set_page_info('title', _l("Maintenance"));

		//Render
		output($this->render('common/maintenance', $data));
	}
}
