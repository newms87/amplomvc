<?php

class App_Controller_Error extends Controller
{
	public function not_found()
	{
		//Page Head
		set_page_info('title', _l("The page you requested cannot be found!"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url());
		breadcrumb(_l("Not Found"), $this->url->here());

		$this->response->addHeader('HTTP/1.1 404 Not Found');

		//Action Buttons
		$data['continue'] = site_url();

		//Render
		output($this->render('error/not_found', $data));
	}
}
