<?php
class ControllerSettingStore extends Controller {
	 

	public function index() {
		$this->load->language('setting/store');

		$this->document->setTitle($this->_('heading_title'));
		 
		$this->getList();
	}
	      
  	public function insert() {
    	$this->load->language('setting/store');

    	$this->document->setTitle($this->_('heading_title')); 
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$store_id = $this->model_setting_store->addStore($_POST);
	  		
			$this->model_setting_setting->editSetting('config', $_POST, $store_id);
			
			$this->message->add('success', $this->_('text_success'));
			
			$this->redirect($this->url->link('setting/store'));
    	}
	
    	$this->getForm();
  	}

  	public function update() {
    	$this->load->language('setting/store');

    	$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_setting_store->editStore($_GET['store_id'], $_POST);
			
			$this->model_setting_setting->editSetting('config', $_POST, $_GET['store_id']);
			
			$this->message->add('success', $this->_('text_success'));
			
			$this->redirect($this->url->link('setting/store', 'store_id=' . $_GET['store_id']));
		}

    	$this->getForm();
  	}

  	public function delete() {
    	$this->load->language('setting/store');

    	$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_POST['selected']) && $this->validateDelete()) {
			foreach ($_POST['selected'] as $store_id) {
				$this->model_setting_store->deleteStore($store_id);
				
				$this->model_setting_setting->deleteSetting('config', $store_id);
			}

			$this->message->add('success', $this->_('text_success'));
			
			$this->redirect($this->url->link('setting/store'));
		}

    	$this->getList();
  	}
	
	private function getList() {
$this->template->load('setting/store_list');

	   $url = $this->get_url(array('page'));
      
      $this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
      $this->breadcrumb->add($this->_('heading_title'), $this->url->link('setting/store'));
      					
		$this->data['insert'] = $this->url->link('setting/store/insert');
		$this->data['delete'] = $this->url->link('setting/store/delete');	

		$store_total = $this->model_setting_store->getTotalStores();
	
		$stores = $this->model_setting_store->getStores();
 
    	foreach ($stores as &$store) {
			$action = array();
			
			if($store['store_id'] === 0){
				$action[] = array(
					'text' => $this->_('text_edit'),
					'href' => $this->url->link('setting/setting')
				);
			}
			else{			
				$action[] = array(
					'text' => $this->_('text_edit'),
					'href' => $this->url->link('setting/store/update', 'store_id=' . $store['store_id'])
				);
			}
			
			$store['action'] = $action;
			
			if($store['store_id'] === 0){
				$store['name'] .= $this->_('text_default'); 
			}
			
			$store['selected'] = isset($_POST['selected']) && in_array($store['store_id'], $_POST['selected']);
		}
		
		$this->data['data_stores'] = $stores;
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->response->setOutput($this->render());
	}
	 
	public function getForm() {
		$this->template->load('setting/store_form');

	   $store_id = isset($_GET['store_id'])?$_GET['store_id']:0;
      
      $this->document->addScript("image_manager.js");
		
      $this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
      $this->breadcrumb->add($this->_('heading_title'), $this->url->link('setting/store'));
      
		if (!$store_id) {
			$this->data['action'] = $this->url->link('setting/store/insert');
		} else {
			$this->data['action'] = $this->url->link('setting/store/update', 'store_id=' . $store_id);
		}
				
		$this->data['cancel'] = $this->url->link('setting/store');
	
		if (isset($_GET['store_id']) && ($_SERVER['REQUEST_METHOD'] != 'POST')) {
			$store_info = $this->model_setting_setting->getSetting('config', $store_id);
    	}
		
		$defaults = array('config_url'=>'',
                        'config_ssl'=>'',
                        'config_name'=>'',
                        'config_owner'=>'',
                        'config_address'=>'',
                        'config_email'=>'',
                        'config_telephone'=>'',
                        'config_fax'=>'',
                        'config_title'=>'',
                        'config_meta_description'=>'',
                        'config_default_layout_id'=>'',
                        'config_template'=>'',
                        'config_country_id'=>$this->config->get('config_country_id'),
                        'config_zone_id'=>$this->config->get('config_zone_id'),
                        'config_language'=>$this->config->get('config_language'),
                        'config_currency'=>$this->config->get('config_currency'),
                        'config_catalog_limit'=>'12',
                        'config_allowed_shipping_zone'=>0,
                        'config_show_price_with_tax'=>'',
                        'config_tax_default'=>'',
                        'config_tax_customer'=>'',
                        'config_customer_group_id'=>'',
                        'config_customer_price'=>'',
                        'config_customer_approval'=>'',
                        'config_guest_checkout'=>'',
                        'config_account_id'=>'',
                        'config_checkout_id'=>'',
                        'config_stock_display'=>'',
                        'config_stock_checkout'=>'',
                        'config_order_status_id'=>'',
                        'config_cart_weight'=>'',
                        'config_logo'=>'',
                        'config_icon'=>'',
                        'config_image_category_height'=>80,
                        'config_image_thumb_width'=>228,
                        'config_image_thumb_height'=>228,
                        'config_image_popup_width'=>500,
                        'config_image_popup_height'=>500,
                        'config_image_product_width'=>80,
                        'config_image_product_height'=>80,
                        'config_image_category_width'=>80,
                        'config_image_additional_width'=>74,
                        'config_image_additional_height'=>74,
                        'config_image_related_width'=>80,
                        'config_image_related_height'=>80,
                        'config_image_compare_width'=>90,
                        'config_image_compare_height'=>90,
                        'config_image_wishlist_width'=>50,
                        'config_image_wishlist_height'=>50,
                        'config_image_cart_width'=>80,
                        'config_image_cart_height'=>80,
                        'config_use_ssl'=>''
                       );
      foreach($defaults as $d=>$value){
         if (isset($_POST[$d])) {
            $this->data[$d] = $_POST[$d];
         } elseif (isset($store_info[$d])) {
            $this->data[$d] = $store_info[$d];
         } else {
            $this->data[$d] = $value;
         }
      }
      
      $this->data['layouts'] = $this->model_design_layout->getLayouts();
      
      $this->data['templates'] = array();

      $directories = glob(DIR_CATALOG . 'view/theme/*', GLOB_ONLYDIR);
      
      foreach ($directories as $directory) {
         $this->data['templates'][] = basename($directory);
      }  
      
      $this->data['geo_zones'] = array_merge(array(0=>"--- All Zones ---"),$this->model_localisation_geo_zone->getGeoZones());
      
      $this->data['countries'] = $this->model_localisation_country->getCountries();
      
      $this->data['languages'] = $this->model_localisation_language->getLanguages();
      
      $this->data['currencies'] = $this->model_localisation_currency->getCurrencies();
      
      $this->data['customer_groups'] = $this->model_sale_customer_group->getCustomerGroups();
      
      $this->data['informations'] = $this->model_catalog_information->getInformations();
      
      $this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
      
      if (isset($store_info['config_logo']) && file_exists(DIR_IMAGE . $store_info['config_logo']) && is_file(DIR_IMAGE . $store_info['config_logo'])) {
         $this->data['logo'] = $this->image->resize($store_info['config_logo'], 100, 100);    
      } else {
         $this->data['logo'] = $this->image->resize('no_image.jpg', 100, 100);
      }
      
      if (isset($store_info['config_icon']) && file_exists(DIR_IMAGE . $store_info['config_icon']) && is_file(DIR_IMAGE . $store_info['config_icon'])) {
         $this->data['icon'] = $this->image->resize($store_info['config_icon'], 100, 100);
      } else {
         $this->data['icon'] = $this->image->resize('no_image.jpg', 100, 100);
      }
      
      $this->data['no_image'] = $this->image->resize('no_image.jpg', 100, 100);
      
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validateForm() {
		if (!$this->user->hasPermission('modify', 'setting/store')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		if (!$_POST['config_url']) {
			$this->error['config_url'] = $this->_('error_url');
		}
				
		if (!$_POST['config_name']) {
			$this->error['config_name'] = $this->_('error_name');
		}	
		
		if ((strlen($_POST['config_owner']) < 3) || (strlen($_POST['config_owner']) > 64)) {
			$this->error['config_owner'] = $this->_('error_owner');
		}

		if ((strlen($_POST['config_address']) < 3) || (strlen($_POST['config_address']) > 256)) {
			$this->error['config_address'] = $this->_('error_address');
		}
		
    	if ((strlen($_POST['config_email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $_POST['config_email'])) {
      		$this->error['config_email'] = $this->_('error_email');
    	}

    	if ((strlen($_POST['config_telephone']) < 3) || (strlen($_POST['config_telephone']) > 32)) {
      		$this->error['config_telephone'] = $this->_('error_telephone');
    	}
		
		if (!$_POST['config_title']) {
			$this->error['config_title'] = $this->_('error_title');
		}	
		
		if (!$_POST['config_image_category_width'] || !$_POST['config_image_category_height']) {
			$this->error['config_image_category_height'] = $this->_('error_image_category');
		}
				
		if (!$_POST['config_image_thumb_width'] || !$_POST['config_image_thumb_height']) {
			$this->error['config_image_thumb_height'] = $this->_('error_image_thumb');
		}	
		
		if (!$_POST['config_image_popup_width'] || !$_POST['config_image_popup_height']) {
			$this->error['config_image_popup_height'] = $this->_('error_image_popup');
		}
			
		if (!$_POST['config_image_product_width'] || !$_POST['config_image_product_height']) {
			$this->error['config_image_product_height'] = $this->_('error_image_product');
		}
		
		if (!$_POST['config_image_additional_width'] || !$_POST['config_image_additional_height']) {
			$this->error['config_image_additional_height'] = $this->_('error_image_additional');
		}
		
		if (!$_POST['config_image_related_width'] || !$_POST['config_image_related_height']) {
			$this->error['config_image_related_height'] = $this->_('error_image_related');
		}
		
		if (!$_POST['config_image_compare_width'] || !$_POST['config_image_compare_height']) {
			$this->error['config_image_compare_height'] = $this->_('error_image_compare');
		}
		
		if (!$_POST['config_image_wishlist_width'] || !$_POST['config_image_wishlist_height']) {
			$this->error['config_image_wishlist_height'] = $this->_('error_image_wishlist');
		}
						
		if (!$_POST['config_image_cart_width'] || !$_POST['config_image_cart_height']) {
			$this->error['config_image_cart_height'] = $this->_('error_image_cart');
		}
		
		if (!$_POST['config_catalog_limit']) {
			$this->error['config_catalog_limit'] = $this->_('error_limit');
		}
					
		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	private function validateDelete() {
		if (!$this->user->hasPermission('modify', 'setting/store')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		foreach ($_POST['selected'] as $store_id) {
			if (!$store_id) {
				$this->error['warning'] = $this->_('error_default');
			}
			
			$store_total = $this->model_sale_order->getTotalOrdersByStoreId($store_id);
	
			if ($store_total) {
				$this->error['warning'] = sprintf($this->_('error_store'), $store_total);
			}	
		}
		
		if (!$this->error) {
			return true; 
		} else {
			return false;
		}
	}
	
	public function template() {
		$template = basename($_GET['template']);
		
		if (file_exists(DIR_IMAGE . 'templates/' . $template . '.png')) {
			$image = HTTPS_IMAGE . 'templates/' . $template . '.png';
		} else {
			$image = HTTPS_IMAGE . 'no_image.jpg';
		}
		
		$this->response->setOutput('<img src="' . $image . '" alt="" title="" style="border: 1px solid #EEEEEE;" />');
	}		
	
   private function get_url($filters=null){
      $url = '';
      $filters = $filters?$filters:array('sort', 'order', 'page');
      foreach($filters as $f)
         if (isset($_GET[$f]))
            $url .= "&$f=" . $_GET[$f];
      return $url;
   }		
}