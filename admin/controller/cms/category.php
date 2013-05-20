<?php 
class ControllerCmsCategory extends Controller { 
	
	public function index() {
		$this->load->language('cms/category');

		$this->document->setTitle($this->_('heading_title'));
		
		$this->getList();
	}

	public function insert() {
		$this->load->language('cms/category');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_cms_category->addCategory($_POST);

			$this->message->add('success', $this->_('text_success'));
			
			$this->url->redirect($this->url->link('cms/category')); 
		}

		$this->getForm();
	}

	public function update() {
		$this->load->language('cms/category');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_cms_category->editCategory($_GET['cms_category_id'], $_POST);
			
			$this->message->add('success', $this->_('text_success'));
			
			$this->url->redirect($this->url->link('cms/category'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('cms/category');

		$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_POST['selected']) && $this->validateDelete()) {
			foreach ($_POST['selected'] as $cms_category_id) {
				$this->model_cms_category->deleteCategory($cms_category_id);
			}

			$this->message->add('success', $this->_('text_success'));

			$this->url->redirect($this->url->link('cms/category'));
		}

		$this->getList();
	}

	private function getList() {
		$this->template->load('cms/category_list');

	   $this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
      $this->breadcrumb->add($this->_('heading_title'), $this->url->link('cms/category'));
      									
		$this->data['insert'] = $this->url->link('cms/category/insert');
		$this->data['delete'] = $this->url->link('cms/category/delete');
		
		$this->data['categories'] = array();
		
		$results = $this->model_cms_category->getCategories(0);

		foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->_('text_edit'),
				'href' => $this->url->link('cms/category/update', 'cms_category_id=' . $result['cms_category_id'])
			);
					
			$this->data['categories'][] = array(
				'cms_category_id' => $result['cms_category_id'],
				'name'        => $result['name'],
				'sort_order'  => $result['sort_order'],
				'selected'    => isset($_POST['selected']) && in_array($result['cms_category_id'], $_POST['selected']),
				'action'      => $action
			);
		}
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function getForm() {
		$this->template->load('cms/category_form');

	   $cms_category_id = isset($_GET['cms_category_id'])?$_GET['cms_category_id']:null;
	   
	   $this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
      $this->breadcrumb->add($this->_('heading_title'), $this->url->link('cms/category'));
      
		if (!$cms_category_id) {
			$this->data['action'] = $this->url->link('cms/category/insert');
		} else {
			$this->data['action'] = $this->url->link('cms/category/update', 'cms_category_id=' . $cms_category_id);
		}
		
		$this->data['cancel'] = $this->url->link('cms/category');

		if ($cms_category_id && ($_SERVER['REQUEST_METHOD'] != 'POST')) {
      	$category_info = $this->model_cms_category->getCategory($cms_category_id);
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
                        'category_layout'=>array()
                        );

      foreach($defaults as $d=>$value){
         if (isset($_POST[$d])) {
            $this->data[$d] = $_POST[$d];
         } elseif (isset($category_info[$d])) {
            $this->data[$d] = $category_info[$d];
         } elseif(!$cms_category_id) {
            $this->data[$d] = $value;
         }
      }
      
		$this->data['languages'] = $this->model_localisation_language->getLanguages();

      //Anything uninitialized at this point we know there is a cms_category_id set, so grab the values
		if(!isset($this->data['category_description'])){
		   $this->data['category_description'] = $this->model_cms_category->getCategoryDescriptions($cms_category_id);
		}
      if(!isset($this->data['category_store'])) {
         $this->data['category_store'] = $this->model_cms_category->getCategoryStores($cms_category_id);
      }
      if(!isset($this->data['category_layout'])){
         $this->data['category_layout'] = $this->model_cms_category->getCategoryLayouts($cms_category_id);
      }
      
      if (!empty($category_info) && $category_info['image'] && file_exists(DIR_IMAGE . $category_info['image'])) {
         $this->data['thumb'] = $this->image->resize($category_info['image'], 100, 100);
      } else {
         $this->data['thumb'] = $this->image->resize('no_image.png', 100, 100);
      }
      
      $this->data['no_image'] = $this->image->resize('no_image.png', 100, 100);
      
      $this->data['categories'] = array(0=>'-- None --');
		$categories = $this->model_cms_category->getCategories(0);
		// Remove own id from list
		foreach ($categories as $key => $category) {
			if ($category['cms_category_id'] != $cms_category_id) {
			   $this->data['categories'][$category['cms_category_id']] = $category['name'];
			}
		}
						
		$this->data['stores'] = $this->model_setting_store->getStores();
		
		$layouts = $this->model_design_layout->getLayouts();
		foreach($layouts as $l)
         $this->data['layouts'][$l['layout_id']] = $l['name'];
      
      
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

   public function generate_url(){
      $name = isset($_POST['name'])?$_POST['name']:'';
      if(!$name)return;
      
      echo json_encode($this->model_cms_category->generate_url($name));
      exit;
   }
   
	private function validateForm() {
		if (!$this->user->hasPermission('modify', 'cms/category')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		foreach ($_POST['category_description'] as $language_id => $value) {
			if ((strlen($value['name']) < 2) || (strlen($value['name']) > 255)) {
				$this->error["category_description[$language_id][name]"] = $this->_('error_name');
			}
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	private function validateDelete() {
		if (!$this->user->hasPermission('modify', 'cms/category')) {
			$this->error['warning'] = $this->_('error_permission');
		}
 
		if (!$this->error) {
			return true; 
		} else {
			return false;
		}
	}
   
   private function get_url(){
      $url = '';
      $filters = array('sort', 'order', 'page');
      foreach($filters as $f)
         if (isset($_GET[$f]))
            $url .= "&$f=" . $_GET[$f];
      return $url;
   }
}