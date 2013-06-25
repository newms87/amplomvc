<?php
class Admin_Controller_Error_Permission extends Controller 
{
	public function index()
	{
		$this->template->load('error/permission');

		$this->load->language('error/permission');
  
		$this->document->setTitle($this->_('heading_title'));
		
			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('error/permission'));

		$this->data['breadcrumbs'] = $this->breadcrumb->render();
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
  	}
}