<?php

class App_Controller_Common_Home extends Controller
{
	public function index()
	{
		//Page Head
		$this->document->setTitle(option('config_title'));
		$this->document->setDescription(option('config_meta_description'));

		//Page Title
		$data = array(
			'page_title'     => option('config_title'),
			'call_to_action' => option('config_home_call_to_action'),
		);

		//Render
		output($this->render('common/home', $data));
	}
}
