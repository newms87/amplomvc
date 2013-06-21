<?php
class %class_name% extends Controller 
{
	public function index($settings)
	{
		$this->template->load('block/%route%');
		$this->language->load('block/%route%');
		
		//Your code goes here...
		
		$this->render();
	}
}
