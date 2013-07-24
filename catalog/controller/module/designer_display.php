<?php
class Catalog_Controller_Module_DesignerDisplay extends Controller
{
	protected function index($setting)
	{
		$this->template->load('module/designer_display');

		$this->language->load('module/designer_display');

		$this->data['designers'] = array();

		$designers = explode(',', $this->config->get('featured_designers'));

		if (empty($setting['limit'])) {
			$setting['limit'] = 9;
		}
		
		$designers = array_slice($designers, 0, (int)$setting['limit']);
		
		foreach ($designers as $designer_id) {
			$designer_info = $this->Model_Catalog_Manufacturer->getManufacturer($designer_id);
			if ($designer_info) {
				if ($designer_info['image']) {
					$image = $this->image->get($designer_info['image']);
				} else {
					$image = false;
				}
				
				$featured_product = $this->Model_Catalog_Product->getProduct($designer_info['featured_product_id']);
				
				//product not found or not active
				if(empty($featured_product))continue;
				
				$p_images = $this->Model_Catalog_Product->getProductImages($featured_product['product_id']);
				$product_images = array();
				if(isset($p_images))
					foreach ($p_images as $pi) {
						$product_images[] = $this->image->resize($pi['image'], $setting['image_width'], $setting['image_height']);
					}
				
				if ($featured_product['special'] && $featured_product['special'] > 0) {
					$featured_product['sale_price'] = '$' . number_format($featured_product['special'],2);
				}
				else {
					$featured_product['sale_price'] = null;
				}
				$featured_product['price'] = '$' . number_format($featured_product['price'],2);
				
				$featured_product['images'] = $product_images;
				$this->data['designers'][] = array(
					'manufacturer_id' => $designer_info['manufacturer_id'],
					'image'		=> $image,
					'name'		=> $designer_info['name'],
					'featured_product' => $featured_product,
					'cat_name'	=> $featured_product['cat_name'],
					'href'		=> $this->url->site($designer_info['keyword']),
					'interview_href' => $this->url->site($designer_info['keyword'] . "/interview")
				);
			}
		}

		$this->render();
	}
}