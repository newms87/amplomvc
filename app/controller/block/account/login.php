<?php

class App_Controller_Block_Account_Login extends Controller
{
	public function build($settings = array())
	{
		if (!empty($settings['redirect'])) {
			$this->request->setRedirect($settings['redirect']);
		}

		//Input Data
		$defaults = array(
			'username' => ''
		);

		$data = $_POST + $defaults;

		//Template Data
		$data['gp_login'] = $this->Model_Block_Login_Google->getConnectUrl();
		$data['fb_login'] = $this->Model_Block_Login_Facebook->getConnectUrl();

		//The Template
		$template = !empty($settings['template']) ? $settings['template'] : 'block/account/login_header';

		//Render
		$this->render($template, $data);
	}
}
