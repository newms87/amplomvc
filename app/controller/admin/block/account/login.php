<?php
/**
 * Name: Login
 */
class App_Controller_Admin_Block_Account_Login extends App_Controller_Admin_Block_Block
{
	public function settings(&$settings)
	{
		//Render
		$this->render('block/account/login', $settings);
	}

	public function save()
	{
		return '';
	}
}
