<?php
class ModelCatalogProduct extends Model {
	public function addProduct($data) {
	   if($this->user->isDesigner()){
         $data['quantity'] = 1;
         $data['minimum'] = 1;
         $data['subtract'] = 1;
         $data['is_final'] = 1;
         $data['stock_status_id'] = 5;
         $data['date_available'] = date_format(new DateTime(),'Y-m-d H:i:s');
         $data['date_expires'] = DATETIME_ZERO;
         $data['shipping'] = 1;
         $data['price'] = 0;
         $data['cost'] = 0;
         $data['points'] = 0;
         $data['editable'] = 1;
         $data['status'] = 0;
         $data['tax_class_id'] = $this->config->get('config_tax_default_id');
         $data['product_store'] = array(0,1,2);
         
	      if(!$data['model'])
	        $data['model'] = $this->generate_model($data['product_description'][1]['name']);
      }
      
      $data['date_added'] = date_format(new DateTime(),'Y-m-d H:i:s');
      
		$product_id = $this->insert('product', $data);
		
      if(!$product_id)return;
      
      //Product Descriptions
		foreach ($data['product_description'] as $language_id => $value) {
		   $value['product_id'] = $product_id;
         $value['language_id'] = $language_id;
         
         if(!$value['meta_description']){
            $value['meta_description'] = strip_tags(html_entity_decode($value['blurb']));
         }
         
         if(!$value['meta_keyword'] && isset($data['product_tag'][$language_id])){
            $value['meta_keyword'] = $data['product_tag'][$language_id];
         }
            
			$this->insert('product_description', $value);
		}
		
      
      //Product Store
		if (isset($data['product_store'])) {
			foreach ($data['product_store'] as $store_id) {
			   $values = array(
			      'store_id' => $store_id,
			      'product_id' => $product_id
			     );
				$this->insert('product_to_store', $values);
			}
		}
      
      
      //Product Attributes
		if (isset($data['product_attributes'])) {
            foreach ($data['product_attributes'] as $product_attribute) {
               $product_attribute['product_id'] = $product_id;
               $product_attribute['language_id'] = $language_id;
               
               $this->insert('product_attribute', $product_attribute);
            }
         }
      
      //Product Options
		if (isset($data['product_options'])) {
			foreach ($data['product_options'] as $product_option) {
			   if($this->user->isDesigner()){
			      $product_option['required'] = 1;
            }
            
				if (in_array($product_option['type'], array('select', 'radio', 'checkbox', 'image'))) {
				   $product_option['product_id'] = $product_id;
               
					$product_option_id = $this->insert('product_option', $product_option);
				
					if (isset($product_option['product_option_value'])) {
						foreach ($product_option['product_option_value'] as $product_option_value) {
						   if($this->user->isDesigner()){
						      $product_option_value['cost'] = 0;
                        $product_option_value['price'] = 0;
                        $product_option_value['points'] = 0;
                        $product_option_value['weight'] = 0;
					      }
							
                     $product_option_value['product_option_id'] = $product_option_id;
                     $product_option_value['product_id'] = $product_id;
                     $product_option_value['option_id'] = $product_option['option_id'];
                     
							$product_option_value_id = $this->insert('product_option_value', $product_option_value);
                     
                     if(isset($product_option_value['restrictions'])){
                        foreach($product_option_value['restrictions'] as $restriction){
                           $restriction['product_id']      = $product_id;
                           $restriction['option_value_id'] = $product_option_value['option_value_id'];
                           
                           $this->insert('product_option_value_restriction', $restriction);
                        }
                     }
						}
					}
				} else {
				   $product_option['product_id'] = $product_id;
               
					$this->insert('product_option',  $product_option);
				}
			}
		}
      
      
      //Additional Product Images
      if (isset($data['product_images'])) {
         foreach ($data['product_images'] as $product_image) {
            $product_image['product_id'] = $product_id;
            
            $this->insert('product_image', $product_image);
         }
      }

      		
		if(!$this->user->isDesigner()){
		   
         //Product Categories
		   if (isset($data['product_category'])) {
            foreach (array_unique($data['product_category']) as $category_id) {
               $values = array(
                  'product_id' => $product_id,
                  'category_id' => $category_id
                 );
               $this->insert('product_to_category',  $values);
            }
         }
         
         
         //Product Discount
   		if (isset($data['product_discounts'])) {
   			foreach ($data['product_discounts'] as $product_discount) {
   			   $product_discount['product_id'] = $product_id;
               
   				$this->insert('product_discount', $product_discount);
   			}
   		}
         
         
         //Product Specials
   		if (isset($data['product_specials'])) {
   			foreach ($data['product_specials'] as $product_special) {
   			   $product_special['product_id'] = $product_id;
               
   				$this->insert('product_special', $product_special);
   			}
   		}
   		
         
         //Product Downloads
   		if (isset($data['product_download'])) {
   			foreach ($data['product_download'] as $download_id) {
   			   $values = array(
   			      'download_id' => $download_id,
   			      'product_id' => $product_id
   			     );
   				$this->insert('product_to_download', $values);
   			}
   		}
   		
         
         //Product Related
   		if (isset($data['product_related'])) {
   			foreach ($data['product_related'] as $related_id) {
   			   $values = array(
   			      'product_id' => $product_id,
   			      'related_id' => $related_id
   			     );
   			      
   				$this->insert('product_related', $values);
               
               //the inverse so the other product is related to this product too!
               $values = array(
                  'product_id' => $related_id,
                  'related_id' => $product_id
                 );
                  
               $this->insert('product_related', $values);
   			}
   		}
         
         
         //Product Reward
   		if (isset($data['product_reward'])) {
   			foreach ($data['product_reward'] as $customer_group_id => $product_reward) {
   			   $product_reward['product_id'] = $product_id;
               $product_reward['customer_group_id'] = $customer_group_id;
               
   				$this->insert('product_reward', $product_reward);
   			}
   		}
         
         
         //Product Layouts
   		if (isset($data['product_layout'])) {
   			foreach ($data['product_layout'] as $store_id => $layout) {
   				if ($layout['layout_id']) {
   				   $layout['product_id'] = $product_id;
                  $layout['store_id'] = $store_id;
                  
   					$this->insert('product_to_layout', $layout);
   				}
   			}
   		}
         
         //Product Templates
         if (isset($data['product_template'])) {
            foreach ($data['product_template'] as $store_id => $themes) {
               foreach($themes as $theme => $template){
                  if(empty($template['template'])) continue;
                  
                  $template['product_id'] = $product_id;
                  $template['theme'] = $theme;
                  $template['store_id'] = $store_id;
                  
                  $this->insert('product_template', $template); 
               }
            }
         }
   		
   		foreach ($data['product_tag'] as $language_id => $value) {
   			if ($value) {
   				$tags = explode(',', $value);
   				
   				foreach ($tags as $tag) {
   				   $values = array(
   				     'product_id' => $product_id,
   				     'language_id' => $language_id,
   				     'tag' => trim($tag)
                   );
                   
   					$this->insert('product_tag', $values);
   				}
   			}
   		}
		}
						
		if ($data['keyword']) {
         if(!preg_match("/^product\//",$data['keyword'])){
            $data['keyword'] = 'product/' . $data['keyword']; 
         }
         
         $url_alias = array(
            'route'=>'product/product',
            'query'=>'product_id=' . (int)$product_id,
            'keyword'=>$this->db->escape($data['keyword']),
            'status'=>$data['status'],
           );
         
         $this->model_setting_url_alias->addUrlAlias($url_alias);
      }
      
		$this->cache->delete('product');
	}
	
	public function editProduct($product_id, $data) {
      if($this->user->isDesigner()){
         if(!$data['model']){
            $data['model'] = $this->generate_model($data['name']);
         }
         
         $data['status'] = 0;
      }
      
      if($this->user->isDesigner()){
         
         $values = array();
         
         $allowed = array('model','image','manufacturer_id', 'weight', 'weight_class_id', 'length','width','height','length_class_id');
         
         foreach(array_keys($data) as $key){
            if(in_array($key, $allowed)){
               $values[$key] = $data[$key];
            }
         }
         
         $values['date_modified'] = date_format(new DateTime(),'Y-m-d H:i:s');
         
         $this->update('product', $values, $product_id);
      }
      else{
         $data['date_modified'] = date_format(new DateTime(),'Y-m-d H:i:s');
         
		   $this->update('product', $data, $product_id);
      }
      
      
      //Product Descriptions
		$this->delete('product_description', array('product_id'=>$product_id));
		
		foreach ($data['product_description'] as $language_id => $value) {
         $value['product_id'] = $product_id;
         $value['language_id'] = $language_id;

         if(!$value['meta_description']){
            $value['meta_description'] = strip_tags(html_entity_decode($value['blurb']));
         }
         
         if(!$value['meta_keyword']){
            $value['meta_keyword'] = $data['product_tag'][$language_id];
         }
            
         $this->insert('product_description', $value);
      }
      
      
      //Product Options
      $this->delete('product_option', array('product_id'=>$product_id));
      $this->delete('product_option_value', array('product_id'=>$product_id));
      $this->delete('product_option_value_restriction', array('product_id'=>$product_id));
      
      if (isset($data['product_options'])) {
         foreach ($data['product_options'] as $product_option) {
            if($this->user->isDesigner()){
               $product_option['required'] = 1;
            }
            
            $product_option['product_id'] = $product_id;
            
            $product_option_id = $this->insert('product_option', $product_option);
         
            if (isset($product_option['product_option_value'])) {
               foreach ($product_option['product_option_value'] as $product_option_value) {
                  if($this->user->isDesigner()){
                     $product_option_value['cost']   = 0;
                     $product_option_value['price']  = 0;
                     $product_option_value['points'] = 0;
                     $product_option_value['weight'] = 0;
                  }
                  
                  $product_option_value['product_id'] = $product_id;
                  $product_option_value['option_id'] = $product_option['option_id'];
                  $product_option_value['product_option_id'] = $product_option_id;
                  
                  $product_option_value_id = $this->insert('product_option_value', $product_option_value);
                  
                  if(isset($product_option_value['restrictions'])){
                     foreach($product_option_value['restrictions'] as $restriction){
                        $restriction['product_id']      = $product_id;
                        $restriction['option_value_id'] = $product_option_value['option_value_id'];
                        
                        $this->insert('product_option_value_restriction', $restriction);
                     }
                  }
               }
            }
         }
      }
      
      
      //Product Additional Images
      $this->delete('product_image', array('product_id'=>$product_id));
      
      if (isset($data['product_images'])) {
         foreach ($data['product_images'] as $product_image) {
            $product_image['product_id'] = $product_id;
            
            $this->insert('product_image', $product_image);
         }
      }
      
      
      if(!$this->user->isDesigner()){
            
         //Product Categories
         $this->delete('product_to_category', array('product_id'=>$product_id));
               
         if (isset($data['product_category'])) {
            foreach (array_unique($data['product_category']) as $category_id) {
               $values = array(
                  'product_id' => $product_id,
                  'category_id' => $category_id
                 );
               $this->insert('product_to_category',  $values);
            }
         }
         
         
         //Product Stores
		   $this->delete('product_to_store', array('product_id'=>$product_id));
   
   		if (isset($data['product_store'])) {
            foreach ($data['product_store'] as $store_id) {
               $values = array(
                  'store_id' => $store_id,
                  'product_id' => $product_id
                 );
               $this->insert('product_to_store', $values);
            }
         }
   	   
         
         //Product Attributes
   		$this->delete('product_attribute', array('product_id'=>$product_id));
   
   		if (isset($data['product_attributes'])) {
            foreach ($data['product_attributes'] as $product_attribute) {
               $product_attribute['product_id'] = $product_id;
               $product_attribute['language_id'] = $language_id;
               
               $this->insert('product_attribute', $product_attribute);
            }
         }
			
         //Product Discount
   		$this->delete('product_discount', array('product_id'=>$product_id));
    
   		if (isset($data['product_discounts'])) {
            foreach ($data['product_discounts'] as $product_discount) {
               $product_discount['product_id'] = $product_id;
               
               $this->insert('product_discount', $product_discount);
            }
         }
   		
         
         //Product Special
   		$this->delete('product_special', array('product_id'=>$product_id));
   		
   		if (isset($data['product_specials'])) {
            foreach ($data['product_specials'] as $product_special) {
               $product_special['product_id'] = $product_id;
               
               $this->insert('product_special', $product_special);
            }
         }
		   
         
         //Product Downloads
   		$this->delete('product_to_download',  array('product_id'=>$product_id));
   		
   		if (isset($data['product_download'])) {
            foreach ($data['product_download'] as $download_id) {
               $values = array(
                  'download_id' => $download_id,
                  'product_id' => $product_id
                 );
               $this->insert('product_to_download', $values);
            }
         }
         
         
         //Product Related
   		$this->delete('product_related',  array('product_id'=>$product_id));
   		$this->delete('product_related', array('related_id'=>$product_id));
   
   		if (isset($data['product_related'])) {
            foreach ($data['product_related'] as $related_id) {
               $values = array(
                  'product_id' => $product_id,
                  'related_id' => $related_id
                 );
                  
               $this->insert('product_related', $values);
               
               //the inverse so the other product is related to this product too!
               $values = array(
                  'product_id' => $related_id,
                  'related_id' => $product_id
                 );
                  
               $this->insert('product_related', $values);
            }
         }
   		
         
         //Product Reward
   		$this->delete('product_reward',  array('product_id'=>$product_id));
   
   		if (isset($data['product_reward'])) {
            foreach ($data['product_reward'] as $customer_group_id => $product_reward) {
               $product_reward['product_id'] = $product_id;
               $product_reward['customer_group_id'] = $customer_group_id;
               
               $this->insert('product_reward', $product_reward);
            }
         }
         
         
         //Product Layouts
   		$this->delete('product_to_layout',  array('product_id'=>$product_id));
   
   		if (isset($data['product_layout'])) {
            foreach ($data['product_layout'] as $store_id => $layout) {
               if ($layout['layout_id']) {
                  $layout['product_id'] = $product_id;
                  $layout['store_id'] = $store_id;
                  
                  
                  $this->insert('product_to_layout', $layout);
               }
            }
         }
   		
         //Product Templates
         $this->delete('product_template',  array('product_id'=>$product_id));
   
         if (isset($data['product_template'])) {
            foreach ($data['product_template'] as $store_id => $themes) {
               foreach($themes as $theme => $template){
                  if(empty($template['template'])) continue;
                  
                  $template['product_id'] = $product_id;
                  $template['theme'] = $theme;
                  $template['store_id'] = $store_id;
                  
                  $this->insert('product_template', $template); 
               }
            }
         }
         
         
         //Product Tags
   		$this->delete('product_tag',  array('product_id'=>$product_id));
   		
   		foreach ($data['product_tag'] as $language_id => $value) {
            if ($value) {
               $tags = explode(',', $value);
               
               foreach ($tags as $tag) {
                  $values = array(
                    'product_id' => $product_id,
                    'language_id' => $language_id,
                    'tag' => trim($tag)
                   );
                   
                  $this->insert('product_tag', $values);
               }
            }
         }
		}
	   
      
      //Product URL Alias
      $this->model_setting_url_alias->deleteUrlAliasByRouteQuery('product/product', 'product_id=' . (int)$product_id);
      
		if ($data['keyword']) {
         if(!preg_match("/^product\//",$data['keyword'])){
            $data['keyword'] = 'product/' . $data['keyword']; 
         }
         
         $url_alias = array(
            'route'=>'product/product',
            'query'=>'product_id=' . (int)$product_id,
            'keyword'=>$this->db->escape($data['keyword']),
            'status'=>$data['status'],
           );
         
         $this->model_setting_url_alias->addUrlAlias($url_alias);
      }
      
		$this->cache->delete('product');
	}
	
	public function generate_url($product_id, $name){
      $url = 'product/'.$this->model_setting_url_alias->format_url($name);
      $orig = $url;
      $count = 2;
      
      $url_alias = $product_id?$this->model_setting_url_alias->getUrlAliasByRouteQuery('product/product', "product_id=$product_id"):null;
      
      $test = $this->model_setting_url_alias->getUrlAliasByKeyword($url);
      while(!empty($test) && $test['url_alias_id'] != $url_alias['url_alias_id']){
         $url = $orig . '-' . $count++;
         $test = $this->model_setting_url_alias->getUrlAliasByKeyword($url);
      }
      return $url;
   }
   
   public function generate_model($name){
      $model = strtoupper($this->model_setting_url_alias->format_url($name));
      $orig = $model;
      $count = 2;
      $test = $this->query("SELECT COUNT(*) as count FROM " . DB_PREFIX ."product WHERE model='$model'");
      while($test->row['count']){
         $model = $orig . '-' . $count++;
         $test = $this->query("SELECT COUNT(*) as count FROM " . DB_PREFIX ."product WHERE model='$model'");
      }
      return $model;
   }
   
	public function copyProduct($product_id) {
		$query = $this->query("SELECT DISTINCT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		
		if ($query->num_rows) {
			$data = array();
			
			$data = $query->row;
			
			$data['keyword'] = '';

			$data['status'] = 0;
						
			$data['product_attribute'] = $this->getProductAttributes($product_id);
			$data['product_description'] = $this->getProductDescriptions($product_id);			
			$data['product_discount'] = $this->getProductDiscounts($product_id);
			$data['product_image'] = $this->getProductImages($product_id);		
			$data['product_option'] = $this->getProductOptions($product_id);
			$data['product_related'] = $this->getProductRelated($product_id);
			$data['product_reward'] = $this->getProductRewards($product_id);
			$data['product_special'] = $this->getProductSpecials($product_id);
			$data['product_tag'] = $this->getProductTags($product_id);
			$data['product_category'] = $this->getProductCategories($product_id);
			$data['product_download'] = $this->getProductDownloads($product_id);
			$data['product_layout'] = $this->getProductLayouts($product_id);
         $data['product_template'] = $this->getProductTemplates($product_id);
			$data['product_store'] = $this->getProductStores($product_id);
			
			$this->addProduct($data);
         
         return true;
		}

      return false;
	}
	
	public function deleteProduct($product_id) {
		$this->delete('product', array('product_id'=>$product_id));
		$this->delete('product_attribute', array('product_id'=>$product_id));
		$this->delete('product_description', array('product_id'=>$product_id));
		$this->delete('product_discount', array('product_id'=>$product_id));
		$this->delete('product_image', array('product_id'=>$product_id));
		$this->delete('product_option', array('product_id'=>$product_id));
		$this->delete('product_option_value', array('product_id'=>$product_id));
      $this->delete('product_option_value_restriction', array('product_id'=>$product_id));
		$this->delete('product_related', array('product_id'=>$product_id));
		$this->delete('product_related', array('related_id'=>$product_id));
		$this->delete('product_reward', array('product_id'=>$product_id));
		$this->delete('product_special', array('product_id'=>$product_id));
		$this->delete('product_tag', array('product_id'=>$product_id));
		$this->delete('product_to_category', array('product_id'=>$product_id));
		$this->delete('product_to_download', array('product_id'=>$product_id));
		$this->delete('product_to_layout', array('product_id'=>$product_id));
      $this->delete('product_template', array('product_id'=>$product_id));
		$this->delete('product_to_store', array('product_id'=>$product_id));
		$this->delete('review', array('product_id'=>$product_id));
		
      $this->model_setting_url_alias->deleteUrlAliasByRouteQuery('product/product', 'product_id=' . (int)$product_id);
		
		$this->cache->delete('product');
	}
	
	public function getProduct($product_id) {
		$query = $this->query("SELECT DISTINCT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
      
      if($query->num_rows){
         $url_alias = $this->model_setting_url_alias->getUrlAliasByRouteQuery('product/product', "product_id=" . (int)$product_id);
         $query->row['keyword'] = $url_alias ? $url_alias['keyword']:'';
      }
         
		return $query->row;
	}
	
	public function getProducts($data = array(), $select = null) {
	   $lang_id = (int)$this->config->get('config_language_id');
      
      if(!$data){
         $products = $this->cache->get('product.' . $lang_id);
         
         if($products){
            return $products;
         }
      }
      
      if(!$select){
		   $select = "pd.*, p.*";
      }
      
      //JOIN Required tables
      $tables = array();
      
      $tables["LEFT JOIN"]["product_description pd"] = "p.product_id = pd.product_id AND pd.language_id = '$lang_id'";

		if (isset($data['filter_category_id']) && !empty($data['filter_category_id'])) {
		   $tables["LEFT JOIN"]['product_to_category p2c'] = "p.product_id = p2c.product_id";
		}
		
      if(isset($data['sort']) && $data['sort'] == 'm.name'){
         $tables["LEFT JOIN"]['manufacturer m'] = 'p.manufacturer_id = m.manufacturer_id';
      }
      
      $where = array();
		
		if (!empty($data['filter_name'])) {
			$where["AND"][] = "LCASE(pd.name) LIKE '%" . $this->db->escape(strtolower($data['filter_name'])) . "%'";
		}

		if (!empty($data['filter_model'])) {
			$where["AND"][] = "LCASE(p.model) LIKE '%" . $this->db->escape(strtolower($data['filter_model'])) . "%'";
		}
		if (!empty($data['filter_manufacturer_id'])) {
		   if(is_array($data['filter_manufacturer_id'])){
            $where["AND"][] = "p.manufacturer_id IN (" . implode(',',$data['filter_manufacturer_id']) . ")";
         }
         else{
            $where["AND"][] = "p.manufacturer_id ='" . (int)$data['filter_manufacturer_id'] . "'";
         }
      }
      
		if (!empty($data['filter_price']['low'])) {
			$where["AND"][] = "p.price >= '" . (int)$data['filter_price'] . "'";
		}
      
      if (!empty($data['filter_price']['high'])) {
         $where["AND"][] = "p.price <= '" . (int)$data['filter_price'] . "'";
      }
      
      if (!empty($data['filter_cost']['low'])) {
         $where["AND"][] = "p.cost >= '" . (int)$data['filter_cost'] . "'";
      }
      
      if (!empty($data['filter_cost']['high'])) {
         $where["AND"][] = "p.cost <= '" . (int)$data['filter_cost'] . "'";
      }
      
      if (isset($data['filter_is_final'])) {
         $where["AND"][] = "p.is_final = '" . (int)$data['filter_is_final'] . "'";
      }
		
		if (!empty($data['filter_date_expires']['start'])) {
         $where["AND"][] = "p.date_expires > '" . $this->db->escape($data['filter_date_expires']['start']) . "'";
      }
      
      if (!empty($data['filter_date_expires']['end'])) {
         $where["AND"][] = "p.date_expires < '" . $this->db->escape($data['filter_date_expires']['end']) . "'";
      }
		
		if (isset($data['filter_quantity'])) {
			$where["AND"][] = "p.quantity = '" . (int)$data['filter_quantity'] . "'";
		}
		
      if (isset($data['filter_editable'])) {
         $where["AND"][] = "p.editable = '" . (int)$data['filter_editable'] . "'";
      }
      
		if (isset($data['filter_status'])) {
			$where["AND"][] = "p.status = '" . (int)$data['filter_status'] . "'";
		}
		
		//TODO: Optimize this so we pull p2c.category_id IN (SELECT category_id ...) for sub categories
		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				   
				$implode_data = array((int)$data['filter_category_id']);
				
				$categories = $this->model_catalog_category->getCategories($data['filter_category_id']);
				
				foreach ($categories as $category) {
					$implode_data[] = (int)$category['category_id'];
				}
				
				$where["AND"][] = "p2c.category_id IN (" . implode(',', $implode_data) . ")";			
			} else {
				$where["AND"][] = "p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
			}
		}
		
      $query = $this->execute('product p', $select, $tables, $where, $data);
	   
	   if(!$data && $query->num_rows > 2){
         $this->cache->set('product.' . $lang_id, $query->rows);
      }
      
		return $query->rows;
	}
	
   public function isEditable($product_id){
      $query = $this->query("SELECT editable FROM " . DB_PREFIX . "product WHERE product_id='$product_id'");
      return (int)$query->row['editable'] == 1;
   }

   public function updateProductCategory($product_id, $op, $category_id){
      $this->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id='$product_id' AND category_id='$category_id'");
      if($op == 'add')
         $this->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id='$product_id', category_id='$category_id'");
   }
   
   public function updateProductValue($product_id, $name, $value){
      $this->query("UPDATE " . DB_PREFIX . "product SET `$name`='$value' WHERE product_id='$product_id'");
      if($name == 'status'){
         $url_alias = $this->model_setting_url_alias->getUrlAliasByRouteQuery('product/product', "product_id=$product_id");
         if(!empty($url_alias)){
            $this->model_setting_url_alias->editUrlAlias($url_alias['url_alias_id'],array('status'=>$value));
         }
      }
         
      $this->cache->delete('product');
   }
   
   public function updateProductDescriptions($product_id, $name, $value){
      //overwrites all languages!
      $this->query("UPDATE " . DB_PREFIX . "product_description SET `$name`='" . $this->db->escape($value) . "' WHERE product_id='$product_id'");
      $this->cache->delete('product');
   }
   
   
   public function getProductFull($product_id) {
      $product_id = (int)$product_id;
      
      $lang_id = (int)$this->config->get('config_language_id');
      
      $discount = "(SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.quantity >= '0' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount";
      $special = "(SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ((ps.date_start = '" . DATETIME_ZERO . "' OR ps.date_start < NOW()) AND (ps.date_end = '". DATETIME_ZERO . "' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special";

      $fs_table = "(SELECT ops.flashsale_id, ops.product_id, ops.date_end, ops.price as special FROM 
                        (SELECT fp.flashsale_id, fp.product_id, ps.product_special_id, MIN(ps.price) as special FROM " . DB_PREFIX . "flashsale_product fp 
                         LEFT JOIN " . DB_PREFIX . "product_special ps ON (fp.product_id=ps.product_id) WHERE ps.date_start < NOW() AND ps.date_end > NOW() GROUP BY flashsale_id, product_id) as low_price
                         INNER JOIN " . DB_PREFIX ."product_special ops ON(low_price.flashsale_id=ops.flashsale_id AND ops.product_special_id=low_price.product_special_id AND low_price.product_id=ops.product_id AND low_price.special=ops.price) GROUP BY product_id
                        ) as fs_table ON(fs_table.product_id=p.product_id)";
      
      $category = DB_PREFIX . "product_to_category p2c ON (p2c.product_id=p.product_id)";
      $description = DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id AND pd.language_id='$lang_id')";
      
      $query = $this->query("SELECT DISTINCT p.*, p2c.category_id, pd.*, $special, $discount, fs_table.flashsale_id,fs_table.date_end, pd.name AS name, p.image, m.name AS manufacturer, m.keyword, m.status as manufacturer_status, p.sort_order " . 
                                 "FROM " . DB_PREFIX . "product p LEFT JOIN $category LEFT JOIN $description LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) LEFT JOIN $fs_table WHERE p.product_id='$product_id' AND p.status = '1' AND m.status='1' AND p.date_available <= NOW() LIMIT 1");
      
      if ($query->num_rows) {
         $query->row['price'] = $query->row['discount'] ? $query->row['discount'] : $query->row['price'];
      }
      
      return $query->row;
   }
   
   public function getProductNames($product_ids){
      $query = $this->query("SElECT p.product_id, pd.name FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON(p.product_id=pd.product_id) WHERE p.product_id IN(" . implode(',', $product_ids) . ")");
   
      return $query->rows;
   }
	
	public function getProductDescriptions($product_id) {
		$product_description_data = array();
		
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");
		
		foreach ($query->rows as $result) {
			$product_description_data[$result['language_id']] = $result;
		}
		
		return $product_description_data;
	}

	public function getProductAttributes($product_id) {
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id) WHERE pa.product_id = '" . (int)$product_id . "' AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY pa.attribute_id");
		
		return $query->rows;
	}
	
	public function getProductOptions($product_id) {
		   
		$query = $this->query("SELECT *, po.sort_order FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY o.sort_order");
		
      $restrict_list = $this->getProductOptionValueRestrictions($product_id);
      
      $restrictions = array();
      
      foreach($restrict_list as $value){
         $restrictions[$value['option_value_id']][] = $value;
      }
      
		foreach ($query->rows as &$product_option) {
				
				$pov_query = $this->query("SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_id = '" . (int)$product_option['product_option_id'] . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY ov.sort_order");
		      
            foreach($pov_query->rows as &$pov){
               if(isset($restrictions[$pov['option_value_id']])){
                  $pov['restrictions'] = $restrictions[$pov['option_value_id']];
               }
            }
            
            $product_option['product_option_value'] = $pov_query->rows;
		}	
		
		return $query->rows;
	}
	
   public function getProductOptionValueRestrictions($product_id){
      $language_id = $this->config->get('config_language_id');
      
      $query = $this->query("SELECT * FROM " . DB_PREFIX . "product_option_value_restriction WHERE product_id='" . (int)$product_id . "'");
      
      return $query->rows;
   }
   
	public function getProductImages($product_id) {
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "' ORDER BY sort_order");
		
		return $query->rows;
	}
	
	public function getProductDiscounts($product_id) {
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' ORDER BY quantity, priority, price");
		
		return $query->rows;
	}
	
	public function getProductSpecials($product_id) {
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' ORDER BY priority, price");
		
		return $query->rows;
	}
	
	public function getProductRewards($product_id) {
		$product_reward_data = array();
		
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");
		
		foreach ($query->rows as $result) {
			$product_reward_data[$result['customer_group_id']] = array('points' => $result['points']);
		}
		
		return $product_reward_data;
	}
		
	public function getProductDownloads($product_id) {
		$product_download_data = array();
		
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");
		
		foreach ($query->rows as $result) {
			$product_download_data[] = $result['download_id'];
		}
		
		return $product_download_data;
	}

	public function getProductStores($product_id) {
		$product_store_data = array();
		
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_store_data[] = $result['store_id'];
		}
		
		return $product_store_data;
	}

	public function getProductLayouts($product_id) {
		$product_layout_data = array();
		
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");
		
		foreach ($query->rows as $result) {
			$product_layout_data[$result['store_id']] = $result['layout_id'];
		}
		
		return $product_layout_data;
	}
   
   public function getProductTemplates($product_id) {
      $query = $this->query("SELECT * FROM " . DB_PREFIX . "product_template WHERE product_id = '" . (int)$product_id . "'");
      
      $template_data = array();
     
      foreach ($query->rows as $result) {
         $template_data[$result['store_id']][$result['theme']] = $result;
      }
      
      return $template_data;
   }
		
	public function getProductCategories($product_id) {
		$product_category_data = array();
		
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
		
		foreach ($query->rows as $result) {
			$product_category_data[] = $result['category_id'];
		}

		return $product_category_data;
	}

	public function getProductRelated($product_id) {
		$product_related_data = array();
		
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
		
		foreach ($query->rows as $result) {
			$product_related_data[] = $result['related_id'];
		}
		
		return $product_related_data;
	}
	
	public function getProductTags($product_id) {
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "product_tag WHERE product_id = '" . (int)$product_id . "'");
		
		$tag_data = array();
		
		foreach ($query->rows as $result) {
			$tag_data[$result['language_id']][] = $result['tag'];
		}
		
      $product_tag_data = array();
      
		foreach ($tag_data as $language_id => $tags) {
			$product_tag_data[$language_id] = implode(',', $tags);
		}
		
		return $product_tag_data;
	}
	
	public function getTotalProducts($data = array()) {
	   unset($data['limit']);
      unset($data['sort']);
      unset($data['order']);
      
	   $query = $this->getProducts($data, 'COUNT(*) as total');
      
      return isset($query[0]['total']) ? $query[0]['total'] : 0;
	}	
	
	public function getTotalProductsByTaxClassId($tax_class_id) {
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE tax_class_id = '" . (int)$tax_class_id . "'");

		return $query->row['total'];
	}
		
	public function getTotalProductsByStockStatusId($stock_status_id) {
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE stock_status_id = '" . (int)$stock_status_id . "'");

		return $query->row['total'];
	}
	
	public function getTotalProductsByWeightClassId($weight_class_id) {
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE weight_class_id = '" . (int)$weight_class_id . "'");

		return $query->row['total'];
	}
	
	public function getTotalProductsByLengthClassId($length_class_id) {
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE length_class_id = '" . (int)$length_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByDownloadId($download_id) {
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_to_download WHERE download_id = '" . (int)$download_id . "'");
		
		return $query->row['total'];
	}
	
	public function getTotalProductsByManufacturerId($manufacturer_id) {
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		return $query->row['total'];
	}
	
	public function getTotalProductsByAttributeId($attribute_id) {
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_attribute WHERE attribute_id = '" . (int)$attribute_id . "'");

		return $query->row['total'];
	}	
	
	public function getTotalProductsByOptionId($option_id) {
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_option WHERE option_id = '" . (int)$option_id . "'");

		return $query->row['total'];
	}	
	
	public function getTotalProductsByLayoutId($layout_id) {
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_to_layout WHERE layout_id = '" . (int)$layout_id . "'");

		return $query->row['total'];
	}			
}