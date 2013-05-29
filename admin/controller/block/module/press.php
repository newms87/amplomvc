<?php
class ControllerBlockModulePress extends Controller 
{
	
	public function settings(&$settings)
	{
		$this->load->language('block/module/press');
			
		$this->template->load('block/module/press_settings');

		if (!isset($settings['press_items'])) {
			$this->data['press_items'] = array();
		}
				
		$this->data += $settings;
		
		$this->render();
	}
	
	/*
	
	public function profile(&$profiles)
	{
		$this->load->language('block/module/press');
		
		$this->template->load('block/module/press_profile');

		$this->data += $profiles;
		
		//Add your code here
		
		$this->render();
	}
	*/
	
	public function validate()
	{
		return $this->error;
	}
}
