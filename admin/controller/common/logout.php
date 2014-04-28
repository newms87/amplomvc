<?php
class Admin_Controller_Common_Logout extends Controller
{
	public function index()
	{
		$this->user->logout();

		redirect('common/login');
	}
}
