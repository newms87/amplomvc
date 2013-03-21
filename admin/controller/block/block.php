<?php
class ControllerBlockBlock extends Controller {
	private $block_controller;
	
	public function index() {
		$this->load->language('block/block');

		$this->document->setTitle($this->_('heading_title'));
		
		if(isset($_GET['name'])){
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
      
		//This table column data is in admin/view/template_option/[template]/design/navigation_list.to
      $table = $this->template->get_table('listview');
      
		//Add table filter data	
		$table->set_column_filter('status', 'select', $this->_('data_statuses_blank'));
		
		//Add Table Cell Data array
		$table->set_column_cell_data('status', 'map', $this->_('data_statuses'));
		
      $table->set_template('table/sort_filter_list');
      
		//Add Sorting / Paging information to the table and the $data query
      $data_list = array(
         'sort'  =>'name',
         'order' =>'ASC',
         'page'  =>1
      );
      
      $data = array();
      
      foreach($data_list as $key => $default){
         if(isset($_GET[$key])){
            $data[$key] = $_GET[$key];
         }
         else{
            $data[$key] = $default;
         }
      }
      
      $table->add_extra_data($data);
      
		//Set the filter value if set by user
      if(isset($_GET['filter'])){
         foreach($_GET['filter'] as $filter => $value){
            $data[$filter] = $value;
            $table->set_column_filter_value($filter, $value);
         }
      }
      
		//Filter Query is for saving the previous filter and adding to the URL query string
      $queries = array(
         'filter_query' => $this->url->get_query('filter'),
      ); 
      
      $table->add_extra_data($queries);
      
		//finish adding $data query information
      $data['limit'] = $this->config->get('config_admin_limit');
      $data['start'] = ($data['page'] - 1) * $data['limit'];
      
		
		//Retrieve the Filtered Table row data
      $blocks = $this->model_block_block->getBlocks($data);
      
      foreach ($blocks as &$block) {
      	$this->language->load('block/' . $block['name']);
			
			$block['display_name'] = $this->_('heading_title');
			
         $action = array(
            'text' => $this->_('text_edit'),
            'href' => $this->url->link('block/block', 'name=' . $block['name'])
         );
         
         $block['action'] = $action;
      }
      
		//This sets the table row data
      $table->set_table_data($blocks);
      
      $this->data['block_list'] = $table->build();
      
      $this->data['insert'] = $this->url->link('block/block', 'name=new');
      $this->data['delete'] = $this->url->link('block/block/delete');
      
      $this->children = array(
         'common/header',
         'common/footer'
      );
      
      $this->response->setOutput($this->render());
	}
	
	private function getForm(){
		$this->template->load('block/block');
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_block_block->updateBlock($_GET['name'], $_POST);
			
			$this->message->add('success', $this->_('text_success'));
			
			$this->redirect($this->url->link('block/block'));
		}
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_block_list'), $this->url->link('block/block'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('block/block','name=' . $_GET['name']));

		$this->data['action'] = $this->url->link('block/block','name=' . $_GET['name']);
		
		$this->data['cancel'] = $this->url->link('block/block');

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$this->data['profiles'] = isset($_POST['profiles']) ? $_POST['profiles'] : array();
			$this->data['settings'] = isset($_POST['settings']) ? $_POST['settings'] : array();
		} else {
			$block = $this->model_block_block->getBlock($_GET['name']);
			
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
		
		$this->data['stores'] = $this->model_setting_store->getStores();
		$this->data['layouts'] = $this->model_design_layout->getLayouts();
		$this->data['data_positions'] = array('' => $this->_('text_none')) + $this->_('data_positions');
		 
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
