<?php
class Admin_Controller_Module_Category extends Controller 
{
	
	
	public function index()
	{
		$this->template->load('module/category');

		$this->language->load('module/category');

		$this->document->setTitle($this->_('heading_title'));
		
		if ($this->request->isPost() && $this->validate()) {
			$this->Model_Setting_Setting->editSetting('category', $_POST);
					
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
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('module/category'));

		$this->data['action'] = $this->url->link('module/category');
		
		$this->data['cancel'] = $this->url->link('extension/module');
		
		$this->data['modules'] = array();
		
		if (isset($_POST['category_module'])) {
			$this->data['modules'] = $_POST['category_module'];
		} elseif ($this->config->get('category_module')) {
			$this->data['modules'] = $this->config->get('category_module');
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
		if (!$this->user->hasPermission('modify', 'module/category')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		return $this->error ? false : true;
	}
}