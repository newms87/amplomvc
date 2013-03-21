<?php 

/**
 * @param $product_id - Required, the product ID to generate the options template for
 * 
 * @return Product options template
 * 
 */
class ControllerProductBlockOptions extends Controller {
	 
	public function index($settings, $product_id) {
	   $this->language->load('product/block/options');
      
	   $this->template->load('product/block/options');
      
      //TODO - Change this to template controlled values
		$image_width = $this->config->get('config_image_product_option_width');
		$image_height = $this->config->get('config_image_product_option_height');
		
		$product_options = $this->model_catalog_product->getProductOptions($product_id);
      
      //return a blank template if no options were found
      if(!$product_options){
         return '';
      }
      
      foreach ($product_options as $key=>&$product_option) {
            
         if(empty($product_option['product_option_value'])){
            unset($product_options[$key]);
            continue;
         }
         
         foreach ($product_option['product_option_value'] as $key=>&$product_option_value) {
            
            //if this product is still in stock
            if (!$product_option_value['subtract'] || ((int)$product_option_value['quantity'] > 0)) {
               
               if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$product_option_value['price']) {
                  
                  if($this->config->get('config_show_price_with_tax')){
                     $product_option_value['price'] = $this->tax->calculate($product_option_value['price'], $product_info['tax_class_id']);
                  }
                  
                  $product_option_value['price'] = $this->currency->format($product_option_value['price']);
                  
               } else {
                  $product_option_value['price'] = false;
               }
               
               $with_price = '';
               if($product_option_value['price'] > 0){
                  $with_price = ' ' . $this->_('text_option_price_add') . $price;
               }
               if($product_option_value['price'] < 0){
                  $with_price = ' ' . $this->_('text_option_price_subtract') . (-1*$price);
               }
               
               $product_option_value['name']  = $product_option_value['name'] . $with_price;
               
               if($product_option['type'] == 'image'){
                  $image = $product_option_value['image'];
                  $product_option_value['thumb'] = $this->image->resize($image, $this->config->get('config_image_product_option_width'), $this->config->get('config_image_product_option_height'));
                  
                  $small_image = $this->image->resize($image, $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'));
                  $popup_image = $this->image->resize($image, $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height'));
                  $product_option_value['rel'] = "{gallery:'gal1', smallimage:'$small_image', largeimage:'$popup_image'}";
               }
            }
            else {
	            unset($product_option['product_option_value'][$key]);
            }
         }
         
         $blank_option = array();
         
         switch($product_option['type']){
            case 'select':
            case 'radio':
               $blank_option[''] = array('option_value_id'=>'', 'product_option_value_id'=>'', 'name'=>$this->_('text_select_option'));
               break;
            
            case 'image':
               if(!(int)$product_option['required']){
                  $image = $this->image->resize('data/no_image_select.png', $this->config->get('config_image_product_option_width'), $this->config->get('config_image_product_option_height'));
                  $blank_option[''] = array('option_value_id'=>'', 'product_option_value_id'=>'', 'rel'=>'', 'thumb'=>$image, 'name'=>$this->_('text_select_option'));
               }
               break;
            
            default: break;
         }
         
         if($blank_option){
            $product_option['product_option_value'] = $blank_option + $product_option['product_option_value'];
         }
      }
      
      $this->data['product_options'] = $product_options;
      
      $this->data['product_option_restrictions'] = $this->model_catalog_product->getProductOptionValueRestrictions($product_id);
      
		$this->children = array();
					
		$this->response->setOutput($this->render());
  	}
}
