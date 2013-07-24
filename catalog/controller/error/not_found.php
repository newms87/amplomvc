<?php
class Catalog_Controller_Error_NotFound extends Controller
{
	public function index()
	{
		//Template and Language
		$this->template->load('error/not_found');
		$this->language->load('error/not_found');
		
		//Page Title
		$this->document->setTitle($this->_('heading_title'));
		
		//Breadcrumbs
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_error'), $this->url->here());
		
		$this->response->addHeader($_SERVER['SERVER_PROTOCOL'] . '/1.1 404 Not Found');
		
		//Action Buttons
		$this->data['continue'] = $this->url->link('common/home');

		//Dependencies
		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);
		
		//Render
		$this->response->setOutput($this->render());
  	}
}