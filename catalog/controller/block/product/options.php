<?php
class Catalog_Controller_Block_Product_Options extends Controller
{
	public function index($settings)
	{
		$product_id = !empty($settings['product_id']) ? $settings['product_id'] : null;
		
		if (!$product_id) {
			return '';
		}
		
		$product_options = $this->Model_Catalog_Product->getProductOptions($product_id);
		
		//return a blank template if no options were found
		if (!$product_options) {
			return '';
		}
		
		//Template and Language
		$this->language->load('block/product/options');
		$this->template->load('block/product/options');
		
		$image_width = $this->config->get('config_image_product_option_width');
		$image_height = $this->config->get('config_image_product_option_height');
		$image_thumb_width = $this->config->get('config_image_thumb_width');
		$image_thumb_height = $this->config->get('config_image_thumb_height');
		$image_popup_width = $this->config->get('config_image_popup_width');
		$image_popup_height = $this->config->get('config_image_popup_height');
		
		foreach ($product_options as $key => &$product_option) {
			if (empty($product_option['product_option_values'])) {
				unset($product_options[$key]);
				continue;
			}
			
			$product_option['default'] = array();
			
			foreach ($product_option['product_option_values'] as $key => &$product_option_value) {
				//if this product is still in stock
				if (!$product_option_value['subtract'] || ((int)$product_option_value['quantity'] > 0)) {
					//Hide Price for non-logged customers
					if ($this->config->get('config_customer_hide_price') && !$this->customer->isLogged()) {
						$product_option_value['price'] = false;
					}
					else {
						$pov_price = $product_option_value['price'];
						
						if ($this->config->get('config_show_price_with_tax')) {
							$pov_price= $this->tax->calculate($pov_price, $product_info['tax_class_id']);
						}
						
						$product_option_value['price'] = $this->currency->format($pov_price);
						
						//Show the price with the Product Option Name
						if ($pov_price > 0) {
							$product_option_value['value'] .= $this->_('text_option_price_add', $pov_price, $product_option_value['price']);
						} elseif ($pov_price < 0) {
							$product_option_value['value'] .= $this->_('text_option_price_subtract', $pov_price, $product_option_value['price']);
						}
					}
					
					if ($product_option['type'] == 'image') {
						$image = $product_option_value['image'];
						$product_option_value['thumb'] = $this->image->resize($image, $image_width, $image_height);
						
						$small_image = $this->image->resize($image, $image_thumb_width, $image_thumb_height);
						$popup_image = $this->image->resize($image, $image_popup_width, $image_popup_height);
						$product_option_value['rel'] = "{gallery:'gal1', smallimage:'$small_image', largeimage:'$popup_image'}";
					}
					
					if ($product_option_value['default']) {
						$product_option['default'][] = $product_option_value['product_option_value_id'];
					}
				}
				else {
					unset($product_option['product_option_values'][$key]);
				}
			} unset($product_option_value);
			
			$blank_option = array();
			
			switch($product_option['type']){
				case 'select':
					$blank_option[''] = array('option_value_id'=>'', 'product_option_value_id'=>'', 'value' => $this->_('text_select_option'));
					break;
				
				case 'radio':
				case 'checkbox':
					break;
					
				case 'image':
					if (!(int)$product_option['required']) {
						$image = $this->image->resize('data/no_image_select.png', $image_width, $image_height);
						$blank_option[''] = array('option_value_id'=>'', 'product_option_value_id'=>'', 'rel'=>'', 'thumb'=>$image, 'value'=>$this->_('text_select_option'));
					}
					break;
				
				default: break;
			}
			
			if ($blank_option) {
				$product_option['product_option_values'] = $blank_option + $product_option['product_option_values'];
			}
		} unset($product_option);
		
		$this->data['product_options'] = $product_options;
		
		$this->data['no_image'] = $this->image->resize('no_image.png', $this->config->get('config_image_product_option_width'), $this->config->get('config_image_product_option_height'));
		
		$this->response->setOutput($this->render());
  	}
}
