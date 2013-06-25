<?php
class Admin_Controller_Catalog_Manufacturer extends Controller 
{
  	public function index()
  	{
		$this->load->language('catalog/manufacturer');
		
		$this->getList();
  	}
  
  	public function insert()
  	{
		$this->load->language('catalog/manufacturer');
		
		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Catalog_Manufacturer->addManufacturer($_POST);
			
			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));
			}
			
			$this->url->redirect($this->url->link('catalog/manufacturer'));
		}
	
		$this->getForm();
  	}
	
  	public function update()
  	{
		$this->load->language('catalog/manufacturer');
		
		if ($this->request->isPost() && $this->validateForm()) {
			$manufacturer_id = isset($_GET['manufacturer_id'])?$_GET['manufacturer_id']:0;
		
			$this->Model_Catalog_Manufacturer->editManufacturer($_GET['manufacturer_id'], $_POST);
			
			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));
				$this->url->redirect($this->url->link('catalog/manufacturer'));
			}
		}
	
		$this->getForm();
  	}

  	public function delete()
  	{
		$this->load->language('catalog/manufacturer');
		
		if (isset($_GET['manufacturer_id']) && $this->validateDelete()) {
			$this->Model_Catalog_Manufacturer->deleteManufacturer($_GET['manufacturer_id']);
			
			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));
				$this->url->redirect($this->url->link('catalog/manufacturer', $this->url->get_query_exclude('manufacturer_id')));
			}
		}
	
		$this->getList();
  	}
	
	public function batch_update()
	{
		$this->language->load('catalog/manufacturer');
		
		if (!empty($_POST['selected']) && isset($_GET['action'])) {
			if ($_GET['action'] !== 'delete' || $this->validateDelete()) {
				foreach ($_POST['selected'] as $manufacturer_id) {
					switch($_GET['action']){
						case 'enable':
							$this->Model_Catalog_Manufacturer->updateField($manufacturer_id, array('status' => 1));
							break;
						case 'disable':
							$this->Model_Catalog_Manufacturer->updateField($manufacturer_id, array('status' => 0));
							break;
						case 'delete':
								$this->Model_Catalog_Manufacturer->deleteManufacturer($manufacturer_id);
							break;
						case 'copy':
							$this->Model_Catalog_Manufacturer->copyManufacturer($manufacturer_id);
							break;
					}
					
					if ($this->error) {
						break;
					}
				}
			}
			
			if (!$this->error && !$this->message->error_set()) {
				$this->message->add('success',$this->_('text_success'));
				
				$this->url->redirect($this->url->link('catalog/manufacturer', $this->url->get_query_exclude('action')));
			}
		}

		$this->getList();
	}
	
  	private function getList()
  	{
  		$this->document->setTitle($this->_('heading_title'));
		
		$this->template->load('catalog/manufacturer_list');
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('catalog/manufacturer'));
		//The Table Columns
		$columns = array();

		$columns['thumb'] = array(
			'type' => 'image',
			'display_name' => $this->_('column_image'),
			'filter' => false,
			'sortable' => true,
			'sort_value' => '__image_sort__image',
		);
		
		$columns['name'] = array(
			'type' => 'text',
			'display_name' => $this->_('column_name'),
			'filter' => true,
			'sortable' => true,
		);
		
		$columns['vendor_id'] = array(
			'type' => 'text',
			'display_name' => $this->_('column_vendor_id'),
			'filter' => true,
			'sortable' => true,
		);
		
		
		$columns['date_active'] = array(
			'type' => 'datetime',
			'display_name' => $this->_('column_date_active'),
			'filter' => true,
			'sortable' => true,
		);
		
		$columns['date_expires'] = array(
			'type' => 'datetime',
			'display_name' => $this->_('column_date_expires'),
			'filter' => true,
			'sortable' => true,
		);
		
		$columns['stores'] = array(
			'type' => 'multiselect',
			'display_name' => $this->_('column_store'),
			'filter' => true,
			'build_config' => array('store_id' => 'name'),
			'build_data' => $this->Model_Setting_Store->getStores(),
			'sortable' => false,
		);
		
		$columns['status'] = array(
			'type' => 'select',
			'display_name' => $this->_('column_status'),
			'filter' => true,
			'build_data' => $this->_('data_statuses'),
			'sortable' => true,
		);
		
		//The Sort data
		$sort_filter = array();
		
		$this->sort->load_query_defaults($sort_filter, 'name', 'ASC');
		
		//Filter
		$filter_values = !empty($_GET['filter']) ? $_GET['filter'] : array();
		
		if ($filter_values) {
			$sort_filter += $filter_values;
		}
		
		$manufacturer_total = $this->Model_Catalog_Manufacturer->getTotalManufacturers($sort_filter);
		$manufacturers = $this->Model_Catalog_Manufacturer->getManufacturers($sort_filter);
		
		foreach ($manufacturers as &$manufacturer) {
			$manufacturer['actions'] = array(
				'edit' => array(
					'text' => $this->_('text_edit'),
					'href' => $this->url->link('catalog/manufacturer/update', 'manufacturer_id=' . $manufacturer['manufacturer_id'])
				),
				'delete' => array(
					'text' => $this->_('text_delete'),
					'href' => $this->url->link('catalog/manufacturer/delete', 'manufacturer_id=' . $manufacturer['manufacturer_id'])
				)
			);
			
			if ($manufacturer['date_active'] === DATETIME_ZERO) {
				$manufacturer['date_active'] = $this->_('text_no_date_active');
			} else {
				$manufacturer['date_active'] = $this->date->format($manufacturer['date_active'], 'M d, Y H:i:s');
			}

			if ($manufacturer['date_expires'] === DATETIME_ZERO) {
				$manufacturer['date_expires'] = $this->_('text_no_date_expires');
			} else {
				$manufacturer['date_expires'] = $this->date->format($manufacturer['date_active'], 'M d, Y H:i:s');
			}
			
			$manufacturer['thumb'] = $this->image->resize($manufacturer['image'], $this->config->get('config_image_admin_list_width'), $this->config->get('config_image_admin_list_height'));
			
			$manufacturer['stores'] = $this->Model_Catalog_Manufacturer->getManufacturerStores($manufacturer['manufacturer_id']);
		} unset($manufacturer);
		
		//The table template data
		$tt_data = array(
			'row_id'		=> 'manufacturer_id',
			'route'		=> 'catalog/manufacturer',
			'sort'		=> $sort_filter['sort'],
			'order'		=> $sort_filter['order'],
			'page'		=> $sort_filter['page'],
			'sort_url'	=> $this->url->link('catalog/manufacturer', $this->url->get_query('filter')),
			'columns'	=> $columns,
			'data'		=> $manufacturers,
		);
		
		$tt_data += $this->language->data;
		
		//Build the table template
		$this->table->init();
		$this->table->set_template('table/list_view');
		$this->table->set_template_data($tt_data);
		$this->table->map_attribute('filter_value', $filter_values);
		
		$this->data['list_view'] = $this->table->render();
		
		//Batch Actions
		$url_query = $this->url->get_query('filter', 'sort', 'order', 'page');
		
		$this->data['batch_actions'] = array(
			'enable'	=> array(
				'label' => "Enable"
			),
			'disable'=>	array(
				'label' => "Disable",
			),
			'copy' => array(
				'label' => "Copy",
			),
			'delete' => array(
				'label' => "Delete",
			),
		);
		
		$this->data['batch_update'] = html_entity_decode($this->url->link('catalog/manufacturer/batch_update', $url_query));
		
		//Action buttons
		$this->data['insert'] = $this->url->link('catalog/manufacturer/insert');
		
		//Item Limit Menu
		$this->data['limits'] = $this->sort->render_limit();
		
		//Pagination
		$this->pagination->init();
		$this->pagination->total = $manufacturer_total;
		
		$this->data['pagination'] = $this->pagination->render();
		
		//Children
		$this->data['breadcrumbs'] = $this->breadcrumb->render();
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

  	private function getForm()
  	{
  		$this->document->setTitle($this->_('heading_title'));
		
		$this->template->load('catalog/manufacturer_form');

  		$manufacturer_id = $this->data['manufacturer_id'] = isset($_GET['manufacturer_id']) ? (int)$_GET['manufacturer_id'] : null;
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('catalog/manufacturer'));
		
		if (!$manufacturer_id) {
			$this->data['action'] = $this->url->link('catalog/manufacturer/insert');
		} else {
			$this->data['action'] = $this->url->link('catalog/manufacturer/update', 'manufacturer_id=' . $manufacturer_id);
		}
		
		$this->data['cancel'] = $this->url->link('catalog/manufacturer');
		
		if ($manufacturer_id && !$this->request->isPost()) {
			$manufacturer_info = $this->Model_Catalog_Manufacturer->getManufacturer($manufacturer_id);
			
			$manufacturer_info['stores'] = $this->Model_Catalog_Manufacturer->getManufacturerStores($manufacturer_id);
		}
		
		$defaults = array(
			'name' => '',
			'keyword' => '',
			'image' => '',
			'date_active' => $this->date->now(),
			'date_expires'=> $this->date->add(null, '30 days'),
			'description' => '',
			'teaser' => '',
			'shipping_return' => '',
			'stores' => array(1),
			'sort_order' => 0,
			'status' => 0,
		);
		
		
		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$this->data[$key] = $_POST[$key];
			} elseif (isset($manufacturer_info[$key])) {
				$this->data[$key] = $manufacturer_info[$key];
			} else {
				$this->data[$key] = $default;
			}
		}
		
		$this->data['data_stores'] = $this->Model_Setting_Store->getStores();
		
		$translate_fields = array(
			'name',
			'description',
			'teaser',
			'shipping_return',
		);
		
		$this->data['translations'] = $this->translation->get_translations('manufacturer', $manufacturer_id, $translate_fields);
		
		$this->data['breadcrumbs'] = $this->breadcrumb->render();
		
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
		
		if (!$this->validation->text($_POST['name'], 3, 128)) {
			$this->error['name'] = $this->_('error_name');
		}
		
		if (!isset($_POST['keyword'])) {
			$_POST['keyword'] = $this->tool->get_slug($_POST['name']);
		} elseif (empty($_POST['keyword']) || preg_match("/[^A-Za-z0-9-]/", $_POST['keyword'])) {
			$this->error['keyword'] = $this->_('error_keyword');
		}
		
		return $this->error ? false : true;
  	}

  	private function validateDelete()
  	{
		if (!$this->user->hasPermission('modify', 'catalog/manufacturer')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		$manufacturer_ids = array();
		
		if (isset($_POST['selected'])) {
			$manufacturer_ids = $_POST['selected'];
		}
		
		if (isset($_GET['manufacturer_id'])) {
			$manufacturer_ids[] = $_GET['manufacturer_id'];
		}

		foreach ($manufacturer_ids as $manufacturer_id) {
			$data = array(
				'manufacturer_id' => $manufacturer_id,
			);
			
  			$product_count = $this->Model_Catalog_Product->getTotalProducts($data);
	
			if ($product_count) {
				$this->error['manufacturer' . $manufacturer_id] = $this->_('error_product', $product_count);
			}
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