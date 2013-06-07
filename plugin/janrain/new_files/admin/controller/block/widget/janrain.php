<?php
class Admin_Controller_Block_Widget_Janrain extends Controller 
{
	public function settings(&$settings)
	{
		$this->template->load('block/widget/janrain_settings');
		
		$this->data = $settings;
		
		$this->data['image_offset'] = array(
			'facebook'	=> 0,
			'google'		=> 1,
			'linkedin'	=> 2,
			'myspace'	=> 3,
			'twitter'	=> 4, 
			'windowslive' => 5,
			'yahoo'		=> 6,
			'aol'			=> 7,
			'bing'		=> 8,
			'flickr'		=> 9,
			'' => 10,'' =>11,'' => 12,'' => 13,'' => 14,''=> 15,
			'wordpress'	=> 16,
			'paypal'		=> 17,
			'' => 18,'' => 19,'' => 20,'' => 21
		);
		
		$this->data['social_icon_sprite'] = $this->image->get('janrain/rpx-icons16.png');
		
		$defaults = array(
			'application_domain' => '',
			'api_key' => '',
			'display_icons' => array(),
			'login_redirect' => '',
			'logout_redirect' => '',
			'integrate_header' => 0,
		);
		
		foreach ($defaults as $key => $default) {
			if (!isset($this->data[$key])) {
				$this->data[$key] = $default;
			}
		}
		
		$this->language->format('entry_login_redirect_description', SITE_URL);
		$this->language->format('entry_logout_redirect_description', SITE_URL);
		
		$this->render();
	}

	public function saveSettings(&$settings)
	{
		$file_modifications = array(
			array(
				'mod' => 'includes/catalog/controller/block/account/login.php',
				'for' => 'catalog/controller/block/account/login.php',
			),
			array(
				'mod' => 'includes/catalog/view/theme/default/template/block/account/login_header.tpl',
				'for' => 'catalog/view/theme/default/template/block/account/login_header.tpl',
			), 
		);
		
		if (!empty($settings['integrate_header'])) {
			$this->plugin->addFileModifications('janrain', $file_modifications);
		} else {
			$this->plugin->removeFileModifications('janrain', $file_modifications);
		}
	}
	
	public function validate()
	{
		return $this->error;
	}
}