<?php
class Admin_Controller_Error_Permission extends Controller
{
	public function index()
	{
		$this->view->load('error/permission');

		$this->document->setTitle(_l("Permission Denied!"));

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Permission Denied!"), $this->url->link('error/permission'));

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}
}
