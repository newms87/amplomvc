<?php
class ControllerModuleSearchBar extends Controller {
	
	
	public function index() {
		$this->template->load('module/search_bar');

		$this->load->language('module/search_bar');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('search_bar', $_POST);
					
			$this->message->add('success', $this->_('text_success'));
						
			$this->url->redirect($this->url->link('extension/module'));
		}
				
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_module'), $this->url->link('extension/module'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('module/search_bar'));

		$this->data['action'] = $this->url->link('module/search_bar');
		
		$this->data['cancel'] = $this->url->link('extension/module');
		
		$this->data['modules'] = array();
		
		if (isset($_POST['search_bar_module'])) {
			$this->data['modules'] = $_POST['search_bar_module'];
		} elseif ($this->config->get('search_bar_module')) {
			$this->data['modules'] = $this->config->get('search_bar_module');
		}
				
		$this->data['layouts'] = $this->model_design_layout->getLayouts();

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/search_bar')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		return $this->error ? false : true;
	}
}