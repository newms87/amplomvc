#<?php
//=====
class Catalog_Controller_Block_Account_Login extends Controller
{
//.....
	public function index($settings)
	{
//-----
//>>>>> {php} {before}
		$this->language->plugin('janrain', 'account_login');
		$janrain_settings = array(
			'display_type' => 'popup',
			'icon_size'    => 'tiny'
		);

		$this->data['block_widget_janrain'] = $this->getBlock('widget/janrain', $janrain_settings);
//-----
//=====
		$this->render();
//-----
//=====
	}
//.....
}

//-----
