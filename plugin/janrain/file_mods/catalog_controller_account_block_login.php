#<?php
//=====
class Catalog_Controller_Account_Block_Login extends Controller 
{
//.....
	public function index($settings, $type = 'header')
	{
//-----
//>>>>> {php} {before}
		$this->language->plugin('janrain', 'account_block_login');
		$janrain_settings = array('display_type'=>'popup','icon_size'=>'tiny');
		$this->data['module_janrain'] = $this->getModule('janrain', $janrain_settings);
//-----
//=====
		$this->render();
//-----
//=====
  	}
//.....
}
//-----
