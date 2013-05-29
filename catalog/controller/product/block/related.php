<?php
class ControllerProductBlockRelated extends Controller 
{
	
	public function index($settings, $product_id)
	{
		$this->template->load('product/block/related');
		
		//Find the related products
		$related_products = $this->model_catalog_product->getProductRelated($product_id);
		
		foreach ($related_products as &$product) {
			if ($product['image']) {
				$product['image'] = $this->image->resize($product['image'], $this->config->get('config_image_related_width'), $this->config->get('config_image_related_height'));
			} else {
				$product['image'] = false;
			}
			
			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
				if ($this->config->get('config_show_price_with_tax')) {
					$product['price'] = $this->tax->calculate($product['price'], $product['tax_class _id']);
				}
				$product['price'] = $this->currency->format($product['price']);
			} else {
				$product['price'] = false;
			}
					
			if ((float)$product['special']) {
				$product['special'] = $this->currency->format($this->tax->calculate($product['special'], $product['tax_class _id']));
			} else {
				$product['special'] = false;
			}
			
			if ($this->config->get('config_review_status')) {
				$product['rating'] = (int)$product['rating'];
			} else {
				$product['rating'] = false;
			}
			
			$product['reviews'] = sprintf($this->_('text_reviews'), (int)$product['reviews']);
			
			$product['href'] = $this->url->link('product/product', 'product_id=' . $product['product_id']);
		}

		$this->data['products'] = $related_products;
		
		$this->children = array();
					
		$this->response->setOutput($this->render());
  	}
}
