<?php
class Admin_Controller_Common_Logout extends Controller
{
	public function index()
	{
		$this->user->logout();

		$this->url->redirect($this->url->link('common/login'));
	}
}
