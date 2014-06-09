<?php

/**
 * Name: Social Media Login
 */
class App_Controller_Block_Account_Login extends App_Controller_Block_Block
{
	public function build($settings = array())
	{
		if (!empty($settings['redirect'])) {
			$this->request->setRedirect($settings['redirect']);
		}

		//Block Settings
		$defaults = array(
			'username' => '',
			'size'     => 'large',
		   'template' => 'block/account/login',
		);

		$settings += $_POST + $defaults;

		//Template Data
		$login_settings = $this->config->loadGroup('login_settings');

		$medias = array();

		if (!empty($login_settings['status'])) {
			if (!empty($login_settings['facebook']['active'])) {
				$medias['facebook'] = array(
					'name' => 'facebook',
					'url'  => $this->Model_Block_Login_Facebook->getConnectUrl(),
				);
			}

			if (!empty($login_settings['google_plus']['active'])) {
				$medias['google-plus'] = array(
					'name' => 'google-plus',
					'url'  => $this->Model_Block_Login_Google->getConnectUrl(),
				);
			}
		}

		$settings['medias'] = $medias;

		//Render
		$this->render($settings['template'], $settings);
	}

	public function settings(&$settings)
	{
		//Render
		$this->render('block/account/login', $settings);
	}
}
