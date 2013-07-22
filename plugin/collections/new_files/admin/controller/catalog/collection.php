<?php
class Admin_Controller_Catalog_Collection extends Controller
{
	public function index()
	{
		$this->language->load('catalog/collection');

		$this->getList();
	}

	public function insert()
	{
		$this->language->load('catalog/collection');

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Catalog_Collection->addCollection($_POST);
			
			if (!$this->message->error_set()) {
				$this->message->add('success',$this->_('text_success'));
				
				$this->url->redirect($this->url->link('catalog/collection'));
			}
		}
	
		$this->getForm();
	}

	public function update()
	{
		$this->language->load('catalog/collection');

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Catalog_Collection->editCollection($_GET['collection_id'], $_POST);

			if (!$this->message->error_set()) {
				$this->message->add('success',$this->_('text_success'));
				
				$this->url->redirect($this->url->link('catalog/collection'));
			}
		}
	
		$this->getForm();
	}

	public function delete()
	{
		$this->language->load('catalog/collection');
		
		if (!empty($_GET['collection_id']) && $this->validateDelete()) {
			$this->Model_Catalog_Collection->deleteCollection($_GET['collection_id']);
			
			if (!$this->message->error_set()) {
				$this->message->add('success',$this->_('text_success'));
				
				$this->url->redirect($this->url->link('catalog/collection'));
			}
		}

		$this->getList();
	}
	
	public function batch_update()
	{
		$this->language->load('catalog/collection');
		
		if (isset($_POST['selected']) && isset($_GET['action'])) {
			foreach ($_POST['selected'] as $collection_id) {
				switch($_GET['action']){
					case 'enable':
						$this->Model_Catalog_Collection->update_field($collection_id, array('status' => 1));
						break;
					case 'disable':
						$this->Model_Catalog_Collection->update_field($collection_id, array('status' => 0));
						break;
					case 'delete':
						$this->Model_Catalog_Collection->deleteCollection($collection_id);
						break;
					case 'copy':
						$this->Model_Catalog_Collection->copyCollection($collection_id);
						break;
				}
				if($this->error)
					break;
			}
			
			if (!$this->error) {
				if (!$this->message->error_set()) {
					$this->message->add('success',$this->_('text_success'));
					
					$this->url->redirect($this->url->link('catalog/collection'));
				}
			}
		}

		$this->getList();
	}

	private function getList()
	{
		//Page Title
		$this->document->setTitle($this->_('heading_title'));
		
		//The Template
		$this->template->load('catalog/collection_list');

		//Breadcrumbs
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('catalog/collection'));
		
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
		
		$columns['categories'] = array(
			'type' => 'multiselect',
			'display_name' => $this->_('column_category'),
			'filter' => true,
			'build_config' => array('category_id' , 'name'),
			'build_data' => $this->Model_Catalog_Category->getCategoriesWithParents(),
			'sortable' => false,
		);
		
		$columns['stores'] = array(
			'type' => 'multiselect',
			'display_name' => $this->_('column_store'),
			'filter' => true,
			'build_config' => array('store_id' , 'name'),
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
		
		$collection_total = $this->Model_Catalog_Collection->getTotalCollections($sort);
		$collections = $this->Model_Catalog_Collection->getCollections($sort + $filter);
		
		$url_query = $this->url->getQueryExclude('collection_id');
		
		foreach ($collections as &$collection) {
			$collection['actions'] = array(
				'edit' => array(
					'text' => $this->_('text_edit'),
					'href' => $this->url->link('catalog/collection/update', 'collection_id=' . $collection['collection_id'])
				),
				'delete' => array(
					'text' => $this->_('text_delete'),
					'href' => $this->url->link('catalog/collection/delete', 'collection_id=' . $collection['collection_id'] . $url_query)
				)
			);
			
			$collection['thumb'] = $this->image->resize($collection['image'], $this->config->get('config_image_admin_list_width'), $this->config->get('config_image_admin_list_height'));
			
			$collection['categories'] = $this->Model_Catalog_Collection->getCollectionCategories($collection['collection_id']);
			
			$collection['stores'] = $this->Model_Catalog_Collection->getCollectionStores($collection['collection_id']);
		} unset($collection);
		
		//Build The Table
		$tt_data = array(
			'row_id'		=> 'collecion_id',
		);
		
		$this->table->init();
		$this->table->setTemplate('table/list_view');
		$this->table->setColumns($columns);
		$this->table->setRows($collections);
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
			'delete' => array(
				'label' => $this->_('text_delete'),
			),
		);
		
		$this->data['batch_update'] = html_entity_decode($this->url->link('catalog/collection/batch_update', $url_query));
		
		//Render Limit Menu
		$this->data['limits'] = $this->sort->render_limit();
		
		//Action Buttons
		$this->data['insert'] = $this->url->link('catalog/collection/insert', $url_query);
		
		//Pagination
		$this->pagination->init();
		$this->pagination->total = $collection_total;
		
		$this->data['pagination'] = $this->pagination->render();
		
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
		$this->document->setTitle($this->_('heading_title'));
		
		$this->language->load('catalog/collection');
		
		$this->template->load('catalog/collection_form');

		$collection_id = isset($_GET['collection_id']) ? $_GET['collection_id'] : null;
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('catalog/collection'));
		
		if (!$collection_id) {
			$this->data['action'] = $this->url->link('catalog/collection/insert');
		} else {
			$this->data['action'] = $this->url->link('catalog/collection/update', 'collection_id=' . $collection_id);
		}
		
		$this->data['cancel'] = $this->url->link('catalog/collection');

		if ($collection_id && !$this->request->isPost()) {
			$collection_info = $this->Model_Catalog_Collection->getCollection($collection_id);
			
			$collection_info['products'] = $this->Model_Catalog_Collection->getCollectionProducts($collection_id);
			$collection_info['categories'] = $this->Model_Catalog_Collection->getCollectionCategories($collection_id);
			$collection_info['stores'] = $this->Model_Catalog_Collection->getCollectionStores($collection_id);
		}
		
		//initialize the values in order of Post, Database, Default
		$defaults = array(
			'name' => '',
			'keyword' => '',
			'image' => '',
			'meta_keywords' => '',
			'meta_description' => '',
			'description' => '',
			'products' => array(),
			'categories' => array(),
			'stores' => array(0),
			'status' => 1,
			'sort_order' => 0,
		);

		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$this->data[$key] = $_POST[$key];
			} elseif (isset($collection_info[$key])) {
				$this->data[$key] = $collection_info[$key];
			} else {
				$this->data[$key] = $default;
			}
		}
		
		//Image
		$this->data['thumb'] = $this->image->resize($this->data['image'], $this->config->get('config_image_admin_width'), $this->config->get('config_image_admin_height'));
		
		$this->data['data_categories'] = $this->Model_Catalog_Category->getCategoriesWithParents();
		
		$this->data['data_stores'] = $this->Model_Setting_Store->getStores();
		
		$translate_fields = array(
			'name',
			'meta_keywords',
			'meta_description',
			'description',
		);
		
		$this->data['translations'] = $this->translation->get_translations('collection', $collection_id, $translate_fields);
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->response->setOutput($this->render());
	}

	private function validateForm()
	{
		if (!$this->user->hasPermission('modify', 'catalog/collection')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		if (!isset($_POST['stores'])) {
			$_POST['stores'] = array('');
		}
		
		$collection_id = isset($_GET['collection_id']) ? $_GET['collection_id'] : 0;
		
		$name = $_POST['name'];
		
		$query = $this->db->query("SELECT COUNT(*) as total FROM " . DB_PREFIX . "collection WHERE name = '$name' AND collection_id != $collection_id");
	
		if ($query->row['total']) {
			$this->error['name'] = $this->_('error_duplicate_name', $name);
		}
		
		if (!$this->validation->text($name, 3, 63)) {
			$this->error['name'] = $this->_('error_name');
		}
		
		if (!$this->error) {
			if (empty($_POST['keyword'])) {
				$_POST['keyword'] = $this->tool->get_slug($name);
			} else {
				$_POST['keyword'] = $this->tool->get_slug($_POST['keyword']);
			}
		}
		
		return $this->error ? false : true;
	}

	private function validateDelete()
	{
		if (!$this->user->hasPermission('modify', 'catalog/collection')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		return $this->error ? false : true;
	}
}
