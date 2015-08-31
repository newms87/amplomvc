<?php

class App_Controller_Common_Maintenance extends Controller
{
	public function index()
	{
		//Page Head
		set_page_info('title', _l("Maintenance"));

		$this->document->setLinks('primary', array());
		$this->document->setLinks('account', array());
		$this->document->setLinks('footer', array());

		//Render
		output($this->render('common/maintenance'));
	}
}
