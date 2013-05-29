<?php
class ControllerProductBlockSuggestions extends Controller 
{
	
	/**
	* @param - $product_info - an array of product information
	* @param $limit - The maximum number of suggested products to show
	*/
	public function index($settings, $product_info, $limit = null)
	{
		$this->language->load('product/block/suggestions');
		
		$this->template->load('product/block/suggestions');
		
		if (!$limit) {
			$limit = $settings['limit'];
		}
		
		$image_width = 174; //$settings['image_width'];
		$image_height = 135; //$settings['image_height'];
		
		$suggestions = $this->model_catalog_product->getProductSuggestions($product_info, $limit);
		
		$this->data['suggestions'] = array();
		
		foreach ($suggestions as $p) {
			if ($p['image']) {
				$image = $this->image->resize($p['image'],$image_width,$image_height);
			} else {
				$image = false;
			}
	
			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
				$price = $this->currency->format($this->tax->calculate($p['price'], $p['tax_class _id']));
			} else {
				$price = false;
			}
				
			if ((float)$p['special']) {
				$special = $this->currency->format($this->tax->calculate($p['special'], $p['tax_class _id']));
			} else {
				$special = false;
			}
				
			$this->data['suggestions'][] = array(
				'product_id' => $p['product_id'],
				'thumb'		=> $image,
				'name'		=> $this->tool->limit_characters($p['name'], 25),
				'price'		=> $price,
				'special'	=> $special,
				'flashsale_id'=>$special?$p['flashsale_id']:null,
				'is_final'	=> $p['is_final'],
				'href'		=> $this->url->link('product/product','product_id='.$p['product_id'])
			);
		}
		
		$this->children = array();
					
		$this->response->setOutput($this->render());
  	}
}
