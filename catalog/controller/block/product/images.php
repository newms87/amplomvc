<?php
class Catalog_Controller_Block_Product_Images extends Controller 
{
	
	public function index($settings)
	{
		$product_info = !empty($settings['product_info']) ? $settings['product_info'] : null;
		
		if (!$product_info) {
			return;
		}
		
		$this->language->load('block/product/images');
		$this->template->load('block/product/images');
		
		if ($product_info['image']) {
			$this->data['popup'] = $this->image->resize($product_info['image'], $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height'));
			$this->data['thumb'] = $this->image->resize($product_info['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'));
		} else {
			$this->data['popup'] = '';
			$this->data['thumb'] = '';
		}
		
		$this->data['images'] = array();
		
		$results = $this->Model_Catalog_Product->getProductImages($product_info['product_id']);
			
		array_unshift($results,array('image'=>$product_info['image']));
		
		$this->data['zoombox_width'] = $this->config->get('config_image_thumb_width') * 1.06;
		$this->data['zoombox_height'] = $this->config->get('config_image_thumb_height') * 1.01;
		$this->data['zoombox_x'] = 25;
		$this->data['zoombox_y'] = 0;
		$this->data['zoombox_position'] = 'right';
		
		foreach ($results as $result) {
			$small_image = $this->image->resize($result['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'));
			$popup_image = $this->image->resize($result['image'], $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height'));
			$this->data['images'][] = array(
				'rel' => "{gallery:'gal1', smallimage:'$small_image', largeimage:'$popup_image'}",
				'popup' => $popup_image,
				'thumb' => $this->image->resize($result['image'], $this->config->get('config_image_additional_width'), $this->config->get('config_image_additional_height'))
			);
		}
		
		$this->render();
	}
}