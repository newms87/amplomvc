<?php
class ControllerModuleSlideshow extends Controller {
	
	
	public function index() {	
		$this->template->load('module/slideshow');

		$this->load->language('module/slideshow');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('slideshow', $_POST);		
					
			$this->message->add('success', $this->_('text_success'));
						
			$this->url->redirect($this->url->link('extension/module'));
		}
				
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->error['dimension'])) {
			$this->data['error_dimension'] = $this->error['dimension'];
		} else {
			$this->data['error_dimension'] = array();
		}
		
			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_module'), $this->url->link('extension/module'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('module/slideshow'));

		$this->data['action'] = $this->url->link('module/slideshow');
		
		$this->data['cancel'] = $this->url->link('extension/module');
		
		$this->data['modules'] = array();
		
		if (isset($_POST['slideshow_module'])) {
			$this->data['modules'] = $_POST['slideshow_module'];
		} elseif ($this->config->get('slideshow_module')) { 
			$this->data['modules'] = $this->config->get('slideshow_module');
		}	
						
		$this->data['layouts'] = $this->model_design_layout->getLayouts();

		$this->data['banners'] = $this->model_design_banner->getBanners();
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/slideshow')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		if (isset($_POST['slideshow_module'])) {
			foreach ($_POST['slideshow_module'] as $key => $value) {
				if (!$value['width'] || !$value['height']) {
					$this->error['dimension'][$key] = $this->_('error_dimension');
				}				
			}
		}	
						
		return $this->error ? false : true;	
	}
}