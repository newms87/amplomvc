<?php

class App_Controller_Common_Maintenance extends Controller
{
	public function index()
	{
		//Page Head
		set_page_info('title', _l("Maintenance"));

		$this->document->setLinks('primary', array());
		
		//Render
		output($this->render('common/maintenance'));
	}
}
