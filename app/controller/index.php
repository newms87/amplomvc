<?php

class App_Controller_Index extends Controller
{
	public function index()
	{
		//Page Head
		set_page_info('title', option('site_title'));

		set_page_meta('description', option('site_meta_description'));

		//Render
		output($this->render('index'));
	}
}
