<?php
class Admin_Controller_Block_Widget_Specials extends Controller
{
	
	/*
	public function settings(&$settings)
	{
		$this->template->load('block/widget/specials_settings');
		
		$this->data['settings'] = $settings;
		
		
		
		$this->render();
	}
	*/
	
	public function validate()
	{
		return $this->error;
	}
}
