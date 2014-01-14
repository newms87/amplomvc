<?php
class Catalog_Controller_Common_Maintenance extends Controller
{
	public function index()
	{
		//Page Head
		$this->document->setTitle(_l("Maintenance"));

		//The Template
		$this->template->load('common/maintenance');

		//Dependencies
		$this->children = array(
			'common/footer',
			'common/header'
		);

		//Render
		$this->response->setOutput($this->render());
	}
}
