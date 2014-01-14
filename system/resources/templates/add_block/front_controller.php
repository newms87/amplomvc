<?php
class __class_name__ extends Controller
{
	public function index($settings)
	{
		//Template and Language
		$this->template->load('block/__route__');
		//The Data
		$this->data = $settings;

		//Render
		$this->render();
	}
}
