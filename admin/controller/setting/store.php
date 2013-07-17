<?php
class Admin_Controller_Setting_Store extends Controller 
{
	public function index()
	{
		$this->language->load('setting/store');

		$this->document->setTitle($this->_('heading_title'));
		
		$this->getList();
	}
			
  	public function insert()
  	{
		$this->language->load('setting/store');

		$this->document->setTitle($this->_('heading_title'));
		
		if ($this->request->isPost() && $this->validateForm()) {
			
			$store_id = $this->Model_Setting_Store->addStore($_POST);
			
			$this->Model_Setting_Setting->editSetting('config', $_POST, $store_id);
			
			$this->message->add('success', $this->_('text_success'));
			
			$this->url->redirect($this->url->link('setting/store'));
		}
	
		$this->getForm();
  	}

  	public function update()
  	{
		$this->language->load('setting/store');

		$this->document->setTitle($this->_('heading_title'));
		
		if ($this->request->isPost() && $this->validateForm()) {
			
			$this->Model_Setting_Store->editStore($_GET['store_id'], $_POST);
			
			$this->Model_Setting_Setting->editSetting('config', $_POST, $_GET['store_id']);
			
			$this->message->add('success', $this->_('text_success'));
			
			$this->url->redirect($this->url->link('setting/store', 'store_id=' . $_GET['store_id']));
		}

		$this->getForm();
  	}

  	public function delete()
  	{
		$this->language->load('setting/store');

		$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_POST['selected']) && $this->validateDelete()) {
			foreach ($_POST['selected'] as $store_id) {
				$this->Model_Setting_Store->deleteStore($store_id);
				
				$this->Model_Setting_Setting->deleteSetting('config', $store_id);
			}

			$this->message->add('success', $this->_('text_success'));
			
			$this->url->redirect($this->url->link('setting/store'));
		}

		$this->getList();
  	}
	
	private function getList()
	{
		$this->template->load('setting/store_list');

		$url = $this->url->getQuery('page');
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('setting/store'));
		
		$this->data['admin_settings'] = $this->url->link('setting/setting');
		$this->data['system_update'] = $this->url->link('setting/update');
		
		$this->data['insert'] = $this->url->link('setting/store/insert');
		$this->data['delete'] = $this->url->link('setting/store/delete');

		$store_total = $this->Model_Setting_Store->getTotalStores();
	
		$stores = $this->Model_Setting_Store->getStores();
 
		foreach ($stores as &$store) {
			$action = array();
			
			if ($store['store_id'] === 0) {
				$action[] = array(
					'text' => $this->_('text_edit'),
					'href' => $this->url->link('setting/setting')
				);
			}
			else {
				$action[] = array(
					'text' => $this->_('text_edit'),
					'href' => $this->url->link('setting/store/update', 'store_id=' . $store['store_id'])
				);
			}
			
			$store['action'] = $action;
			
			if ($store['store_id'] === 0) {
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
	
	public function getForm()
	{
		$this->template->load('setting/store_form');

		$store_id = isset($_GET['store_id']) ? $_GET['store_id'] : 0;
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('setting/store'));
		
		if (!$store_id) {
			$this->data['action'] = $this->url->link('setting/store/insert');
		} else {
			$this->data['action'] = $this->url->link('setting/store/update', 'store_id=' . $store_id);
		}
				
		$this->data['cancel'] = $this->url->link('setting/store');
	
		if ($store_id && !$this->request->isPost()) {
			$store = $this->Model_Setting_Store->getStore($store_id);
			
			if (!$store) {
				$this->message->add('warning', $this->_('error_store_invalid'));
				$this->url->redirect($this->url->link('setting/store'));
			}
			
			$store_config = $this->Model_Setting_Setting->getSetting('config', $store_id);
			
			if (empty($store_config)) {
				$store_config = $this->Model_Setting_Setting->getSetting('config', 0);
			}
			
			$store_info = $store + $store_config;
		}
		
		$defaults = array(
			'name' => 'Store ' . $store_id,
			'url' => '',
			'ssl' => '',
			'config_owner'=>'',
			'config_address'=>'',
			'config_email'=>'',
			'config_telephone'=>'',
			'config_fax'=>'',
			'config_title'=>'',
			'config_meta_description'=>'',
			'config_default_layout_id'=>'',
			'config_theme'=>'',
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
			'config_show_product_model' => 1,
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
		
		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$this->data[$key] = $_POST[$key];
			} elseif (isset($store_info[$key])) {
				$this->data[$key] = $store_info[$key];
			} else {
				$this->data[$key] = $default;
			}
		}
		
		$this->breadcrumb->add($this->data['name'], $this->url->link('setting/store/update', 'store_id=' . $store_id));
		
		$this->data['layouts'] = $this->Model_Design_Layout->getLayouts();
		
		$this->data['themes'] = $this->theme->getThemes();
		
		$this->data['geo_zones'] = array_merge(array(0=>"--- All Zones ---"),$this->Model_Localisation_GeoZone->getGeoZones());
		
		$this->data['countries'] = $this->Model_Localisation_Country->getCountries();
		
		$this->data['languages'] = $this->Model_Localisation_Language->getLanguages();
		
		$this->data['currencies'] = $this->Model_Localisation_Currency->getCurrencies();
		
		$this->data['customer_groups'] = $this->Model_Sale_CustomerGroup->getCustomerGroups();
		
		$this->data['informations'] = $this->Model_Catalog_Information->getInformations();
		
		$this->data['order_statuses'] = $this->order->getOrderStatuses();
		
		$this->data['load_theme_img'] = $this->url->link('setting/setting/theme');
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validateForm()
	{
		if (!$this->user->hasPermission('modify', 'setting/store')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		if (!$this->validation->text($_POST['name'], 1, 64)) {
			$this->error['name'] = $this->_('error_name');
		}
		
		if (!$this->validation->url($_POST['url'])) {
			$this->error['url'] = $this->_('error_url');
		}
		
		if (!$this->validation->url($_POST['ssl'])) {
			$this->error['ssl'] = $this->_('error_ssl');
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
					
		return $this->error ? false : true;
	}

	private function validateDelete()
	{
		if (!$this->user->hasPermission('modify', 'setting/store')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		foreach ($_POST['selected'] as $store_id) {
			if (!$store_id) {
				$this->error['warning'] = $this->_('error_default');
			}
			
			$filter = array(
				'store_ids' => array($store_id),
			);
			
			$store_total = $this->System_Model_Order->getTotalOrders($filter);
	
			if ($store_total) {
				$this->error['warning'] = sprintf($this->_('error_store'), $store_total);
			}
		}
		
		return $this->error ? false : true;
	}
}