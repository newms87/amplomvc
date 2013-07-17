<?php
class Catalog_Controller_Block_Product_Additional extends Controller 
{
	public function index($settings)
	{
		if (empty($settings['product_info'])) {
			return;
		}
		
		$product_info = $settings['product_info'];
		
		$this->language->load('block/product/additional');
		$this->template->load('block/product/additional');
		
		$this->data = $product_info;
		
		$this->data['shipping_policy'] = $this->cart->getShippingPolicy($product_info['shipping_policy_id']);
		$this->data['return_policy'] = $this->cart->getReturnPolicy($product_info['return_policy_id']);
		
		$this->data['is_default_shipping_policy'] = $product_info['shipping_policy_id'] == $this->config->get('config_default_shipping_policy');
		$this->data['is_default_return_policy'] = $product_info['return_policy_id'] == $this->config->get('config_default_return_policy');
		
		$url_shipping_return_policy = $this->url->link('information/information/shipping_return_policy','product_id=' . $product_info['product_id']);
		
		if ($this->data['return_policy']['days'] < 0) {
			$this->data['is_final_explanation'] = $this->_('final_sale_explanation', $url_shipping_return_policy);
		}
		
		if ($this->config->get('config_shipping_return_info_id')) {
			$this->_('text_view_policies', $this->url->link('information/information/info', 'information_id=' . $this->config->get('config_shipping_return_info_id')));
		} else {
			$this->data['text_view_policies'] = '';
		}
		
		if ($this->config->get('config_show_product_attributes')) {
			$this->data['attribute_groups'] = $this->Model_Catalog_Product->getProductAttributes($product_info['product_id']);
		}
		
		$this->render();
	}
}
