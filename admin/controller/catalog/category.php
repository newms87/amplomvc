<?php
class Admin_Controller_Catalog_Category extends Controller
{
	public function index()
	{
		$this->language->load('catalog/category');

		$this->getList();
	}

	public function update()
	{
		$this->language->load('catalog/category');

		if ($this->request->isPost() && $this->validateForm()) {
			//Insert
			if (empty($_GET['category_id'])) {
				$this->Model_Catalog_Category->addCategory($_POST);
			}
			//Update
			else {
				$this->Model_Catalog_Category->editCategory($_GET['category_id'], $_POST);
			}
			
			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));
			
				$this->url->redirect($this->url->link('catalog/category'));
			}
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->language->load('catalog/category');
		
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
				
				$this->url->redirect($this->url->link('catalog/category', $this->url->getQueryExclude('action')));
			}
		}

		$this->getList();
	}
	
	private function getList()
	{
		//Page Head
		$this->document->setTitle($this->_('heading_title'));
		
		//The Template
		$this->template->load('catalog/category_list');

		//Breadcrumbs
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
			'build_config' => array('store_id', 'name'),
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
		
		//Get Sorted / Filtered Data
		$sort = $this->sort->getQueryDefaults('name', 'ASC');
		$filter = !empty($_GET['filter']) ? $_GET['filter'] : array();
		
		$category_total = $this->Model_Catalog_Category->getTotalCategories($filter);
		$categories = $this->Model_Catalog_Category->getCategoriesWithParents($sort + $filter);
		
		$url_query = $this->url->getQueryExclude('category_id');
		$image_width = $this->config->get('config_image_admin_list_width');
		$image_height = $this->config->get('config_image_admin_list_height');
		
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
			
			$category['thumb'] = $this->image->resize($category['image'], $image_width, $image_height);
			
			$category['stores'] = $this->Model_Catalog_Category->getCategoryStores($category['category_id']);
		} unset($category);
		
		//Build The Table
		$tt_data = array(
			'row_id'		=> 'category_id',
		);
		
		$this->table->init();
		$this->table->setTemplate('table/list_view');
		$this->table->setColumns($columns);
		$this->table->setRows($categories);
		$this->table->setTemplateData($tt_data);
		$this->table->mapAttribute('filter_value', $filter);
		
		$this->data['list_view'] = $this->table->render();
		
		//Batch Actions
		$this->data['batch_actions'] = array(
			'enable'	=> array(
				'label' => $this->_('text_enable')
			),
			'disable'=>	array(
				'label' => $this->_('text_disable'),
			),
			'copy' => array(
				'label' => $this->_('text_copy'),
			),
			'delete' => array(
				'label' => $this->_('text_delete'),
			),
		);
		
		$this->data['batch_update'] = html_entity_decode($this->url->link('catalog/category/batch_update', $url_query));
		
		//Render Limit Menu
		$this->data['limits'] = $this->sort->render_limit();
		
		//Pagination
		$this->pagination->init();
		$this->pagination->total = $category_total;
		
		$this->data['pagination'] = $this->pagination->render();
		
		//Action Buttons
		$this->data['insert'] = $this->url->link('catalog/category/update');
		
		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		//Render
		$this->response->setOutput($this->render());
	}

	private function getForm()
	{
		//Page Head
		$this->document->setTitle($this->_('heading_title'));
		
		//The template
		$this->template->load('catalog/category_form');

		//Insert or Update
		$category_id = $this->data['category_id'] = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
		
		//Breadcrumbs
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('catalog/category'));
		
		if ($category_id) {
			$this->breadcrumb->add($this->_('text_edit'), $this->url->link('catalog/category/update', 'category_id=' . $category_id));
		} else {
			$this->breadcrumb->add($this->_('text_insert'), $this->url->link('catalog/category/update'));
		}
		
		//Load Information
		if ($category_id && !$this->request->isPost()) {
			$category_info = $this->Model_Catalog_Category->getCategory($category_id);
			
			$category_info['stores'] = $this->Model_Catalog_Category->getCategoryStores($category_id);
			$category_info['layouts'] = $this->Model_Catalog_Category->getCategoryLayouts($category_id);
		}
		
		//Set Values or Defaults
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

		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$this->data[$key] = $_POST[$key];
			} elseif (isset($category_info[$key])) {
				$this->data[$key] = $category_info[$key];
			} else {
				$this->data[$key] = $default;
			}
		}
		
		//All other categories to select parent
		$categories = $this->Model_Catalog_Category->getCategoriesWithParents();
		
		// Remove own id from list
		foreach ($categories as $key => $category) {
			if ($category['category_id'] === $category_id) {
				unset($categories[$key]);
				break;
			}
		}
		
		//Translations
		$this->data['translations'] = $this->Model_Catalog_Category->getCategoryTranslations($category_id);
		
		//Additional Data
		$this->data['data_categories'] = array_merge(array(0 => $this->_('text_none')), $categories);
		$this->data['data_stores'] = $this->Model_Setting_Store->getStores();
		$this->data['data_layouts'] = array('' => '') + $this->Model_Design_Layout->getLayouts();
		
		//Ajax Urls
		$this->data['url_generate_url'] = $this->url->ajax('catalog/category/generate_url');
		
		//Action Buttons
		$this->data['action'] = $this->url->link('catalog/category/update', 'category_id=' . $category_id);
		$this->data['cancel'] = $this->url->link('catalog/category');
		
		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		//Render
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
