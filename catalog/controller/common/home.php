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
		$this->template->load('common/home');

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
