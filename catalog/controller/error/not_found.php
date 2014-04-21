<?php
class Catalog_Controller_Error_NotFound extends Controller
{
	public function index()
	{
		//Page Head
		$this->document->setTitle(_l("The page you requested cannot be found!"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("The page you requested cannot be found."), $this->url->here());

		$this->response->addHeader($_SERVER['SERVER_PROTOCOL'] . '/1.1 404 Not Found');

		//Action Buttons
		$data['continue'] = $this->url->link('common/home');

		//Render
		$this->response->setOutput($this->render('error/not_found', $data));
	}
}
