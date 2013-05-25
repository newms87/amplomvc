<?php
class ControllerBlockBlock extends Controller {
	private $block_controller;
	
	public function index() {
		$this->load->language('block/block');

		$this->document->setTitle($this->_('heading_title'));
		
		if(isset($_GET['name'])){
				
			if(!$this->model_block_block->is_block($_GET['name'])){
				$this->message->add('warning', $this->_('error_unknown_block'));
				
				$this->url->redirect($this->url->link('block/block'));
			}
			
			$this->getForm();
		}
		else{
			$this->getList();
		}
	}
	
	private function getList(){
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
		
		//The Sort data
		$data = array();
		
		$sort_defaults = array(
			'sort' => 'name',
			'order' => 'ASC',
			'limit' => $this->config->get('config_admin_limit'),
			'page' => 1,
		);
		
		foreach($sort_defaults as $key => $default){
			$data[$key] = $$key = isset($_GET[$key]) ? $_GET[$key] : $default;
		}
		
		$data['start'] = ($page - 1) * $limit;
		
		//Filter
		$filter_values = !empty($_GET['filter']) ? $_GET['filter'] : array();
		
		if($filter_values){
			$data += $filter_values;
		}
		
		//Table Row Data
		$block_total = $this->model_block_block->getTotalBlocks($data);
		$blocks = $this->model_block_block->getBlocks($data);
		
		foreach ($blocks as &$block) {
			$actions = array(
				'edit' => array(
					'text' => $this->_('text_edit'),
					'href' => $this->url->link('block/block', 'name=' . $block['name'])
				),
			);
			
			$block['actions'] = $actions;
		}
		
		//The table template data
		$tt_data = array(
			'row_id'		=> 'name',
			'route'		=> 'block/block',
			'sort'		=> $sort,
			'order'		=> $order,
			'page'		=> $page,
			'sort_url'	=> $this->url->link('block/block', $this->url->get_query('filter')),
			'columns'	=> $columns,
			'data'		=> $blocks,
		);
		
		$tt_data += $this->language->data;
		
		//Build the table template
		$this->mytable->init();
		$this->mytable->set_template('table/list_view');
		$this->mytable->set_template_data($tt_data);
		$this->mytable->map_attribute('filter_value', $filter_values);
		
		$this->data['list_view'] = $this->mytable->build();
		
		//Action Buttons
		$this->data['insert'] = $this->url->link('block/block', 'name=new');
		
		//Pagination
		$url_query = $this->url->get_query('filter', 'sort', 'order');
		
		$this->pagination->init();
		$this->pagination->total = $block_total;
		$this->pagination->limit = $limit;
		$this->pagination->page = $page;
		$this->pagination->url = $this->url->link('block/block', $url_query);
		
		$this->data['pagination'] = $this->pagination->render();
		
		//Template Children
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		//Render
		$this->response->setOutput($this->render());
	}
	
	private function getForm(){
		$this->template->load('block/block');
		
		$name = $_GET['name'];
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_block_block->updateBlock($name, $_POST);
			
			$this->message->add('success', $this->_('text_success'));
			
			$this->url->redirect($this->url->link('block/block'));
		}
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_block_list'), $this->url->link('block/block'));
		
		$this->language->load('block/' . $name);
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('block/block','name=' . $name));

		$this->data['action'] = $this->url->link('block/block','name=' . $name);
		
		$this->data['cancel'] = $this->url->link('block/block');

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$this->data['profiles'] = isset($_POST['profiles']) ? $_POST['profiles'] : array();
			$this->data['settings'] = isset($_POST['settings']) ? $_POST['settings'] : array();
		} else {
			$block = $this->model_block_block->getBlock($name);
			
			if(!empty($block)){
				$this->data['profiles'] = $block['profiles'];
				$this->data['settings'] = $block['settings'];
			}
		}
		
		if(empty($this->data['settings'])){
			$this->data['settings'] = array();
		}
		
		if(empty($this->data['profiles'])){
			$this->data['profiles'] = array();
		}
		
		
		$defaults = array(
			'status' => 1,
		);
		
		foreach($defaults as $key => $default){
			if(isset($_POST[$key])){
				$this->data[$key] = $_POST[$key];
			}
			elseif(isset($block[$key])){
				$this->data[$key] = $block[$key];
			}
			else{
				$this->data[$key] = $default;
			}
		}
		
		//Get additional Block settings and profile data (this is the plugin part)
		$this->load_block_data();
		
		$this->data['data_stores'] = $this->model_setting_store->getStores();
		$this->data['data_layouts'] = $this->model_design_layout->getLayouts();
		$this->data['data_positions'] = array('' => $this->_('text_none')) + $this->theme->get_setting('data_positions');
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->response->setOutput($this->render());
	}
	
	private function load_block_controller(){
		if($this->block_controller) return;
		
		$path = $_GET['name'];
		$file = DIR_APPLICATION . 'controller/block/' . $path . '.php';
		$class = "ControllerBlock" . preg_replace("/[^A-Z0-9]/i",'',$path);
		$class_path = 'block/' . $path;
	
		if (file_exists($file)) {
			_require_once($file);

			$this->block_controller = new $class($class_path, $this->registry);
		} else {
			trigger_error('Error: Could not load block controller ' . $path . '!');
			exit();
		}
	}
	
	private function load_block_data(){
		$this->load_block_controller();
		
		$method = 'settings';
		if(method_exists($this->block_controller, $method)){
			$this->block_controller->$method($this->data['settings']);
			$this->data['extend_settings'] = $this->block_controller->output;
		}
		
		$method = 'profile';
		if(method_exists($this->block_controller, $method)){
			$this->block_controller->$method($this->data['profiles']);
			$this->data['extend_profile'] = $this->block_controller->output;
		}
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'block/block')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		$this->validate_block_data();
		
		return $this->error ? false : true;
	}
	
	private function validate_block_data(){
		$this->load_block_controller();
		
		$method = 'validate';
		
		if(method_exists($this->block_controller, $method)){
			$this->error += $this->block_controller->$method();
		}
	}
}
