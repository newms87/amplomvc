<?php
class Admin_Controller_Common_Footer extends Controller
{
	public function index()
	{
		$this->view->load('common/footer');

		$this->render();
	}
}
