<?php 
class ControllerCatalogProduct extends Controller {
	 
     
  	public function index() {
		$this->load->language('catalog/product');
    	
		$this->document->setTitle($this->_('heading_title')); 
		
		$this->getList();
  	}
  
  	public function insert() {
    	$this->load->language('catalog/product');

    	$this->document->setTitle($this->_('heading_title')); 
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_product->addProduct($_POST);
	  		
         if(!$this->message->error_set()){
			   $this->message->add('success',$this->_('text_success'));
         }
         else{
            'insert failed';
            exit;
         }
	  
			$this->getList();
         return;
    	}
	
    	$this->getForm();
  	}

  	public function update() {
    	$this->load->language('catalog/product');

    	$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_product->editProduct($_GET['product_id'], $_POST);
			
			if(!$this->message->error_set()){
            $this->message->add('success',$this->_('text_success'));
         }
			
         $this->getList();
         return;
		}

    	$this->getForm();
  	}

  	public function delete() {
  	   
    	$this->load->language('catalog/product');

    	$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_POST['selected']) && $this->validateDelete()) {
			foreach ($_POST['selected'] as $product_id) {
				$this->model_catalog_product->deleteProduct($product_id);
	  		}

			if(!$this->message->error_set()){
            $this->message->add('success',$this->_('text_success'));
         }
		}

    	$this->getList();
  	}

  	public function copy() {
    	$this->load->language('catalog/product');

    	$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_POST['selected']) && $this->validateCopy()) {
			foreach ($_POST['selected'] as $product_id) {
				$this->model_catalog_product->copyProduct($product_id);
	  		}

			if(!$this->message->error_set()){
            $this->message->add('success',$this->_('text_success'));
         }
		}

    	$this->getList();
 	}
	
   public function list_update() {
      $this->load->language('catalog/product');

      $this->document->setTitle($this->_('heading_title'));
      
      if (isset($_POST['selected']) && isset($_GET['action']) && $this->validateCopy()) {
         foreach ($_POST['selected'] as $product_id) {
            switch($_GET['action']){
               case 'enable':
                  $this->model_catalog_product->updateProductValue($product_id, 'status',1);
                  break;
               case 'disable':
                  $this->model_catalog_product->updateProductValue($product_id, 'status',0);
                  break;
               case 'date_expires':
                  $this->model_catalog_product->updateProductValue($product_id,'date_expires',$_GET['action_value']);
                  break;
               case 'is_final':
                  $this->model_catalog_product->updateProductValue($product_id,'is_final',$_GET['action_value']);
                  break;
               case 'add_cat':
                  $this->model_catalog_product->updateProductCategory($product_id, 'add',$_GET['action_value']);
                  break;
               case 'remove_cat':
                  $this->model_catalog_product->updateProductCategory($product_id, 'remove',$_GET['action_value']);
                  break;
               case 'editable':
                  $this->model_catalog_product->updateProductValue($product_id,'editable',$_GET['action_value']);
                  break;
               case 'ship_policy':
                  $this->model_catalog_product->updateProductDescriptions($product_id,'shipping_return', $_GET['action_value']);
                  break;
               default:
                  $this->error['warning'] = "Invalid Action Selected!";
                  break;
            }
            if($this->error)
               break;
         }
         if(!$this->error){
            if(!$this->message->error_set()){
               $this->message->add('success',$this->_('text_success'));
            }
         }
      }

      $this->getList();
   }
   
  	private function getList() {
		$this->template->load('catalog/product_list');

  	   $filters = array(
  	      'filter_name'=>null,'filter_model'=>null,'filter_price'=>null,'filter_cost'=>null,'filter_quantity'=>null,
         'filter_manufacturer_id'=>null,'filter_category_id'=>null,'filter_is_final'=>null,'filter_date_expires'=>null,
         'filter_status'=>null,'filter_editable'=>null,'sort'=>'pd.name','order'=>'ASC','page'=>1
      );

      
      foreach($filters as $f=>$default){
         $data[$f] = $this->data[$f] = $$f = isset($_GET[$f])?$_GET[$f]:$default;
      }

	   $this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
      $this->breadcrumb->add($this->_('heading_title'), $this->url->link('catalog/product'));
  		
      $categories = array();
      foreach($this->model_catalog_category->getCategories(null) as $cat){
         $categories[$cat['category_id']] = $cat['name'];
		}
      
		
		$url = $this->get_url();
		
      //Batch actions
      $this->data['batch_actions'] = array('enable'=>'Enable','disable'=>'Disable', 'date_expires'=>'Product Expiration Date','is_final'=>"Final Sale",
                                            'add_cat'=>'Add Category', 'remove_cat'=>"Remove Category", 'editable'=>'Allow Designer Edits',
                                            'ship_policy'=>"Update Shipping / Return Policy"
                                           );
      $this->data['batch_action_values'] = array('date_expires'=>array('#type'=>'text','#default'=>DATETIME_ZERO,'#attrs'=>array('class'=>'datetime')),
                                                  'is_final'=>array('#type'=>'select','#default'=>1, '#values'=>$this->_('yes_no')),
                                                  'add_cat'=>array('#type'=>'select','#values'=>$categories),
                                                  'remove_cat'=>array('#type'=>'select','#values'=>$categories),
                                                  'editable'=>array('#type'=>'select','#default'=>1, '#values'=>$this->_('yes_no')),
                                                  'ship_policy'=>array('#type'=>'ckedit', '#default'=>$this->_('shipping_return_policy'))
                                                 );
      $this->data['batch_action_go'] = $this->url->link('catalog/product/list_update', $url);
      
      
      //Action Buttons
		$this->data['insert'] = $this->url->link('catalog/product/insert', $url);
		$this->data['copy'] = $this->url->link('catalog/product/copy', $url);	
		$this->data['delete'] = $this->url->link('catalog/product/delete', $url);
    	
		$this->data['products'] = array();

      $data['start']= ($page - 1) * $this->config->get('config_admin_limit');
		$data['limit'] = $this->config->get('config_admin_limit');
		
		$product_total = $this->model_catalog_product->getTotalProducts($data);
		$results = $this->model_catalog_product->getProducts($data);
      
		foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->_('text_edit'),
				'href' => $this->url->link('catalog/product/update', 'product_id=' . $result['product_id'] . $url)
			);
			
			if ($result['image'] && file_exists(DIR_IMAGE . $result['image'])) {
				$image = $this->image->resize($result['image'], 40, 40);
			} else {
				$image = $this->image->resize('no_image.jpg', 40, 40);
			}
         
         $categories = $this->model_catalog_product->getProductCategories($result['product_id']);
         
			$special = false;
			$product_specials = $this->model_catalog_product->getProductSpecials($result['product_id']);
			
			foreach ($product_specials  as $product_special) {
				if (($product_special['date_start'] == DATETIME_ZERO || $product_special['date_start'] < date('Y-m-d H:i:s')) && ($product_special['date_end'] == DATETIME_ZERO || $product_special['date_end'] > date('Y-m-d H:i:s'))) {
					$special = $product_special['price'];
			
					break;
				}					
			}
         
      		$this->data['products'][] = array(
				'product_id' => $result['product_id'],
				'name'       => $result['name'],
				'model'      => $result['model'],
				'price'      => $result['price'],
				'cost'      => $result['cost'],
				'special'    => $special,
				'image'      => $image,
				'categories' => $categories,
				'manufacturer_id' => $result['manufacturer_id']?$result['manufacturer_id']:'',
				'quantity'   => $result['quantity'],
				'is_final'   => $result['is_final'],
				'date_expires'   => $result['date_expires'] == DATETIME_ZERO?"No Expiration":$result['date_expires'],
				'editable'   => ($result['editable']?"Yes":"No"),
				'status'     => ($result['status'] ? $this->_('text_enabled') : $this->_('text_disabled')),
				'selected'   => isset($_POST['selected']) && in_array($result['product_id'], $_POST['selected']),
				'action'     => $action
			);
    	}
		
	   $this->data['category_list'] = array(''=>'');
      foreach($this->model_catalog_category->getCategories(null) as $cat){
         $this->data['category_list'][$cat['category_id']] = $cat['name'];
      }
      
      $this->data['manufacturer_list'] = array(''=>'');
      
      $m_data = array(
         'sort' => 'name',
      );
      
      $manufacturers = $this->model_catalog_manufacturer->getManufacturers($m_data);
      
      foreach($manufacturers as $manufacturer){
         $this->data['manufacturer_list'][$manufacturer['manufacturer_id']] = $manufacturer['name'];
      }

		$url = $this->get_url(true);
		
      $sort_by = array(
         'sort_name'=>'pd.name','sort_model'=>'p.model','sort_price'=>'p.price','sort_cost'=>'p.cost','sort_is_final'=>'p.is_final',
         'sort_manufacturer'=>'m.name','sort_category'=>'c.name','sort_date_expires'=>'p.date_expires','sort_quantity'=>'p.quantity','sort_editable'=>'p.editable',
         'sort_status'=>'p.status','sort_order'=>'p.sort_order'
        );
        
      foreach($sort_by as $s=>$q)
         $this->data[$s] = $this->url->link('catalog/product', "sort=$q" . $url);
		
		$url = $this->get_url();
	   
		$this->pagination->init();
		$this->pagination->total = $product_total;
		$this->pagination->page = $page;
		$this->pagination->limit = $this->config->get('config_admin_limit');
		$this->pagination->text = $this->_('text_pagination');
		$this->pagination->url = $this->url->link('catalog/product', $url . '&page={page}');
			
		$this->data['pagination'] = $this->pagination->render();
	   
         
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
  	}

  	private function getForm() {
      $this->template->load('catalog/product_form');

  	   $product_id = $this->data['product_id'] = isset($_GET['product_id'])?$_GET['product_id']:false;
      
      $this->document->addScript("image_manager.js");
		
      $url = $this->get_url();
      
      $this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
      $this->breadcrumb->add($this->_('heading_title'), $this->url->link('catalog/product'));
      									
		if (!$product_id) {
			$this->data['action'] = $this->url->link('catalog/product/insert', $url);
		} else {
			$this->data['action'] = $this->url->link('catalog/product/update', 'product_id=' . $product_id . $url);
		}
		
		$this->data['cancel'] = $this->url->link('catalog/product', $url);

		if ($product_id && ($_SERVER['REQUEST_METHOD'] != 'POST')) {
      	$product_info = $this->model_catalog_product->getProduct($product_id);
    	}
      
       $default_english = array('shipping_return'=>$this->_('shipping_return_policy'), 'description'=>'','meta_description'=>'','meta_keyword'=>'','name'=>'','blurb'=>'');
       
       $defaults = array(
          'model'=>'',
          'sku'=>'',
          'upc'=>'',
          'location'=>'',
          'keyword'=>'',
          'product_store'=>array(0,1,2),
          'product_description'=>array(1=>$default_english),
          'product_tag'=>array(),
          'image'=>'',
          'manufacturer_id'=>0,
          'shipping'=>1,
          'price'=>'',
          'cost'=>'',
          'is_final'=>0,
          'tax_class_id'=>$this->config->get('config_tax_default_id'),
          'date_available'=>date_format(new DateTime(),"Y-m-d H:i:s"),
          'date_expires'=>'',
          'editable' => 1,
          'quantity'=>1,
          'minimum'=>1,
          'subtract'=>1,
          'sort_order'=>1,
          'stock_status_id'=>$this->config->get('config_stock_status_id'),
          'status'=>1,
          'weight'=>'',
          'weight_class_id'=>$this->config->get('config_weight_class_id'),
          'length'=>'',
          'width'=>'',
          'height'=>'',
          'length_class_id'=>$this->config->get('config_length_class_id'),
          'product_options'=>array(),
          'product_discounts'=>array(),
          'product_specials'=>array(),
          'product_attributes'=>array(), //changed product_attribute -> product_attributes for the POST in the template
          'product_images'=>array(), //changed product_image -> product_images for the POST in the template
          'product_download'=>array(),
          'product_category'=>array(),
          'product_related'=>array(),
          'points'=>'',
          'product_reward'=>array(),
          'product_layout'=>array(),
          'product_template' => array(),
         );

      foreach($defaults as $d=>$default){
         if (isset($_POST[$d]))
            $this->data[$d] = $_POST[$d];
         elseif (isset($product_info[$d]))
            $this->data[$d] = $product_info[$d];
         elseif(!$product_id)
            $this->data[$d] = $default;
      }
      
      if(isset($this->data['keyword'])){
		   $this->data['keyword'] = preg_replace("/^[\/]?product[\/]?/i","",$this->data['keyword']);
      }
      else{
         $this->data['keyword'] = '';
      }
      
		if(!isset($this->data['product_description'])){
			$this->data['product_description'] = $this->model_catalog_product->getProductDescriptions($product_id);
      }
		
      if(!isset($this->data['product_store'])){
         $this->data['product_store'] = $this->model_catalog_product->getProductStores($product_id);
      }
      
		if(!isset($this->data['product_tag'])){
         $this->data['product_tag'] = $this->model_catalog_product->getProductTags($product_id);
      }
      
      if(!isset($this->data['date_available'])){
         $this->data['date_available'] = date('Y-m-d', strtotime($product_info['date_available']));
      }
      
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		
      
		$thumb = ($this->data['image'] && file_exists(DIR_IMAGE . $this->data['image']))?$this->data['image']:'no_image.jpg'; 
	   $this->data['thumb'] = $this->image->resize($thumb, 100, 100);
		
      $this->data['manufacturers'] = array(0=>$this->_('text_none'));
      
		$m_data = array(
			'sort' => 'name'
		);
		
		$manufacturers = $this->model_catalog_manufacturer->getManufacturers($m_data);
		
      foreach($manufacturers as $man){
         $this->data['manufacturers'][$man['manufacturer_id']] = $man['name'];
      }
      
		$this->data['tax_classes'] = array_merge(array(0=>'--- None ---'),$this->model_localisation_tax_class->getTaxClasses());
    
		$this->data['stock_statuses'] = $this->model_localisation_stock_status->getStockStatuses();
    	
		
		$this->data['weight_classes'] = $this->model_localisation_weight_class->getWeightClasses();
    	
		
		$this->data['length_classes'] = $this->model_localisation_length_class->getLengthClasses();
    	
      //Get Product Attributes
		if (!isset($this->data['product_attributes'])){
			$this->data['product_attributes'] = $this->model_catalog_product->getProductAttributes($product_id);
      }
		
      
      /**
       * NOTE to clarify options / product options:
       * 
       * An option contains a set of 1 or more option_values
       * 
       * A product_option is associated to a product and contains product_option_values (a subset option_values from an option),
       * A product_option_value has a reference to an option_value and contains additional data.
       *  
       */
      
      //Get Product Options with product_option_values
      if(!isset($this->data['product_options'])){
			$product_options = $this->model_catalog_product->getProductOptions($product_id);
         
         $this->data['product_options'] = array();
         
         foreach($product_options as $product_option){
            $this->data['product_options'][$product_option['product_option_id']] = $product_option;
            
            $product_option_value_data = array();
            
            foreach($product_option['product_option_value'] as $product_option_value){
               $pov_id = $product_option_value['product_option_value_id'];
               
               $product_option_value_data[$pov_id] = $product_option_value;
               
               //Add restrictions to the product_option_value
               if(isset($restrictions[$pov_id])){
                  $product_option_value_data[$pov_id]['restrictions'] = $restrictions[$pov_id];
               }
            }
            
            $this->data['product_options'][$product_option['product_option_id']]['product_option_value'] = $product_option_value_data;
         }
		}
      
      //Ensure that the index 'product_option_value' is set for each option
      foreach($this->data['product_options'] as &$po){
         if(!isset($po['product_option_value'])){
            $po['product_option_value'] = array();
         }
      }
      
      //Get All option values currently assigned to this product in flat list (for option restrictions)
      $this->data['all_product_option_values'] = array();
      
      foreach($this->data['product_options'] as $key=>$product_option){
         foreach($product_option['product_option_value'] as $product_option_value){
            $this->data['all_product_option_values'][$product_option_value['option_value_id']] = $product_option_value;
         }
      }
      
      //Get Set of Option Values
      $this->data['option_values'] = array();
      
      foreach ($this->data['product_options'] as $product_option) {
         if (!isset($this->data['option_values'][$product_option['option_id']])) {
            $this->data['option_values'][$product_option['option_id']] = $this->model_catalog_option->getOptionValues($product_option['option_id']);
         }
      }
      
      //Find set of option values that are not associated to a product_option
      $this->data['unused_option_values'] = $this->data['option_values'];
      
      foreach($this->data['unused_option_values'] as $option_id=>$option_value){
         foreach($this->data['product_options'] as $product_option){
            if($product_option['option_id'] != $option_id)continue;
            foreach($product_option['product_option_value'] as $product_option_value){
               foreach($option_value as $key=>$ov){
                  if($product_option_value['option_value_id'] == $ov['option_value_id']){
                     unset($this->data['unused_option_values'][$option_id][$key]);
                  }
               }
            }
         }
      }
      
      $this->language->format('text_option_help', $this->config->get('config_email'));
      $this->language->format('text_not_editable', $this->data['product_description'][$this->config->get('config_language_id')]['name'], $this->config->get('config_email'));
      
      $this->data['customer_groups'] = $this->model_sale_customer_group->getCustomerGroups();
		
		if (!isset($this->data['product_discounts'])){
			$this->data['product_discounts'] = $this->model_catalog_product->getProductDiscounts($product_id);
      }

		if (!isset($this->data['product_specials'])){
			$this->data['product_specials'] = $this->model_catalog_product->getProductSpecials($product_id);
      }
		
		if (!isset($this->data['product_images'])){
			$this->data['product_images'] = $this->model_catalog_product->getProductImages($product_id);
      }
		
		foreach ($this->data['product_images'] as &$product_image) {
			if (!$product_image['image'] || !file_exists(DIR_IMAGE . $product_image['image'])) {
				$product_image['image'] = 'no_image.jpg';
			}
         
         $product_image['thumb'] = $this->image->resize($product_image['image'], 100, 100);
		}
      
		$this->data['no_image'] = $this->image->resize('no_image.jpg', 100, 100);

		$this->data['data_downloads'] = $this->model_catalog_download->getDownloads();
		
		if (!isset($this->data['product_download'])){
			$this->data['product_download'] = $this->model_catalog_product->getProductDownloads($product_id);
      }
		
		$this->data['data_categories'] = $this->model_catalog_category->getCategories(0);
		
		if (!isset($this->data['product_category'])){
			$this->data['product_category'] = $this->model_catalog_product->getProductCategories($product_id);
      }
		
		if (!isset($this->data['product_related'])){
			$products = $this->model_catalog_product->getProductRelated($product_id);
   		$this->data['product_related'] = array();
		
   		foreach ($products as $product_id) {
   			$related_info = $this->model_catalog_product->getProduct($product_id);
   			
   			if ($related_info) {
   				$this->data['product_related'][] = array(
   					'product_id' => $related_info['product_id'],
   					'name'       => $related_info['name']
   				);
   			}
   		}
      }
						
		if (!isset($this->data['product_reward'])){
			$this->data['product_reward'] = $this->model_catalog_product->getProductRewards($product_id);
      }
      
      $this->data['data_stores'] = $this->model_setting_store->getStores();
		
      if (!isset($this->data['product_layout'])){
			$this->data['product_layout'] = $this->model_catalog_product->getProductLayouts($product_id);
      }
      
		$this->data['layouts'] = array(0 => '') +    $this->model_design_layout->getLayouts();
      
      if(!isset($this->data['product_template'])){
         $this->data['product_template'] = $this->model_catalog_product->getProductTemplates($product_id);
      }
      
      $this->data['templates'] = $this->model_design_template->getTemplatesFor('product', true);
      
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
  	} 
	
  	private function validateForm() {
  	   
    	if (!$this->user->hasPermission('modify', 'catalog/product')) {
   		$this->error['warning'] = $this->_('error_permission');
    	}

    	foreach ($_POST['product_description'] as $language_id => $value) {
   		if ((strlen($value['name']) < 1) || (strlen($value['name']) > 255)) {
     		  $this->error['product_description['.$language_id.'][name]'] = $this->_('error_name');
   		}
    	}
		
    	if ((strlen($_POST['model']) < 1) || (strlen($_POST['model']) > 64)) {
   		$this->error['model'] = $this->_('error_model');
    	}
      elseif(!isset($_GET['product_id'])){
         $dup = $this->db->query("SELECT COUNT(*) as count FROM " . DB_PREFIX . "product WHERE model='" . $this->db->escape($_POST['model']) . "'");
         if($dup->row['count'] > 0)
            $this->error['model'] = $this->_('error_dup_model');
      }
		
      if ((strlen($_POST['keyword']) < 1) || (strlen($_POST['keyword']) > 255)) {
            $this->error['keyword'] = $this->_('error_keyword');
      }
		
      if(isset($_POST['product_images'])){
         foreach($_POST['product_images']  as $key=>$image){
            if(strtolower($image['image']) == 'data/no_image.jpg' || !$image['image']){
               unset($_POST['product_images'][$key]);
            }
         }
      }
      
      $product_options = isset($_POST['product_options']) ? $_POST['product_options'] : false;
      //validate the quantities
      if($product_options){
         $po_quantity = array();
         
         foreach($product_options as $option_id=>$product_option){
            if(!isset($product_option['product_option_value'])){
               $this->error["option-value$option_id"] = $this->language->format('error_no_option_value', $product_option['name']);
               continue;
            }
            
            if(!$product_option['required']) continue;
            
            if((string)$po_quantity != 'INF'){
               $po_quantity[$option_id] = 0;
            }
            
            foreach($product_option['product_option_value'] as $option_value_id=>$product_option_value){
               if(!$product_option_value['subtract']){
                  $po_quantity = 'INF';
                  continue;
               }
               
               if((string)$po_quantity != 'INF'){
                  $po_quantity[$option_id] += (int)$product_option_value['quantity'];
               }
               
               if(isset($product_option_value['restrictions'])){
                  $restrict_quantity = 0;
                  
                  foreach($product_option_value['restrictions'] as $r_key=>$restriction){
                     $restrict_quantity += (int)$restriction['quantity'];
                  }
                  
                  if($restrict_quantity > (int)$product_option_value['quantity']){
                     $this->error["product_options[$option_id][product_option_value][$option_value_id][quantity]"] = $this->language->format('error_restrict_quantity', $product_option_value['quantity'], $restrict_quantity);
                  }
               }
            }
         }
         
         if($po_quantity != 'INF' && min($po_quantity) < (int)$_POST['quantity']){
            $this->error['quantity'] = $this->language->format('error_po_quantity', $_POST['quantity'], min($po_quantity));
         }
      }
      
      //validate the option restrictions
      if($product_options){
         foreach($product_options as $option_id=>$product_option){
            if(!isset($product_option['product_option_value'])){
               continue;
            }
            
            foreach($product_option['product_option_value'] as $option_value_id=>$product_option_value){
               if(isset($product_option_value['restrictions'])){
                  foreach($product_option_value['restrictions'] as $r_key=>$restriction){
                     if($restriction['restrict_option_value_id'] == $option_value_id){
                        $this->error["product_options[$option_id][product_option_value][$option_value_id][restrictions][$r_key][restrict_option_value_id]"] = $this->_('error_pov_restrict_same');
                     }
                     
                     foreach($product_option_value['restrictions'] as $r_key2=>$restriction2){
                        if($r_key != $r_key2){
                           if($restriction['restrict_option_value_id'] == $restriction2['restrict_option_value_id']){
                              $this->error["product_options[$option_id][product_option_value][$option_value_id][restrictions][$r_key][restrict_option_value_id]"] = $this->_('error_dup_restrict');
                           }
                        }
                     }
                     
                     if(isset($product_option['product_option_value'][$restriction['restrict_option_value_id']])){
                        $this->error["product_options[$option_id][product_option_value][$option_value_id][restrictions][$r_key][restrict_option_value_id]"] = sprintf($this->_('error_restrict_same_option_id'), ucfirst($product_option['type']));
                     }
                  }
               }
            }
         }
      }
      
    	return $this->error ? false : true;
  	}
	
  	private function validateDelete() {
    	if (!$this->user->hasPermission('modify', 'catalog/product') && !$this->user->hasPermission('access','user/user_permission')) {
      		$this->error['warning'] = $this->_('error_permission');  
    	}
      
		if (!$this->error) {
	  		return true;
		} else {
	  		return false;
		}
  	}
  	
  	private function validateCopy() {
    	if (!$this->user->hasPermission('modify', 'catalog/product')) {
   		$this->error['warning'] = $this->_('error_permission');  
    	}
		
		if (!$this->error) {
	  		return true;
		} else {
	  		return false;
		}
  	}
   
   public function select(){
      if(!isset($_POST['filter'])) return;
      
      $filter = $_POST['filter'];
      $select = isset($_POST['select']) ? $_POST['select'] : array();
      $field_keys = isset($_POST['fields']) ? $_POST['fields'] : array();
      
      $fields = array();
      foreach(explode(',',$field_keys) as $key){
         $fields[$key] = 1;
      }
      
      $products = $this->model_catalog_product->getProducts($filter);
      
      $html = '';
      
      foreach($products as $product){
         $data[$product['product_id']] = array_intersect_key($product, $fields);
         
         $selected = $product['product_id'] == $select ? 'selected="selected"' : '';
         
         $html .= "<option value='$product[product_id]' $selected>$product[name]</option>";
      }
      echo json_encode(array('option_data' => $data, 'html' =>$html));
      exit();
   }
   
	public function autocomplete() {
	   $json = array();
      
      $filters = array(
         'filter_name' => null,
         'filter_model' => null,
         'filter_category_id' => null,
         'filter_sub_category' => null,
         'filter_status' => null,
         'filter_quantity' => null,
         'filter_manufacturer_id' => null,
         'filter_price' => null,
         'start' => 0,
         'limit' => 20,
       );
      
      $data = array();
      
      foreach($filters as $key => $default){
         if (isset($_GET[$key])) {
            $data[$key] = $_GET[$key];
         } elseif(!is_null($default)) {
            $data[$key] = $default;
         }
      }
      
		$results = $this->model_catalog_product->getProducts($data);
			
		foreach ($results as $result) {
			$option_data = array();
			
			$product_options = $this->model_catalog_product->getProductOptions($result['product_id']);	
			
			foreach ($product_options as $product_option) {
				$option_value_data = array();
				
				foreach ($product_option['product_option_value'] as $product_option_value) {
					$option_value_data[] = array(
						'product_option_value_id' => $product_option_value['product_option_value_id'],
						'option_value_id'         => $product_option_value['option_value_id'],
						'name'                    => $product_option_value['name'],
						'price'                   => (float)$product_option_value['price'] ? $this->currency->format($product_option_value['price'], $this->config->get('config_currency')) : false,
					);	
				}
			
				$option_data[] = array(
					'product_option_id' => $product_option['product_option_id'],
					'option_id'         => $product_option['option_id'],
					'name'              => $product_option['name'],
					'type'              => $product_option['type'],
					'option_value'      => $option_value_data,
					'required'          => $product_option['required']
				);
			}
		   
			$json[] = array(
				'product_id' => $result['product_id'],
				'name'       => html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'),
				'model'      => $result['model'],
				'option'     => $option_data,
				'image'      => $result['image'],
				'thumb'      => $this->image->resize($result['image'], 100,100),
				'price'      => $result['price']
			);	
		}

		$this->response->setOutput(json_encode($json));
	}

   public function generate_url(){
      $name = isset($_POST['name'])?$_POST['name']:'';
      $product_id = isset($_POST['product_id'])?$_POST['product_id']:'';
      if(!$name)return;
      
      $this->load->model("catalog/product");
      echo json_encode($this->model_catalog_product->generate_url($product_id,$name));
      exit;
   }
   
   public function generate_model(){
      $name = isset($_POST['name'])?$_POST['name']:'';
      if(!$name)return;
      
      $this->load->model("catalog/product");
      echo json_encode($this->model_catalog_product->generate_model($name));
      exit;
   }

   public function fill_shipping_return_policy(){
      $id = isset($_POST['manufacturer_id'])?$_POST['manufacturer_id']:'';
      if(!$id)return;
      
      $this->load->model("catalog/manufacturer");
      $desc = $this->model_catalog_manufacturer->getManufacturerDescriptions($id);
      echo json_encode($desc);
      exit;
   }
   
   private function get_url($new_sort=false){
      $url = '';
      $queries = array('filter_name','filter_model','filter_category_id','filter_manufacturer_id','filter_price','filter_cost','filter_is_final','filter_quantity','filter_date_expires','filter_editable','filter_status','sort','order','page');
      foreach($queries as $query){
         if($new_sort && in_array($query,array('sort','order'))){
            if($query == 'order'){
               $order = (isset($_GET['order']) && $_GET['order'] == 'ASC')?'DESC':'ASC';
               $url .= "&order=$order";
            }
            continue;
         }
         $url .= isset($_GET[$query])?"&$query=" . $_GET[$query]:'';
      }
      return $url;
   }
}
