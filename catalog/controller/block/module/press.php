<?php
class Catalog_Controller_Block_Module_Press extends Controller 
{
	public function index($settings)
	{
		$this->template->load('block/module/press');
		
		$this->language->load('block/module/press');
		
		$settings['image_width'] = 200;
		$settings['image_height'] = 200;
		
		$settings['auto_size'] = true;
		
		foreach ($settings['press_items'] as &$press) {
			if (!empty($press['images'])) {
				foreach ($press['images'] as &$image) {
					if ($settings['auto_size']) {
						$image = $this->image->get($image);
					}
					else {
						$width = !empty($press['image_width']) ? $press['image_width'] : $settings['image_width'];
						$height = !empty($press['image_height']) ? $press['image_height'] : $settings['image_height'];
						
						$image = $this->image->resize($image, $width, $height);
					}
				}
			}
			
			$press['description'] = html_entity_decode($press['description']);
		}
		
		$this->data['press_list'] = $settings['press_items'];
		
		$this->render();
	}
}
