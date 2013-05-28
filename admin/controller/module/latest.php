<?php
class ControllerModuleLatest extends Controller {
	
	
	public function index() {
		$this->template->load('module/latest');

		$this->load->language('module/latest');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('latest', $_POST);
			
			$this->cache->delete('product');
			
			$this->message->add('success', $this->_('text_success'));
						
			$this->url->redirect($this->url->link('extension/module'));
		}
				
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->error['image'])) {
			$this->data['error_image'] = $this->error['image'];
		} else {
			$this->data['error_image'] = array();
		}
		
			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_module'), $this->url->link('extension/module'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('module/latest'));

		$this->data['action'] = $this->url->link('module/latest');
		
		$this->data['cancel'] = $this->url->link('extension/module');

		$this->data['modules'] = array();
		
		if (isset($_POST['latest_module'])) {
			$this->data['modules'] = $_POST['latest_module'];
		} elseif ($this->config->get('latest_module')) {
			$this->data['modules'] = $this->config->get('latest_module');
		}
				
		$this->data['layouts'] = $this->model_design_layout->getLayouts();

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/latest')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		if (isset($_POST['latest_module'])) {
			foreach ($_POST['latest_module'] as $key => $value) {
				if (!$value['image_width'] || !$value['image_height']) {
					$this->error['image'][$key] = $this->_('error_image');
				}
			}
		}
				
		return $this->error ? false : true;
	}
}