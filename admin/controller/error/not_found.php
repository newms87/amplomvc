<?php
class Admin_Controller_Error_NotFound extends Controller
{
	public function index()
	{
		$this->template->load('error/not_found');

		$this->document->setTitle(_l("Page Not Found!"));

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Page Not Found!"), $this->url->link('error/not_found'));

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}
}
