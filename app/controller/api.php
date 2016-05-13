<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC
 * @link http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYING.txt and LICENSE.txt files in the root directory.
 */

class App_Controller_Api extends Controller
{
	public function __construct()
	{
		$this->api->authenticate();

		parent::__construct();
	}

	public function request_token()
	{
		$token = $this->api->getToken();

		if ($token) {
			output_api('success', _l("Authenticated"), array('token' => $token));
		} else {
			output_api('error', _l("Unable to authenticate the request."), null, 401);
		}
	}

	public function refresh_token()
	{
		$token = $this->api->refreshToken();

		if ($token) {
			$td = $this->api->getTokenData();

			$token_data = array(
				'token'   => $td['token'],
				'expires' => $td['date_expires'],
				'created' => $td['date_created'],
			);

			output_api('success', _l("The token has been refreshed."), $token_data);
		} else {
			output_api('error', _l("The token has either expired or does not exist."), null, 401);
		}
	}
}
