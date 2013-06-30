<?php
class Admin_Controller_Design_Layout extends Controller 
{
	public function index()
	{
		$this->load->language('design/layout');

		$this->getList();
	}

	public function insert()
	{
		$this->load->language('design/layout');

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Design_Layout->addLayout($_POST);
			
			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));
				
				$this->url->redirect($this->url->link('design/layout'));
			}
		}

		$this->getForm();
	}

	public function update()
	{
		$this->load->language('design/layout');

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Design_Layout->editLayout($_GET['layout_id'], $_POST);

			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));
				
				$this->url->redirect($this->url->link('design/layout'));
			}
		}

		$this->getForm();
	}
 
	public function delete()
	{
		$this->load->language('design/layout');
 
		if (isset($_GET['layout_id']) && $this->validateDelete()) {
			$this->Model_Design_Layout->deleteLayout($_GET['layout_id']);
			
			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));
				
				$this->url->redirect($this->url->link('design/layout'));
			}
		}

		$this->getList();
	}
	
	public function batch_update()
	{
		$this->language->load('catalog/category');
		
		if (!empty($_POST['selected']) && isset($_GET['action'])) {
			foreach ($_POST['selected'] as $category_id) {
				switch($_GET['action']){
					case 'enable':
						$this->Model_Catalog_Category->updateField($category_id, array('status' => 1));
						break;
					case 'disable':
						$this->Model_Catalog_Category->updateField($category_id, array('status' => 0));
						break;
					case 'delete':
						$this->Model_Catalog_Category->deleteCategory($category_id);
						break;
					case 'copy':
						$this->Model_Catalog_Category->copyCategory($category_id);
						break;
				}
				
				if ($this->error) {
					break;
				}
			}
			
			if (!$this->error && !$this->message->error_set()) {
				$this->message->add('success',$this->_('text_success'));
				
				$this->url->redirect($this->url->link('catalog/category', $this->url->getQueryExclude('action')));
			}
		}

		$this->getList();
	}
	
	private function getList()
	{
		$this->template->load('design/layout_list');
		
		$this->document->setTitle($this->_('heading_title'));
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('design/layout'));
		
		//The Table Columns
		$columns = array();

		$columns['name'] = array(
			'type' => 'text',
			'display_name' => $this->_('column_name'),
			'filter' => true,
			'sortable' => true,
		);
		
		$columns['routes'] = array(
			'type' => 'text',
			'display_name' => $this->_('column_routes'),
			'filter' => false,
			'sortable' => false,
		);
		
		//The Sort data
		$sort_filter = $this->sort->getQueryDefaults('name', 'ASC');
		
		//Filter
		$filter_values = !empty($_GET['filter']) ? $_GET['filter'] : array();
		
		if ($filter_values) {
			$sort_filter += $filter_values;
		}

		$layout_total = $this->Model_Design_Layout->getTotalLayouts($sort_filter);
		$layouts = $this->Model_Design_Layout->getLayouts($sort_filter);
		
		$url_query = $this->url->getQueryExclude('layout_id');
		
		foreach ($layouts as &$layout) {
			$layout['actions'] = array(
				'edit' => array(
					'text' => $this->_('text_edit'),
					'href' => $this->url->link('design/layout/update', 'layout_id=' . $layout['layout_id'])
				),
				'delete' => array(
					'text' => $this->_('text_delete'),
					'href' => $this->url->link('design/layout/delete', 'layout_id=' . $layout['layout_id'] . '&' . $url_query)
				)
			);
			
			$routes = $this->Model_Design_Layout->getLayoutRoutes($layout['layout_id']);
			$layout['routes'] = implode('<br />', array_column($routes, 'route'));
		} unset($layout);
		
		//The table template data
		$tt_data = array(
			'row_id'		=> 'layout_id',
			'route'		=> 'design/layout',
			'columns'	=> $columns,
			'data'		=> $layouts,
		);
		
		//Build the table template
		$this->table->init();
		$this->table->set_template('table/list_view');
		$this->table->set_template_data($tt_data);
		$this->table->map_attribute('filter_value', $filter_values);
		
		$this->data['list_view'] = $this->table->render();
		
		//Batch Actions
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
		
		$this->data['batch_update'] = html_entity_decode($this->url->link('design/layout/batch_update', $url_query));
		
		//Action Buttons
		$this->data['insert'] = $this->url->link('design/layout/insert');
		
		//Item Limit Menu
		$this->data['limits'] = $this->sort->render_limit();
		
		//Pagination
		$this->pagination->init();
		$this->pagination->total = $layout_total;
		$this->data['pagination'] = $this->pagination->render();
		
		$this->data['breadcrumbs'] = $this->breadcrumb->render();
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function getForm()
	{
		$this->template->load('design/layout_form');
		
		$this->document->setTitle($this->_('heading_title'));
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('design/layout'));

		$layout_id = isset($_GET['layout_id']) ? $_GET['layout_id'] : false;
		
		if ($layout_id && !$this->request->isPost()) {
			$layout_info = $this->Model_Design_Layout->getLayout($layout_id);
			
			$layout_info['routes'] = $this->Model_Design_Layout->getLayoutRoutes($layout_id);
		}
		
		$defaults = array(
			'name' => '',
			'routes' => array(),
		);
		
		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$this->data[$key] = $_POST[$key];
			} elseif (isset($layout_info[$key])) {
				$this->data[$key] = $layout_info[$key];
			} else {
				$this->data[$key] = $default;
			}
		}
		
		$this->data['data_stores'] = $this->Model_Setting_Store->getStores();
		
		//Action Buttons
		if ($layout_id) {
			$this->data['action'] = $this->url->link('design/layout/update', 'layout_id=' . $layout_id);
		} else {
			$this->data['action'] = $this->url->link('design/layout/insert');
		}
		
		$this->data['cancel'] = $this->url->link('design/layout');
		
		$this->data['breadcrumbs'] = $this->breadcrumb->render();
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validateForm()
	{
		if (!$this->user->hasPermission('modify', 'design/layout')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		if ((strlen($_POST['name']) < 3) || (strlen($_POST['name']) > 64)) {
			$this->error['name'] = $this->_('error_name');
		}

		return $this->error ? false : true;
	}

	private function validateDelete()
	{
		if (!$this->user->hasPermission('modify', 'design/layout')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		foreach ($_POST['selected'] as $layout_id) {
			if ($this->config->get('config_default_layout_id') == $layout_id) {
				$this->error['warning'] = $this->_('error_default');
			}
			
			$data = array(
				'layouts' => array($layout_id),
			);
			
			$product_total = $this->Model_Catalog_Product->getTotalProducts($data);
	
			if ($product_total) {
				$this->error['warning_product'] = $this->_('error_product', $product_total);
			}

			$category_total = $this->Model_Catalog_Category->getTotalCategories($data);
	
			if ($category_total) {
				$this->error['warning_category'] = $this->_('error_category', $category_total);
			}
			
			$information_total = $this->Model_Catalog_Information->getTotalInformations($data);
		
			if ($information_total) {
				$this->error['warning_information'] = $this->_('error_information', $information_total);
			}
		}
	
		return $this->error ? false : true;
	}
}