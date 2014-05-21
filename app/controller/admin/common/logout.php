<?php
class App_Controller_Admin_Common_Logout extends Controller
{
	public function index()
	{
		$this->user->logout();

		redirect('common/login');
	}
}
