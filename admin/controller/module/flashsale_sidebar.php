<?php
class Admin_Controller_Module_FlashsaleSidebar extends Controller 
{
	
	
	public function index()
	{
		$this->template->load('module/flashsale_sidebar');

		$this->load->language('module/flashsale_sidebar');

		$this->document->setTitle($this->_('heading_title'));
		
		if ($this->request->isPost() && $this->validate()) {
			$this->Model_Setting_Setting->editSetting('flashsale_sidebar', $_POST);
			
			$this->message->add('success', $this->_('text_success'));
			
			$this->url->redirect($this->url->link('module/flashsale_sidebar'));
		}
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_module'), $this->url->link('extension/module'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('module/flashsale_sidebar'));
		
		$this->data['action'] = $this->url->link('module/flashsale_sidebar');
		$this->data['cancel'] = $this->url->link('extension/module');

		$this->data['modules'] = array();
		
		if (isset($_POST['flashsale_sidebar_module'])) {
			$this->data['modules'] = $_POST['flashsale_sidebar_module'];
		} elseif ($this->config->get('flashsale_sidebar_module')) {
			$this->data['modules'] = $this->config->get('flashsale_sidebar_module');
		}
		
		$this->data['layouts'] = $this->Model_Design_Layout->getLayouts();
		
		$this->data['positions'] = array(
			'column_left'=>$this->_('text_column_left'),
			'column_right'=>$this->_('text_column_right')
			);
		
		$this->data['breadcrumbs'] = $this->breadcrumb->render();
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->response->setOutput($this->render());
	}
	
	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'module/flashsale_sidebar')) {
			$this->error['warning'] = $this->_('error_permission');
		}
				
		return $this->error ? false : true;
	}
}