<?php 
class ControllerCatalogCategory extends Controller { 
	
 
	public function index() {
		$this->load->language('catalog/category');

		$this->document->setTitle($this->_('heading_title'));
		
		$this->getList();
	}

	public function insert() {
		$this->load->language('catalog/category');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_category->addCategory($_POST);

			$this->message->add('success', $this->_('text_success'));
			
			$this->url->redirect($this->url->link('catalog/category')); 
		}

		$this->getForm();
	}

	public function update() {
		$this->load->language('catalog/category');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_category->editCategory($_GET['category_id'], $_POST);
			
			$this->message->add('success', $this->_('text_success'));
			
			$this->url->redirect($this->url->link('catalog/category'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('catalog/category');

		$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_POST['selected']) && $this->validateDelete()) {
			foreach ($_POST['selected'] as $category_id) {
				$this->model_catalog_category->deleteCategory($category_id);
			}

			$this->message->add('success', $this->_('text_success'));

			$this->url->redirect($this->url->link('catalog/category'));
		}

		$this->getList();
	}

	private function getList() {
		$this->template->load('catalog/category_list');

		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('catalog/category'));
											
		$this->data['insert'] = $this->url->link('catalog/category/insert');
		$this->data['delete'] = $this->url->link('catalog/category/delete');
		
		$this->data['categories'] = array();
		
		$results = $this->model_catalog_category->getCategories(0);

		foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->_('text_edit'),
				'href' => $this->url->link('catalog/category/update', 'category_id=' . $result['category_id'])
			);
					
			$this->data['categories'][] = array(
				'category_id' => $result['category_id'],
				'name'		=> $result['name'],
				'sort_order'  => $result['sort_order'],
				'selected'	=> isset($_POST['selected']) && in_array($result['category_id'], $_POST['selected']),
				'action'		=> $action
			);
		}
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function getForm() {
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

		if ($category_id && ($_SERVER['REQUEST_METHOD'] != 'POST')) {
			$category_info = $this->model_catalog_category->getCategory($category_id);
		}
		
		//initialize the values in order of Post, Database, Default
		$defaults = array(
			'category_description'=>array(),
			'parent_id'=>0,
			'category_store'=>array(0,1,2),
			'keyword'=>'',
			'image'=>'',
			'top'=>0,
			'column'=>1,
			'sort_order'=>0,
			'status'=>1,
			'category_layout'=>array(),
		);

		foreach($defaults as $d=>$value){
			if (isset($_POST[$d])) {
				$this->data[$d] = $_POST[$d];
			} elseif (isset($category_info[$d])) {
				$this->data[$d] = $category_info[$d];
			} elseif(!$category_id) {
				$this->data[$d] = $value;
			}
		}
		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();

		//Anything uninitialized at this point we know there is a category_id set, so grab the values
		if(!isset($this->data['category_description'])){
			$this->data['category_description'] = $this->model_catalog_category->getCategoryDescriptions($category_id);
		}
		if(!isset($this->data['category_store'])) {
			$this->data['category_store'] = $this->model_catalog_category->getCategoryStores($category_id);
		}
		if(!isset($this->data['category_layout'])){
			$this->data['category_layout'] = $this->model_catalog_category->getCategoryLayouts($category_id);
		}
		
		$this->data['categories'] = array(0=>'-- None --');
		$categories = $this->model_catalog_category->getCategories(0);
		// Remove own id from list
		foreach ($categories as $key => $category) {
			if ($category['category_id'] != $category_id) {
				$this->data['categories'][$category['category_id']] = $category['name'];
			}
		}
						
		$this->data['data_stores'] = $this->model_setting_store->getStores();
		
		$this->data['data_layouts'] = array('' => '') + $this->model_design_layout->getLayouts();
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	public function generate_url(){
		if(!empty($_POST['name'])){
			$category_id = isset($_POST['category_id']) ? $_POST['category_id'] : 0;
			
			$url = $this->model_catalog_category->generate_url($category_id, $_POST['name']);
		}
		else{
			$url = '';
		}

		$this->response->setOutput(json_encode($url));
	}
	
	private function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/category')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		foreach ($_POST['category_description'] as $language_id => $value) {
			if ((strlen($value['name']) < 2) || (strlen($value['name']) > 255)) {
				$this->error["category_description[$language_id][name]"] = $this->_('error_name');
			}
		}

		return $this->error ? false : true;
	}

	private function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/category')) {
			$this->error['warning'] = $this->_('error_permission');
		}
 
		if (!$this->error) {
			return true; 
		} else {
			return false;
		}
	}
}
