<?php
class Admin_Controller_Module_Information extends Controller 
{
	
	
	public function index()
	{
		$this->template->load('module/information');

		$this->language->load('module/information');

		$this->document->setTitle($this->_('heading_title'));
		
		if ($this->request->isPost() && $this->validate()) {
			$this->Model_Setting_Setting->editSetting('information', $_POST);
					
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
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('module/information'));

		$this->data['action'] = $this->url->link('module/information');
		
		$this->data['cancel'] = $this->url->link('extension/module');
				
		$this->data['modules'] = array();
		
		if (isset($_POST['information_module'])) {
			$this->data['modules'] = $_POST['information_module'];
		} elseif ($this->config->get('information_module')) {
			$this->data['modules'] = $this->config->get('information_module');
		}
		
		$this->data['layouts'] = $this->Model_Design_Layout->getLayouts();

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'module/information')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		return $this->error ? false : true;
	}
}