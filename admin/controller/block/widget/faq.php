<?php
class Admin_Controller_Block_Widget_Faq extends Controller
{
	
	public function settings(&$settings)
	{
		$this->template->load('block/widget/faq_settings');
		
		$this->data['settings'] = $settings;
		
		$this->render();
	}
	
	
	/*
	public function profile(&$profiles)
	{
		$this->template->load('block/widget/faq_profile');
		
		//Add your code here
		
		$this->data['profiles'] = $profiles;
		
		$this->render();
	}
	*/
	
	public function validate()
	{
		return $this->error;
	}
}
