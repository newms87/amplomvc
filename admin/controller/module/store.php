<?php
class Admin_Controller_Module_Store extends Controller
{
	
	
	public function index()
	{
		$this->template->load('module/store');

		$this->language->load('module/store');

		$this->document->setTitle($this->_('head_title'));
		
		if ($this->request->isPost() && $this->validate()) {
			$this->Model_Setting_Setting->editSetting('store', $_POST);
					
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
			$this->breadcrumb->add($this->_('head_title'), $this->url->link('module/store'));

		$this->data['action'] = $this->url->link('module/store');
		
		$this->data['cancel'] = $this->url->link('extension/module');

		if (isset($_POST['store_admin'])) {
			$this->data['store_admin'] = $_POST['store_admin'];
		} else {
			$this->data['store_admin'] = $this->config->get('store_admin');
		}
			
		$this->data['modules'] = array();
		
		if (isset($_POST['store_module'])) {
			$this->data['modules'] = $_POST['store_module'];
		} elseif ($this->config->get('store_module')) {
			$this->data['modules'] = $this->config->get('store_module');
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
		if (!$this->user->hasPermission('modify', 'module/store')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		return $this->error ? false : true;
	}
}