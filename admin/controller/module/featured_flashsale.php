<?php
class ControllerModuleFeaturedFlashsale extends Controller {
	 
	
	public function index() {   
		$this->template->load('module/featured_flashsale');

		$this->load->language('module/featured_flashsale');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate()) {
         unset($_POST['designer']);
         unset($_POST['choose_product']);

			$this->model_setting_setting->editSetting('featured_flashsale', $_POST);	
			
			$this->message->add('success', $this->_('text_success'));
			
			$this->redirect($this->url->link('module/featured_flashsale'));
		}
	   
      $this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
      $this->breadcrumb->add($this->_('text_module'), $this->url->link('extension/module'));
      $this->breadcrumb->add($this->_('heading_title'), $this->url->link('module/featured_flashsale'));
      
		$this->data['action'] = $this->url->link('module/featured_flashsale');
		$this->data['cancel'] = $this->url->link('extension/module');

		if (isset($_POST['featured_flashsale'])) {
			$ff = $_POST['featured_flashsale'];
		} else {
			$ff = $this->model_setting_setting->getSetting('featured_flashsale');
		}
      
		$this->data['featured_list'] = isset($ff['featured_list'])?$ff['featured_list']:array();
      
	   $this->data['modules'] = isset($ff['featured_flashsale_module'])?$ff['featured_flashsale_module']:array();
		
      
      $designers = $this->model_catalog_manufacturer->getManufacturers();

      foreach($designers as $d)
         $this->data['designers'][$d['manufacturer_id']] = $d['name'];
      
		$layouts = $this->model_design_layout->getLayouts();
		$this->data['layouts'] = array();
		foreach($layouts as $layout)
			$this->data['layouts'][$layout['layout_id']] = $layout['name']; 
      
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->response->setOutput($this->render());
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/featured_flashsale')) {
			$this->error['warning'] = $this->_('error_permission');
		}
				
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}
