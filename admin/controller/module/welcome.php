<?php
class ControllerModuleWelcome extends Controller {
	 
	 
	public function index() {   
		$this->template->load('module/welcome');

		$this->load->language('module/welcome');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('welcome', $_POST);
			
			$this->message->add('success', $this->_('text_success'));
						
			$this->redirect($this->url->link('extension/module'));
		}
				
		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_module'), $this->url->link('extension/module'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('module/welcome'));

		$this->data['action'] = $this->url->link('module/welcome');
		
		$this->data['cancel'] = $this->url->link('extension/module');

		$this->data['modules'] = array();
		
		if (isset($_POST['welcome_module'])) {
			$this->data['modules'] = $_POST['welcome_module'];
		} elseif ($this->config->get('welcome_module')) { 
			$this->data['modules'] = $this->config->get('welcome_module');
		}	
				
		$this->data['layouts'] = $this->model_design_layout->getLayouts();
		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/welcome')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}