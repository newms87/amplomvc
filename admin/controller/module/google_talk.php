<?php
class ControllerModuleGoogleTalk extends Controller {
	 
	
	public function index() {   
		$this->template->load('module/google_talk');

		$this->load->language('module/google_talk');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('google_talk', $_POST);		
					
			$this->message->add('success', $this->_('text_success'));
						
			$this->redirect($this->url->link('extension/module'));
		}
				
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
 		if (isset($this->error['code'])) {
			$this->data['error_code'] = $this->error['code'];
		} else {
			$this->data['error_code'] = '';
		}
		
			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_module'), $this->url->link('extension/module'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('module/google_talk'));

		$this->data['action'] = $this->url->link('module/google_talk');
		
		$this->data['cancel'] = $this->url->link('extension/module');

		if (isset($_POST['google_talk_code'])) {
			$this->data['google_talk_code'] = $_POST['google_talk_code'];
		} else {
			$this->data['google_talk_code'] = $this->config->get('google_talk_code');
		}	
		
		$this->data['modules'] = array();
		
		if (isset($_POST['google_talk_module'])) {
			$this->data['modules'] = $_POST['google_talk_module'];
		} elseif ($this->config->get('google_talk_module')) { 
			$this->data['modules'] = $this->config->get('google_talk_module');
		}			
				
		$this->data['layouts'] = $this->model_design_layout->getLayouts();

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/google_talk')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		if (!$_POST['google_talk_code']) {
			$this->error['code'] = $this->_('error_code');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}