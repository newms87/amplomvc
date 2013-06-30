<?php
class Catalog_Controller_Block_Product_Additional extends Controller 
{
	public function index($settings)
	{
		$product_info = !empty($settings['product_info']) ? $settings['product_info'] : null;
		
		if (!$product_info) {
			return;
		}
		
		$this->language->load('block/product/additional');
		$this->template->load('block/product/additional');
		
		$this->data['product_id'] = $product_info['product_id'];
		
		
		$this->data['is_final'] = (int)$product_info['is_final'];
		$this->_('final_sale_explanation', $this->url->link('information/information/info','information_id=7').'/#return_policy');
		
		$this->data['information'] = html_entity_decode($product_info['information'], ENT_QUOTES, 'UTF-8');
		
		if ($product_info['shipping_return']) {
			$this->data['shipping_return'] = html_entity_decode($product_info['shipping_return'], ENT_QUOTES, 'UTF-8');
			
			$this->data['is_default_shipping'] = $this->data['shipping_return'] == $this->_('shipping_return_policy');
		}
		else {
			$this->data['shipping_return'] = $this->_('shipping_return_policy');
			
			$this->data['is_default_shipping'] = true;
		}

		$this->data['shipping_return_link'] = $this->_('text_view_ship_policy', $this->url->link('information/information/info','information_id=7'));
		
		if ($this->config->get('config_show_product_attributes')) {
			$this->data['attribute_groups'] = $this->Model_Catalog_Product->getProductAttributes($product_info['product_id']);
		}
		
		$this->render();
	}
}
