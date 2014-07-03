<?php
class App_Controller_Error_NotFound extends Controller
{
	public function index()
	{
		//Page Head
		$this->document->setTitle(_l("The page you requested cannot be found!"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url());
		breadcrumb(_l("Not Found"), $this->url->here());

		$this->response->addHeader($_SERVER['SERVER_PROTOCOL'] . '/1.1 404 Not Found');

		//Action Buttons
		$data['continue'] = site_url();

		//Render
		output($this->render('error/not_found', $data));
	}
}
