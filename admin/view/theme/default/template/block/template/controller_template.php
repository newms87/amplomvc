<?php
class %class_name% extends Controller 
{
	%settings_start%
	public function settings(&$settings)
	{
		$this->template->load('block/%route%_settings');
		
		//Your code goes here
		
		$this->data['settings'] = $settings;
		
		$this->render();
	}
	%settings_end%
	
	%profile_start%
	public function profile(&$profiles)
	{
		$this->template->load('block/%route%_profile');
		
		//Add your code here
		
		$this->data['profiles'] = $profiles;
		
		$this->render();
	}
	%profile_end%
	
	public function validate()
	{
		return $this->error;
	}
}
