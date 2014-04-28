<?php

class Catalog_Controller_Block_Account_Login extends Controller
{
	public function build($settings = array())
	{
		if (!empty($settings['redirect'])) {
			$this->request->setRedirect($settings['redirect']);
		}

		//Input Data
		$login_info = array();

		if ($this->request->isPost()) {
			$login_info = $_POST;
		}

		$defaults = array(
			'username' => ''
		);

		$data = $login_info + $defaults;

		//Template Data
		$data['gp_login'] = $this->Catalog_Model_Block_Login_Google->getConnectUrl();
		$data['fb_login'] = $this->Catalog_Model_Block_Login_Facebook->getConnectUrl();

		//For Guest Checkout (on checkout page)
		if ($this->cart->guestCheckoutAllowed()) {
			$data['guest_checkout'] = site_url('checkout/checkout/guest_checkout');
		}

		//Action Buttons
		$data['login']     = site_url('customer/login');
		$data['register']  = site_url('customer/registration');
		$data['forgotten'] = site_url('customer/forgotten');

		//The Template
		$template = !empty($settings['template']) ? $settings['template'] : 'block/account/login_header';

		//Render
		$this->render($template, $data);
	}
}
