<?php
class Admin_Controller_Error_Permission extends Controller
{
	public function index()
	{
		$this->document->setTitle(_l("Permission Denied!"));

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Permission Denied!"), $this->url->link('error/permission'));

		$this->response->setOutput($this->render('error/permission', $data));
	}
}
