<?php
class ControllerBlockModuleSidebar extends Controller {
	 
	public function settings(&$settings) {
		$this->load->language('block/module/sidebar');
		   
		$this->template->load('block/module/sidebar_settings');

		$this->data += $settings;
		
		//Add your code here
		
		$this->render();
	}
	
	
	public function profile(&$profiles) {
		$this->load->language('block/module/sidebar');
		
		$this->template->load('block/module/sidebar_profile');

		$this->data += $profiles;
		
		//Add your code here
		
		$this->render();
	}
	
	
	public function validate() {
		return $this->error;
	}
}
