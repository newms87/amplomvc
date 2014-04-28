<?php
class Catalog_Controller_Block_Login_Facebook extends Controller
{
	public function build()
	{
		//Actions
		$data['connect'] = $this->Catalog_Model_Block_Login_Facebook->getConnectUrl();

		//Render
		$this->render('block/login/facebook', $data);
	}

	public function connect()
	{
		if ($this->Catalog_Model_Block_Login_Facebook->authenticate()) {
			$this->message->add('success', _l("You have been successfully logged in using Facebook!"));
		} else {
			$this->message->add('error', $this->Catalog_Model_Block_Login_Facebook->getError());
			$this->message->add('warning', _l("There was a problem while signing you in with Facebook. Please try again, or try a different login method."));
		}

		if ($this->request->hasRedirect('fb_redirect')) {
			$this->request->doRedirect('fb_redirect');
		} else {
			redirect('common/home');
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

			$this->message->add('success', _l("Successfully Disconnected Google+"));
		} else {
			$this->message->add('warning', _l("Invalid Token"));
		}
		redirect('common/home');
	}
}
