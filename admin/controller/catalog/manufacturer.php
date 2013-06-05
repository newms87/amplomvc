<?php
class Admin_Controller_Catalog_Manufacturer extends Controller 
{
	
  	public function index()
  	{
		$this->load->language('catalog/manufacturer');
		
		$this->document->setTitle($this->_('heading_title'));
		
		$this->getList();
  	}
  
  	public function insert()
  	{
		$this->load->language('catalog/manufacturer');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->Model_Catalog_Manufacturer->addManufacturer($_POST);
			
			if($this->user->isAdmin())
				$this->message->add('success', $this->_('text_success'));
			else {
				$this->message->add('warning', $this->language->formt('error_portal_insert', $this->config->get('config_email')));
				$this->message->add('success', $this->_('text_portal_insert_success'));
			}
			
			$url = $this->get_url();
			
			$this->url->redirect($this->url->link('catalog/manufacturer', $url));
		}
	
		$this->getForm();
  	}
	
  	public function update()
  	{
		$this->load->language('catalog/manufacturer');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$manufacturer_id = isset($_GET['manufacturer_id'])?$_GET['manufacturer_id']:0;
		
			$this->Model_Catalog_Manufacturer->editManufacturer($_GET['manufacturer_id'], $_POST);
			
			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));
				$this->url->redirect($this->url->link('catalog/manufacturer', $this->url->get_query('sort', 'order', 'page')));
			}
		}
	
		$this->getForm();
  	}

  	public function delete()
  	{
		$this->load->language('catalog/manufacturer');

		$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_POST['selected']) && $this->validateDelete()) {
			foreach ($_POST['selected'] as $manufacturer_id) {
				$this->Model_Catalog_Manufacturer->deleteManufacturer($manufacturer_id);
			}

			$this->message->add('success', $this->_('text_success'));
			
			$url = $this->get_url();
			
			$this->url->redirect($this->url->link('catalog/manufacturer', $url));
		}
	
		$this->getList();
  	}
	
  	private function getList()
  	{
		$this->template->load('catalog/manufacturer_list');

  		$sort_list = array('sort'=>'name','order'=>'ASC','page'=>1);
  		foreach($sort_list as $key=>$default)
			$$key = isset($_GET[$key])?$_GET[$key]:$default;
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('catalog/manufacturer'));
		
		$url = $this->get_url();
							
		$this->data['insert'] = $this->url->link('catalog/manufacturer/insert', $url);
		$this->data['delete'] = $this->url->link('catalog/manufacturer/delete', $url);

		$this->data['manufacturers'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);
		
		$manufacturer_total = $this->Model_Catalog_Manufacturer->getTotalManufacturers($data);
		
		$results = $this->Model_Catalog_Manufacturer->getManufacturers($data);
		
		foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->_('text_edit'),
				'href' => $this->url->link('catalog/manufacturer/update', 'manufacturer_id=' . $result['manufacturer_id'] . $url)
			);
						
			$this->data['manufacturers'][] = array(
				'manufacturer_id' => $result['manufacturer_id'],
				'name'				=> $result['name'],
				'vendor_id'		=> $result['vendor_id'],
				'date_active'	=> $result['date_active'] == DATETIME_ZERO ? "No Activation Date" : $this->tool->format_datetime($result['date_active'],'M d, Y H:i:s'),
				'date_expires'	=> $result['date_expires'] == DATETIME_ZERO ? "Never" : $this->tool->format_datetime($result['date_expires'],'M d, Y H:i:s'),
				'status'			=> $result['status'],
				'sort_order'		=> $result['sort_order'],
				'selected'		=> isset($_POST['selected']) && in_array($result['manufacturer_id'], $_POST['selected']),
				'action'			=> $action
			);
		}
		
		$url_query = $this->url->get_query(array('page'));
		
		$url_query .= $order == 'ASC'? '&order=DESC':'&order=ASC';
		
		$sort_by = array('name'=>'name','vendor_id'=>'vendor_id','date_expires'=>'date_expires','date_active'=>'date_active', 'status'=>'status','sort_order'=>'sort_order');
		foreach($sort_by as $key=>$s)
			$this->data['sort_'.$s] = $this->url->link('catalog/manufacturer', 'sort=' . $key . $url);
		
		$url = $this->get_url(array('sort','order'));

		$this->pagination->init();
		$this->pagination->total = $manufacturer_total;
		$this->data['pagination'] = $this->pagination->render();

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
  
  	private function getForm()
  	{
  		if ($this->user->isDesigner()) {
			$this->template->load('catalog/manufacturer_form_restricted');
		}
		else {
			$this->template->load('catalog/manufacturer_form');
		}

  		$manufacturer_id = $this->data['manufacturer_id'] = isset($_GET['manufacturer_id'])?(int)$_GET['manufacturer_id']:null;
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('catalog/manufacturer'));
		
		$url = $this->get_url();
				
		if (!$manufacturer_id) {
			$this->data['action'] = $this->url->link('catalog/manufacturer/insert', $url);
		} else {
			$this->data['action'] = $this->url->link('catalog/manufacturer/update', 'manufacturer_id=' . $manufacturer_id . $url);
		}
		
		$this->data['cancel'] = $this->url->link('catalog/manufacturer', $url);
		
		$manufacturer_info = array();
		if ($manufacturer_id && ($_SERVER['REQUEST_METHOD'] != 'POST')) {
			$manufacturer_info = $this->Model_Catalog_Manufacturer->getManufacturer($manufacturer_id);
		}
		
		$defaults = array('name'=>'',
								'keyword'=>'',
								'manufacturer_store'=>array(0,1,2),
								'manufacturer_description'=>array(),
								'section_attr'=>'',
								'image'=>'',
								'sort_order'=>0,
								'date_active'=>date_format(date_create(),'Y-m-d H:i:s'),
								'date_expires'=>date_format(date_add(new DateTime(), date_interval_create_from_date_string('30 days')),'Y-m-d H:i:s'),
								'status'=>0,
								'editable'=>1
								);
		
		foreach ($defaults as $d=>$value) {
			if (isset($_POST[$d]))
				$this->data[$d] = $_POST[$d];
			elseif (isset($manufacturer_info[$d]))
				$this->data[$d] = $manufacturer_info[$d];
			elseif(!$manufacturer_id)
				$this->data[$d] = $value;
		}
		
		if (!$this->data['editable']) {
			$this->language->format('text_not_editable', $this->data['name'],$this->config->get('config_email'), "Active%20Designer%20Brand%20Modification%20Request");
		}
		
		//Get the rest of the manufacturer information
		if(!isset($this->data['manufacturer_store']))
			$this->data['manufacturer_store'] = $this->Model_Catalog_Manufacturer->getManufacturerStores($manufacturer_id);
		
		if (!isset($this->data['manufacturer_description']))
			$this->data['manufacturer_description'] = $this->Model_Catalog_Manufacturer->getManufacturerDescriptions($manufacturer_id);

		if (!empty($manufacturer_info) && $manufacturer_info['image'] && file_exists(DIR_IMAGE . $manufacturer_info['image'])) {
			$this->data['thumb'] = $this->image->resize($manufacturer_info['image'], 100, 100);
		} else {
			$this->data['thumb'] = $this->image->resize('no_image.png', 100, 100);
		}
		
		$this->data['no_image'] = $this->image->resize('no_image.png', 100, 100);
		
		if ($manufacturer_id) {
			$this->data['articles'] = $this->Model_Catalog_Manufacturer->getManufacturerArticles($manufacturer_id);
		}
		else
			$this->data['articles'] = array();
			
		$this->data['section_attrs'] = array(0=>'( None )');
		$attrs = $this->Model_Catalog_AttributeGroup->getAttributeGroups();
		foreach($attrs as $a)
			$this->data['section_attrs'][$a['attribute_group_id']] = $a['name'];
		
		$this->data['languages'] = $this->Model_Localisation_Language->getLanguages();
		
		$this->data['data_stores'] = $this->Model_Setting_Store->getStores();
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
  	private function validateForm()
  	{
		if (!$this->user->hasPermission('modify', 'catalog/manufacturer')) {
				$this->error['warning'] = $this->_('error_permission');
		}

		if ((strlen($_POST['name']) < 3) || (strlen($_POST['name']) > 64)) {
				$this->error['name'] = $this->_('error_name');
		}
		
		if (isset($_POST['keyword'])) {
			$keyword =$_POST['keyword'];
			if(empty($keyword) || is_null($keyword) || preg_match("/[^A-Za-z0-9-]/",$keyword) > 0)
				$this->error['keyword'] = $this->_('error_keyword');
		}
		
		if ($this->user->isDesigner() && isset($_GET['manufacturer_id']) && !$this->Model_Catalog_Manufacturer->isEditable($_GET['manufacturer_id'])) {
			$this->message->add('warning', $this->_('warning_not_editable'));
			$this->url->redirect($this->url->link('catalog/manufacturer'));
		}
		
		return $this->error ? false : true;
  	}

  	private function validateDelete()
  	{
		if (!$this->user->hasPermission('modify', 'catalog/manufacturer')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		foreach ($_POST['selected'] as $manufacturer_id) {
  			$product_total = $this->Model_Catalog_Product->getTotalProductsByManufacturerId($manufacturer_id);
	
			if ($product_total) {
				$this->error['warning_product'] = sprintf($this->_('error_product'), $product_total);
			}
			
			$flashsales = $this->Model_Catalog_Flashsale->getFlashsalesByDesignerID($manufacturer_id);
			if(!empty($flashsales))
				$this->error['warning_flashsale'] = $this->_('error_flashsale');
			
			if ($this->user->isDesigner() && !$this->Model_Catalog_Manufacturer->isEditable($manufacturer_id)) {
				$this->error['warning_active'] = $this->_('warning_not_editable');
			}
		}
		
		if ($this->user->isDesigner()) {
			$this->error = array();
			$this->error['warning'] = $this->language->format('error_portal_delete', $this->config->get('config_email'));
		}
		
		
		return $this->error ? false : true;
  	}
	
	public function generate_url()
	{
		$name = isset($_POST['name'])?$_POST['name']:'';
		$manufacturer_id = isset($_POST['manufacturer_id'])?$_POST['manufacturer_id']:'';
		if(!$name)return;
		
		echo json_encode($this->Model_Catalog_Manufacturer->generate_url($manufacturer_id,$name));
		exit;
	}
	
	
	public function autocomplete()
	{
		$filters = array(
			'name' => null,
			'status' => null,
			'start' => 0,
			'limit' => 20,
		);
		
		$data = array();
		
		foreach ($filters as $key => $default) {
			if (isset($_GET[$key])) {
				$data[$key] = $_GET[$key];
			} elseif (!is_null($default)) {
				$data[$key] = $default;
			}
		}
		
		$results = $this->Model_Catalog_Manufacturer->getManufacturers($data);
		
		$json = array();
		
		foreach ($results as $result) {
			$json[] = array(
				'manufacturer_id' => $result['manufacturer_id'],
				'name'		=> html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'),
				'image'		=> $result['image'],
			);
		}

		$this->response->setOutput(json_encode($json));
	}
}