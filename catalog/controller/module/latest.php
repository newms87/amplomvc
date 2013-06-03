<?php
class Catalog_Controller_Module_Latest extends Controller 
{
	protected function index($setting)
	{
		$this->template->load('module/latest');

		$this->language->load('module/latest');
		
		$this->data['products'] = array();
		
		$data = array(
			'sort'  => 'p.date_added',
			'order' => 'DESC',
			'start' => 0,
			'limit' => $setting['limit']
		);

		$results = $this->Model_Catalog_Product->getProducts($data);

		foreach ($results as $result) {
			if ($result['image']) {
				$image = $this->image->resize($result['image'], $setting['image_width'], $setting['image_height']);
			} else {
				$image = false;
			}
						
			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
				$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id']));
			} else {
				$price = false;
			}
					
			if ((float)$result['special']) {
				$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id']));
			} else {
				$special = false;
			}
			
			if ($this->config->get('config_review_status')) {
				$rating = $result['rating'];
			} else {
				$rating = false;
			}
			
			$this->data['products'][] = array(
				'product_id' => $result['product_id'],
				'thumb'		=> $image,
				'name'		=> $result['name'],
				'price'		=> $price,
				'special' 	=> $special,
				'rating'	=> $rating,
				'reviews'	=> sprintf($this->_('text_reviews'), (int)$result['reviews']),
				'href'		=> $this->url->link('product/product', 'product_id=' . $result['product_id']),
			);
		}

		$this->render();
	}
}