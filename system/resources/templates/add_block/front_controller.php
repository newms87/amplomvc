<?php

class __class_name__ extends Controller
{
	public function index($settings)
	{
		//The Data
		$data = $settings;

		//Render
		$this->render('__path__', $data);
	}
}
