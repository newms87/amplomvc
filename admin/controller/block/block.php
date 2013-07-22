<?php
class Admin_Controller_Block_Block extends Controller
{
	private $block_controller;
	
	public function index()
	{
		$this->language->load('block/block');

		$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_GET['name'])) {
				
			if (!$this->Model_Block_Block->is_block($_GET['name'])) {
				$this->message->add('warning', $this->_('error_unknown_block'));
				
				$this->url->redirect($this->url->link('block/block'));
			}
			
			$this->getForm();
		}
		else {
			$this->getList();
		}
	}
	
	private function getList()
	{
		$this->template->load('block/list');

		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('block/block'));
		
		//The Table Columns
		$columns = array();

		$columns['display_name'] = array(
			'type' => 'text',
			'display_name' => $this->_('column_display_name'),
			'filter' => true,
			'sortable' => true,
		);
		
		$columns['name'] = array(
			'type' => 'text',
			'display_name' => $this->_('column_name'),
			'filter' => true,
			'sortable' => true,
		);
		
		
		$columns['status'] = array(
			'type' => 'select',
			'display_name' => $this->_('column_status'),
			'filter' => true,
			'build_data' => $this->_('data_statuses'),
			'sortable' => true,
		);
		
		//The Sort & Filter Data
		$sort = $this->sort->getQueryDefaults('name', 'ASC');
		$filter = !empty($_GET['filter']) ? $_GET['filter'] : array();
		
		//Table Row Data
		$block_total = $this->Model_Block_Block->getTotalBlocks($filter);
		$blocks = $this->Model_Block_Block->getBlocks($sort + $filter);
		
		foreach ($blocks as &$block) {
			$actions = array(
				'edit' => array(
					'text' => $this->_('text_edit'),
					'href' => $this->url->link('block/block', 'name=' . $block['name'])
				),
			);
			
			$block['actions'] = $actions;
		}
		
		//Build The Table
		$tt_data = array(
			'row_id'		=> 'name',
		);
		
		$this->table->init();
		$this->table->setTemplate('table/list_view');
		$this->table->setColumns($columns);
		$this->table->setRows($blocks);
		$this->table->setTemplateData($tt_data);
		$this->table->mapAttribute('filter_value', $filter);
		
		$this->data['list_view'] = $this->table->render();
		
		//Action Buttons
		$this->data['insert'] = $this->url->link('block/add');
		
		//Render limit Menu
		$this->data['limits'] = $this->sort->render_limit();
		
		//Pagination
		$this->pagination->init();
		$this->pagination->total = $block_total;
		
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
		$this->template->load('block/block');
		
		$name = $_GET['name'];
		
		if ($this->request->isPost() && $this->validate()) {
			//If plugins have additional
			$this->saveBlockData();
			
			$this->Model_Block_Block->updateBlock($name, $_POST);
			
			$this->message->add('success', $this->_('text_success'));
			
			$this->url->redirect($this->url->link('block/block'));
		}
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_block_list'), $this->url->link('block/block'));
		
		$this->language->load('block/' . $name);
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('block/block','name=' . $name));

		$this->data['action'] = $this->url->link('block/block','name=' . $name);
		$this->data['cancel'] = $this->url->link('block/block');

		if (!$this->request->isPost()) {
			$block = $this->Model_Block_Block->getBlock($name);
		}
		
		$defaults = array(
			'settings' => array(),
			'profiles' => array(),
			'status' => 1,
		);
		
		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$this->data[$key] = $_POST[$key];
			} elseif (isset($block[$key])) {
				$this->data[$key] = $block[$key];
			} else {
				$this->data[$key] = $default;
			}
		}
		
		//Get additional Block settings and profile data (this is the plugin part)
		$this->loadBlockData();
		
		$sort_store = array(
			'sort' => 'name',
			'order' => 'ASC',
		);
		
		$this->data['data_stores'] = $this->Model_Setting_Store->getStores($sort_store);
		
		$sort_layout = array(
			'sort' => 'name',
			'order' => 'ASC',
		);
		
		$this->data['data_layouts'] = $this->Model_Design_Layout->getLayouts($sort_layout);
		
		$this->data['data_positions'] = array('' => $this->_('text_none')) + $this->theme->get_setting('data_positions');
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->response->setOutput($this->render());
	}
	
	private function loadBlockController()
	{
		if($this->block_controller || empty($_GET['name'])) return;
		
		$path = $_GET['name'];
		
		$action = new Action($this->registry, 'block/' . $path);
		
		$this->block_controller = $action->getController();
	}
	
	private function loadBlockData()
	{
		$this->loadBlockController();
		
		if (method_exists($this->block_controller, 'settings')) {
			$this->block_controller->settings($this->data['settings']);
			$this->data['extend_settings'] = $this->block_controller->output;
		}
				
		if (method_exists($this->block_controller, 'profile')) {
			$this->block_controller->profile($this->data['profiles']);
			$this->data['extend_profile'] = $this->block_controller->output;
		}
	}
	
	private function saveBlockData()
	{
		$this->loadBlockController();
		
		if (method_exists($this->block_controller, 'saveSettings')) {
			$this->block_controller->saveSettings($_POST['settings']);
		}
		
		if (method_exists($this->block_controller, 'saveProfile')) {
			$this->block_controller->saveProfile($_POST['profiles']);
		}
	}
	
	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'block/block')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		$this->validate_block_data();
		
		return $this->error ? false : true;
	}
	
	private function validate_block_data()
	{
		$this->loadBlockController();
		
		if (method_exists($this->block_controller, 'validate')) {
			$this->error += $this->block_controller->validate();
		}
	}
}
