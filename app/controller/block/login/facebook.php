<?php

class App_Controller_Block_Login_Facebook extends App_Controller_Block_Block
{
	public function build()
	{
		//Actions
		$data['connect'] = $this->Model_Block_Login_Facebook->getConnectUrl();

		//Render
		$this->render('block/login/facebook', $data);
	}

	public function connect()
	{
		if ($this->Model_Block_Login_Facebook->authenticate()) {
			message('success', _l("You have been successfully logged in using Facebook!"));
		} else {
			message('error', $this->Model_Block_Login_Facebook->fetchError());
			message('warning', _l("There was a problem while signing you in with Facebook. Please try again, or try a different login method."));
		}

		if ($this->request->hasRedirect('fb_redirect')) {
			$this->request->doRedirect('fb_redirect');
		} else {
			redirect();
		}
	}

	public function disconnect()
	{
		if (!empty($_GET['token'])) {
			// Revoke current user's token and reset their session.
			$token = json_decode($_GET['token'])->access_token;
			$client->revokeToken($token);
			// Remove the credentials from the user's session.
			$app['session']->set('token', '');

			message('success', _l("Successfully Disconnected Google+"));
		} else {
			message('warning', _l("Invalid Token"));
		}
		redirect();
	}
}
