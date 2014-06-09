<?php
class App_Controller_Admin_Common_Codemirror extends Controller
{
	public function index()
	{
		$this->response->setOutput($this->render('common/codemirror'));
	}
}