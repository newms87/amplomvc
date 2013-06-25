<?php
class Admin_Controller_Catalog_Category extends Controller 
{
	public function index()
	{
		$this->load->language('catalog/category');

		$this->getList();
	}

	public function insert()
	{
		$this->load->language('catalog/category');
		
		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Catalog_Category->addCategory($_POST);
			
			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));
				
				$this->url->redirect($this->url->link('catalog/category'));
			}
		}

		$this->getForm();
	}

	public function update()
	{
		$this->load->language('catalog/category');

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Catalog_Category->editCategory($_GET['category_id'], $_POST);
			
			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));
			
				$this->url->redirect($this->url->link('catalog/category'));
			}
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->load->language('catalog/category');
		
		if (!empty($_GET['category_id']) && $this->validateDelete()) {
			$this->Model_Catalog_Category->deleteCategory($_GET['category_id']);
			
			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));

				$this->url->redirect($this->url->link('catalog/category'));
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
				
				$this->url->redirect($this->url->link('catalog/category', $this->url->get_query_exclude('action')));
			}
		}

		$this->getList();
	}
	
	private function getList()
	{
		$this->document->setTitle($this->_('heading_title'));
		
		$this->template->load('catalog/category_list');

		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('catalog/category'));
		
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
		
		//Retrieve the Filtered Table row data
		$category_total = $this->Model_Catalog_Category->getTotalCategories($sort_filter);
		$categories = $this->Model_Catalog_Category->getCategoriesWithParents($sort_filter);
		
		$url_query = $this->url->get_query_exclude('category_id');
		
		foreach ($categories as &$category) {
			$category['actions'] = array(
				'edit' => array(
					'text' => $this->_('text_edit'),
					'href' => $this->url->link('catalog/category/update', 'category_id=' . $category['category_id'])
				),
				'delete' => array(
					'text' => $this->_('text_delete'),
					'href' => $this->url->link('catalog/category/delete', 'category_id=' . $category['category_id'] . '&' . $url_query)
				)
			);
			
			$category['thumb'] = $this->image->resize($category['image'], $this->config->get('config_image_admin_list_width'), $this->config->get('config_image_admin_list_height'));
			
			$category['stores'] = $this->Model_Catalog_Category->getCategoryStores($category['category_id']);
		} unset($category);
		
		//The table template data
		$tt_data = array(
			'row_id'		=> 'category_id',
			'route'		=> 'catalog/category',
			'sort'		=> $sort_filter['sort'],
			'order'		=> $sort_filter['order'],
			'page'		=> $sort_filter['page'],
			'sort_url'	=> $this->url->link('catalog/category', $this->url->get_query('filter')),
			'columns'	=> $columns,
			'data'		=> $categories,
		);
		
		$tt_data += $this->language->data;
		
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
		
		$this->data['batch_update'] = html_entity_decode($this->url->link('catalog/category/batch_update', $url_query));
		
		//Action Buttons
		$this->data['insert'] = $this->url->link('catalog/category/insert');
		
		//Item Limit Menu
		$this->data['limits'] = $this->sort->render_limit();
		
		//Pagination
		$this->pagination->init();
		$this->pagination->total = $category_total;
		
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
		$this->document->setTitle($this->_('heading_title'));
		
		$this->template->load('catalog/category_form');

		$category_id = $this->data['category_id'] = isset($_GET['category_id'])?$_GET['category_id']:null;
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('catalog/category'));
		
		if (!$category_id) {
			$this->data['action'] = $this->url->link('catalog/category/insert');
		} else {
			$this->data['action'] = $this->url->link('catalog/category/update', 'category_id=' . $category_id);
		}
		
		$this->data['cancel'] = $this->url->link('catalog/category');

		if ($category_id && !$this->request->isPost()) {
			$category_info = $this->Model_Catalog_Category->getCategory($category_id);
			
			$category_info['stores'] = $this->Model_Catalog_Category->getCategoryStores($category_id);
			$category_info['layouts'] = $this->Model_Catalog_Category->getCategoryLayouts($category_id);
		}
		
		//Set Default Values
		$defaults = array(
			'parent_id'	=> 0,
			'name'		=> '',
			'description' => '',
			'meta_keywords' => '',
			'meta_description' => '',
			'keyword'	=> '',
			'image'		=> '',
			'sort_order'=> 0,
			'status'		=> 1,
			'layouts'	=> array(),
			'stores'		=> array(0),
		);

		foreach ($defaults as $d => $default) {
			if (isset($_POST[$d])) {
				$this->data[$d] = $_POST[$d];
			} elseif (isset($category_info[$d])) {
				$this->data[$d] = $category_info[$d];
			} else {
				$this->data[$d] = $default;
			}
		}
		
		$categories = $this->Model_Catalog_Category->getCategoriesWithParents();
		
		// Remove own id from list
		foreach ($categories as $key => $category) {
			if ($category['category_id'] === $category_id) {
				unset($categories[$key]);
				break;
			}
		}
		
		$this->data['data_categories'] = array_merge(array(0 => $this->_('text_none')), $categories);
		
		$this->data['data_stores'] = $this->Model_Setting_Store->getStores();
		
		$this->data['data_layouts'] = array('' => '') + $this->Model_Design_Layout->getLayouts();
		
		$translate_fields = array(
			'name',
			'meta_keywords',
			'meta_description',
			'description',
		);
		
		$this->data['translations'] = $this->translation->get_translations('category', $category_id, $translate_fields);
		
		$this->data['breadcrumbs'] = $this->breadcrumb->render();
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	public function generate_url()
	{
		if (!empty($_POST['name'])) {
			$category_id = isset($_POST['category_id']) ? $_POST['category_id'] : 0;
			
			$url = $this->Model_Catalog_Category->generate_url($category_id, $_POST['name']);
		}
		else {
			$url = '';
		}

		$this->response->setOutput(json_encode($url));
	}
	
	private function validateForm()
	{
		if (!$this->user->hasPermission('modify', 'catalog/category')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		if (!$this->validation->text($_POST['name'], 2, 64)) {
			$this->error['name'] = $this->_('error_name');
		}
		
		return $this->error ? false : true;
	}

	private function validateDelete()
	{
		if (!$this->user->hasPermission('modify', 'catalog/category')) {
			$this->error['warning'] = $this->_('error_permission');
		}
 
		return $this->error ? false : true; 
	}
}
