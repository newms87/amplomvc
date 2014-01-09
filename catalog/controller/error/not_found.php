<?php
class Catalog_Controller_Error_NotFound extends Controller
{
	public function index()
	{
		//Template and Language
		$this->template->load('error/not_found');
		$this->language->load('error/not_found');

		//Page Head
		$this->document->setTitle(_l("The page you requested cannot be found!"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("The page you requested cannot be found."), $this->url->here());

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