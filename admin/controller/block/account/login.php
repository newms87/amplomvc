<?php
/**
 * Name: Login
 */
class Admin_Controller_Block_Account_Login extends Admin_Controller_Block_Block
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
