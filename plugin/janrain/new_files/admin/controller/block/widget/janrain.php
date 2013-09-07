<?php
class Admin_Controller_Block_Widget_Janrain extends Controller
{
	public function settings(&$settings)
	{
		$this->template->load('block/widget/janrain_settings');

		$this->data = $settings;

		$this->data['image_offset'] = array(
			'facebook'    => 0,
			'google'      => 1,
			'linkedin'    => 2,
			'myspace'     => 3,
			'twitter'     => 4,
			'windowslive' => 5,
			'yahoo'       => 6,
			'aol'         => 7,
			'bing'        => 8,
			'flickr'      => 9,
			''            => 10,
			''            => 11,
			''            => 12,
			''            => 13,
			''            => 14,
			''            => 15,
			'wordpress'   => 16,
			'paypal'      => 17,
			''            => 18,
			''            => 19,
			''            => 20,
			''            => 21
		);

		$this->data['social_icon_sprite'] = $this->image->get('janrain/rpx-icons16.png');

		$defaults = array(
			'application_domain' => '',
			'api_key'            => '',
			'display_icons'      => array(),
			'login_redirect'     => '',
			'logout_redirect'    => '',
			'integrate_header'   => 0,
		);

		foreach ($defaults as $key => $default) {
			if (!isset($this->data[$key])) {
				$this->data[$key] = $default;
			}
		}

		$this->_('entry_login_redirect_description', SITE_URL);
		$this->_('entry_logout_redirect_description', SITE_URL);

		$this->render();
	}

	public function saveSettings(&$settings)
	{
		$file_modifications = array(
			'catalog/view/theme/default/template/block/account/login_header.tpl' => DIR_PLUGIN . 'janrain/includes/catalog/view/theme/default/template/block/account/login_header.tpl',
			'catalog/controller/block/account/login.php' => DIR_PLUGIN . 'janrain/includes/catalog/controller/block/account/login.php',
		);

		if (!empty($settings['integrate_header'])) {
			foreach($file_modifications as $source => $file_mod) {
				$this->mod->addFile($source, $file_mod);
			}

			$this->mod->apply();
			$this->mod->write();
		} else {
			foreach($file_modifications as $source => $file_mod) {
				$this->mod->removeFile($source, $file_mod);
			}

			$this->mod->apply();
			$this->mod->write();
		}
	}

	public function validate()
	{
		return $this->error;
	}
}