<?php
class ControllerModuleBlockPress extends Controller {
	protected function index($data) {
		$this->template->load('module/block/press');
		
	   $this->language->load('module/block/press');
      
		$settings = $data['settings'];
		
		$settings['image_width'] = 200;
		$settings['image_height'] = 200;
		
		foreach($settings['press_items'] as &$press){
			if(empty($press['auto_size'])){
				
				$width = !empty($press['image_width']) ? $press['image_width'] : $settings['image_width'];
				$height = !empty($press['image_height']) ? $press['image_height'] : $settings['image_height'];
				
				$press['thumb'] = $this->image->resize($press['image'], $width, $height);
				
				$press['auto_size'] = 0;
			}
			else{
				$press['thumb'] = $this->image->get($press['image']);
			}
			
			$press['description'] = html_entity_decode($press['description']);
		}
		
		$this->data['press_list'] = $settings['press_items'];
		
		$this->render();
	}
}
