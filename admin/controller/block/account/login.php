<?php
/**
 * Name: Login
 */
class Admin_Controller_Block_Extras_AccountLogin extends Controller
{
	public function settings(&$settings)
	{
		//The Template
		$this->view->load('block/account/login');

		//Render
		$this->render();
	}

	public function save()
	{
		return '';
	}
}
