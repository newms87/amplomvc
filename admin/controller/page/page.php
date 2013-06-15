<?php
class Admin_Controller_Page_Page extends Controller 
{
	
	public function index()
	{
		$this->load->language('page/page');

		$this->document->setTitle($this->_('heading_title'));
		
		$this->getList();
	}

	public function insert()
	{
		$this->load->language('page/page');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($this->request->isPost()) && $this->validateForm()) {
			$this->Model_Page_Page->addPage($_POST);
			
			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success_insert'));
			}
			
			$this->getList();
		}
		else {
			$this->getForm();
		}
	}

	public function update()
	{
		$this->load->language('page/page');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($this->request->isPost()) && $this->validateForm()) {
			$this->Model_Page_Page->editPage($_GET['page_id'], $_POST);
			
			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success_update'));
			}
			
			$this->getList();
		}
		else {
			$this->getForm();
		}
	}
 
	public function delete()
	{
		$this->load->language('page/page');
		$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_GET['page_id']) && $this->validateDelete()) {
			$this->Model_Page_Page->deletePage($_GET['page_id']);
			
			if (!$this->message->error_set()) {
				$this->message->add('notify', $this->_('text_success_delete'));
				
				$this->url->redirect($this->url->link('page/page'));
			}
		}

		$this->getList();
	}
	
	public function batch_update()
	{
		$this->language->load('page/page');
		
		if (isset($_POST['selected']) && isset($_GET['action'])) {
			foreach ($_POST['selected'] as $page_id) {
				switch($_GET['action']){
					case 'enable':
						$this->Model_Page_Page->update_field($page_id, array('status' => 1));
						break;
					case 'disable':
						$this->Model_Page_Page->update_field($page_id, array('status' => 0));
						break;
					case 'delete':
						$this->Model_Page_Page->deletePage($page_id);
						break;
					case 'copy':
						$this->Model_Page_Page->copyPage($page_id);
						break;
				}
				if($this->error)
					break;
			}
			
			if (!$this->error) {
				if (!$this->message->error_set()) {
					$this->message->add('success',$this->_('text_success'));
				}
			}
		}

		$this->index();
	}

	private function getList()
	{
		$this->template->load('page/page_list');

		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('page/page'));
		
		//The Table Columns
		$columns = array();
		
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
		$data = array();
		
		$sort_defaults = array(
			'sort' => 'name',
			'order' => 'ASC',
			'limit' => $this->config->get('config_admin_limit'),
			'page' => 1,
		);
		
		foreach ($sort_defaults as $key => $default) {
			$data[$key] = isset($_GET[$key]) ? $_GET[$key] : $default;
		}
		
		$data['start'] = ($data['page'] - 1) * $data['limit'];
		
		//Filter
		$filter = !empty($_GET['filter']) ? $_GET['filter'] : array();
		
		if ($filter) {
			$data += $filter;
		}
		
		//Retrieve the Filtered Table row data
		$page_total = $this->Model_Page_Page->getTotalPages($data);
		$pages = $this->Model_Page_Page->getPages($data);
		
		foreach ($pages as &$page) {
			$page['actions'] = array(
				'edit' => array(
					'text' => $this->_('text_edit'),
					'href' => $this->url->link('page/page/update', 'page_id=' . $page['page_id'])
				),
				'delete' => array(
					'text' => $this->_('text_delete'),
					'href' => $this->url->link('page/page/delete', 'page_id=' . $page['page_id'])
				)
			);
			
			$page['stores'] = $this->Model_Page_Page->getPageStores($page['page_id']);
		}unset($page);
		
		//The table template data
		$tt_data = array(
			'row_id'		=> 'page_id',
			'route'		=> 'page/page',
			'sort'		=> $data['sort'],
			'order'		=> $data['order'],
			'sort_url'	=> $this->url->link('page/page', $this->url->get_query('filter')),
			'columns'	=> $columns,
			'data'		=> $pages,
		);
		
		$tt_data += $this->language->data;
		
		//Build the table template
		$this->table->init();
		$this->table->set_template('table/list_view');
		$this->table->set_template_data($tt_data);
		$this->table->map_attribute('filter_value', $filter);
		
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
			'delete' => array(
				'label' => "Delete",
			),
		);
		
		$this->data['batch_update'] = html_entity_decode($this->url->link('page/page/batch_update', $url_query));
		
		//Action Buttons
		$this->data['insert'] = $this->url->link('page/page/insert', $url_query);
		
		//Pagination
		$url_query = $this->url->get_query('filter', 'sort', 'order');
		
		$this->pagination->init();
		$this->pagination->total = $page_total;
		$this->data['pagination'] = $this->pagination->render();
		
		//Template Children
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		//Render
		$this->response->setOutput($this->render());
	}

	private function getForm()
	{
		$this->language->load('page/page');
		
		$this->template->load('page/page_form');

		$page_id = isset($_GET['page_id']) ? $_GET['page_id'] : null;
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('page/page'));
		
		if (!$page_id) {
			$this->data['action'] = $this->url->link('page/page/insert');
		} else {
			$this->data['action'] = $this->url->link('page/page/update', 'page_id=' . $page_id);
		}
		
		$this->data['cancel'] = $this->url->link('page/page');

		if ($page_id && (!$this->request->isPost())) {
			$page_info = $this->Model_Page_Page->getPage($page_id);
			
			$page_info['stores'] = $this->Model_Page_Page->getPageStores($page_id);
		}
		
		//initialize the values in order of Post, Database, Default
		$defaults = array(
			'name' => '',
			'keyword' => '',
			'content' => '',
			'meta_keywords' => '',
			'meta_description' => '',
			'layout_id' => 0,
			'stores' => array(0),
			'status' => 1,
			'translations' => array(),
		);

		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$this->data[$key] = $_POST[$key];
			} elseif (isset($page_info[$key])) {
				$this->data[$key] = $page_info[$key];
			} else {
				$this->data[$key] = $default;
			}
		}
		
		if ($page_id) {
			$this->breadcrumb->add($this->data['name'], $this->url->link('page/page/update', 'page_id=' . $page_id));
		} else {
			$this->breadcrumb->add($this->_('text_new_page'), $this->url->link('page/page/insert'));
		}
		
		//Data
		$this->data['data_stores'] = $this->Model_Setting_Store->getStores();
		$this->data['data_layouts'] = $this->Model_Design_Layout->getLayouts();
		
		$this->data['url_create_layout'] = $this->url->link('page/page/create_layout');
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->response->setOutput($this->render());
	}
	
	public function create_layout()
	{
		if (!empty($_POST['name'])) {
			$layout = array(
				'name' => $_POST['name'],
			);
			
			$result = $this->Model_Design_Layout->getLayouts($layout);
			
			if (empty($result)) {
				$layout_id = $this->Model_Design_Layout->addLayout($layout);
			} else {
				$result = current($result);
				$layout_id = $result['layout_id'];
			}
		}
		
		$data = array(
			'sort' => 'name',
			'order' => "ASC",
		);
		
		$layouts = $this->Model_Design_Layout->getLayouts($data);
		
		$this->builder->set_config('layout_id', 'name');
		
		$this->response->setOutput($this->builder->build('select', $layouts, 'layout_id', $layout_id));
	}
	
	private function validateForm()
	{
		if (!$this->user->hasPermission('modify', 'page/page')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		if (!$this->validation->text($_POST['name'], 3, 64)) {
			$this->error['name'] = $this->_('error_name');
		}
		
		return $this->error ? false : true;
	}

	private function validateDelete()
	{
		if (!$this->user->hasPermission('modify', 'page/page')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		return $this->error ? false : true;
	}
}
