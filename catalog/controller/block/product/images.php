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
		
		$images = $this->Model_Catalog_Product->getProductImages($product_info['product_id']);
		
		//Add the main product image as the first image
		array_unshift($images,$product_info['image']);
		
		$this->data['zoombox_width'] = $this->config->get('config_image_thumb_width');
		$this->data['zoombox_height'] = $this->config->get('config_image_thumb_height');
		$this->data['zoombox_x'] = 25;
		$this->data['zoombox_y'] = 0;
		$this->data['zoombox_position'] = 'right';
		
		$image_width = $this->config->get('config_image_thumb_width');
		$image_height = $this->config->get('config_image_thumb_height');
		$image_popup_width = $this->config->get('config_image_popup_width');
		$image_popup_height = $this->config->get('config_image_popup_height');
		$image_additional_width = $this->config->get('config_image_additional_width');
		$image_additional_height = $this->config->get('config_image_additional_height');
		
		foreach ($images as $image) {
			$small_image = $this->image->resize($image, $image_width, $image_height);
			$popup_image = $this->image->resize($image, $image_popup_width, $image_popup_height);
			
			if ($small_image) {
				$this->data['images'][] = array(
					'rel' => "{gallery:'gal1', smallimage:'$small_image', largeimage:'$popup_image'}",
					'popup' => $popup_image,
					'thumb' => $this->image->resize($image, $image_additional_width, $image_additional_height),
				);
			}
		}
		
		$this->render();
	}
}
