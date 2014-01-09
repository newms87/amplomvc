<?php
class Catalog_Controller_Common_Maintenance extends Controller
{
	public function index()
	{
		$this->template->load('common/maintenance');

		$this->language->load('common/maintenance');

		$this->document->setTitle(_l("Maintenance"));

		$this->language->set('message', _l("<h1 style=\"text-align:center;\">We are currently performing some scheduled maintenance. <br/>We will be back as soon as possible. Please check back soon.</h1>"));

		$this->children = array(
			'common/footer',
			'common/header'
		);

		$this->response->setOutput($this->render());
	}
}
