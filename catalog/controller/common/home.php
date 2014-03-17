<?php
class Catalog_Controller_Common_Home extends Controller
{
	public function index()
	{
		//Page Head
		$this->document->setTitle($this->config->get('config_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));

		//Page Title
		$this->data['page_title'] = $this->config->get('config_title');

		//The Template
		$this->view->load('common/home');

		//Dependencies
		$this->children = array(
			'area/left',
			'area/right',
			'area/top',
			'area/bottom',
			'common/footer',
			'common/header'
		);

		//Render
		$this->response->setOutput($this->render());
	}
}
