<?php

class App_Controller_Index extends Controller
{
	public function index()
	{
		//Page Head
		$this->document->setTitle(option('config_title'));
		$this->document->setDescription(option('config_meta_description'));

		//Render
		output($this->render('index'));
	}
}
