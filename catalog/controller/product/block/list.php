<?php
class ControllerProductBlockList extends Controller {
	
	public function index($setting, $data, $template = 'product/list', $process_data = true) {
		$this->template->load($template);
		$this->language->load('product/block/list');
		
		//TODO: need to implement these options in admin panel!
		$this->data['list_show_add_to_cart'] = $this->config->get('config_list_show_add_to_cart');
		$this->data['show_price_tax'] = $this->config->get('config_show_price_with_tax');
		$this->data['review_status'] = $this->config->get('config_review_status');
		
		if($process_data){
			foreach ($data as &$item) {
				$item['thumb'] = $this->image->resize($item['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
				
				if(($this->config->get('config_customer_price') ? $this->customer->isLogged() : true)){
					if (!empty($item['price'])) {
						$item['price'] = $this->currency->format($this->tax->calculate($item['price'], $item['tax_class_id']));
					}
					
					if(!empty($item['special'])){
						$item['special'] = $this->currency->format($this->tax->calculate($item['special'], $item['tax_class_id']));
					}
				} else {
					$item['price'] = false;
				}
				
				if ($this->data['show_price_tax']) {
					$item['tax'] = $this->currency->format((float)$item['special'] ? $item['special'] : $item['price']);
				}
				
				if ($this->data['review_status']) {
					$item['rating'] = (int)$item['rating'];
					$item['reviews'] = sprintf($this->_('text_reviews'), (int)$item['reviews']);
				}
				
				$item['description'] = substr(strip_tags(html_entity_decode($item['description'], ENT_QUOTES, 'UTF-8')), 0, 100) . '..';
				
				if (empty($item['href'])) {
					$item['href'] = $this->url->link('product/product', 'product_id=' . $item['product_id']);
				}
			}
		}

		$this->data['products'] = $data;
		
		//Product Wishlist
		$this->data['wishlist_status'] = $this->config->get('config_wishlist_status');
		
		//Product Compare
		$this->data['compare_status'] = $this->config->get('config_compare_status');
		
		if ($this->data['compare_status']) {
			$this->language->format('text_compare', $this->cart->get_compare_count());
			$this->data['compare'] = $this->url->link('product/compare');
		}
		
		$this->data['sort_url'] = $this->sort->get_sort_url();
		
		$this->render();
  	}
}
