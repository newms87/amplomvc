<?php
class ControllerBlockModulePress extends Controller {
	 
	public function settings(&$settings) {
		$this->load->language('block/module/press');
		   
		$this->template->load('block/module/press_settings');

		//Add your code here
		$thumb_width = $this->config->get('config_image_default_width');
		$thumb_height = $this->config->get('config_image_default_height');
		
		$this->data['no_image'] = $this->image->resize('data/no_image.jpg', $thumb_width, $thumb_height);
		$this->data['thumb_width'] = $thumb_width;
		$this->data['thumb_height'] = $thumb_height;
		
		if(!isset($settings['press_items'])){
			$this->data['press_items'] = array();
		}
		else{
			foreach($settings['press_items'] as &$press){
				$press['thumb'] = $this->image->resize($press['image'], $thumb_width, $thumb_height);
			}
		}
		
		$this->data += $settings;
		
		$this->render();
	}
	
	
	public function profile(&$profiles) {
		$this->load->language('block/module/press');
		
		$this->template->load('block/module/press_profile');

		$this->data += $profiles;
		
		//Add your code here
		
		$this->render();
	}
	
	
	public function validate() {
		return $this->error;
	}
}
