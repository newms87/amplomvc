<?php
class ControllerModuleBestSeller extends Controller {
	protected function index($setting) {
		$this->template->load('module/bestseller');

		$this->language->load('module/bestseller');
 
		$this->data['products'] = array();
      
      
      $featured = $this->config->get('bestseller_list');
      
      if(is_array($featured) && !empty($featured)){
         $data = array(
            'product_ids' => array_keys($featured)
           );
         
         $featured = $this->model_catalog_product->getProducts($data);
      }
      else{
         $featured = array();
      }
      
      if($setting['limit'] - count($featured) > 0){
		   $products = $this->model_catalog_product->getBestSellerProducts($setting['limit'] - count($featured));
         $products = array_merge($featured, $products);
      }
      else{
         $products = $featured;
      }
		
      foreach ($products as $product_info) {
         if ($product_info['image']) {
            $image = $this->image->resize($product_info['image'],$setting['image_width'], $setting['image_height']);
         } else {
            $image = false;
         }

         if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
            $price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_show_price_with_tax')));
         } else {
            $price = false;
         }
            
         if ((float)$product_info['special']) {
            $special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_show_price_with_tax')));
         } else {
            $special = false;
         }
            
         $this->data['products'][] = array(
            'product_id' => $product_info['product_id'],
            'thumb'      => $image,
            'name'       => $this->tool->limit_characters($product_info['name'], 25),
            'price'      => $price,
            'special'    => $special,
            'flashsale_id'=>$special?$product_info['flashsale_id']:null,
            'is_final'   => $product_info['is_final'],
            'href'       => $this->url->link('product/product','product_id='.$product_info['product_id'])
         );
      }







		$this->render();
	}
}