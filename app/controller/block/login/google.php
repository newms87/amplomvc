<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC
 * @link http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt files in the root directory.
 */

class App_Controller_Block_Login_Google extends App_Controller_Block_Block
{
	public function build()
	{
		//Actions
		$data['connect'] = $this->Model_Block_Login_Google->getConnectUrl();

		//Render
		$this->render('block/login/google', $data);
	}

	public function connect()
	{
		if ($this->Model_Block_Login_Google->authenticate()) {
			message('success', _l("You have been successfully logged in using Google+!"));
		} else {
			if ($this->Model_Block_Login_Google->hasError()) {
				message('error', $this->Model_Block_Login_Google->fetchError());
			}

			message('warning', _l("There was a problem while signing you in with Google+. Please try again, or try a different login method."));
		}

		if ($this->request->hasRedirect('gp_redirect')) {
			$this->request->doRedirect('gp_redirect');
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

	public function people()
	{
		// Get list of people user has shared with this app.
		$token = $app['session']->get('token');

		if (empty($token)) {
			return new Response('Unauthorized request', 401);
		}

		$client->setAccessToken($token);
		$people = $plus->people->listPeople('me', 'visible', array());
		return $app->json($people);
	}
}
