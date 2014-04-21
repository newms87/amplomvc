<?php
class Admin_Controller_Error_NotFound extends Controller
{
	public function index()
	{
		$this->document->setTitle(_l("Page Not Found!"));

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Page Not Found!"), $this->url->link('error/not_found'));

		$this->response->setOutput($this->render('error/not_found', $data));
	}
}
