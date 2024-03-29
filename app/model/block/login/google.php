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

class App_Model_Block_Login_Google extends Model
{
	private $settings;

	public function __construct()
	{
		parent::__construct();

		$this->settings = $this->config->load('login_settings', 'google_plus');
	}

	public function getStateToken()
	{
		if (!empty($_SESSION['gp_state'])) {
			$_SESSION['gp_state'] = md5(rand());
		}

		return $_SESSION['gp_state'];
	}

	public function getConnectUrl()
	{
		//Redirect after login
		if (strpos($this->router->getPath(), 'customer/logout') !== 0) {
			$this->request->setRedirect($this->url->here(), null, 'gp_redirect');
		} else {
			$this->request->setRedirect(site_url('account'), null, 'gp_redirect');
		}

		$query = array(
			'scope'         => "https://www.googleapis.com/auth/plus.profile.emails.read",
			'state'         => $this->getStateToken(),
			'redirect_uri'  => site_url("block/login/google/connect"),
			'response_type' => 'code',
			'client_id'     => $this->settings['client_id'],
			'access_type'   => 'offline',
		);

		return site_url('https://accounts.google.com/o/oauth2/auth', $query);
	}

	public function authenticate()
	{
		if (empty($_GET['state']) || $_GET['state'] !== _session('gp_state')) {
			$this->error['state'] = _l("Unable to verify the User");
			return false;
		}

		if (!empty($_GET['error_code'])) {
			$this->error['error_code'] = $_GET['error_message'];
			return false;
		}

		if (empty($_GET['code'])) {
			$this->error['code'] = _l("Your access code was unable to be verified");
			return false;
		}

		//Authentication
		$auth_data = array(
			'code'          => $_GET['code'],
			'client_id'     => $this->settings['client_id'],
			'client_secret' => $this->settings['client_secret'],
			'redirect_uri'  => site_url("block/login/google/connect"),
			'grant_type'    => 'authorization_code',
		);

		$response = $this->curl->post("https://accounts.google.com/o/oauth2/token", $auth_data, Curl::RESPONSE_JSON);

		if (empty($response['access_token'])) {
			$msg                      = _l("There was a problem authenticating your credentials.");
			$this->error['exception'] = $msg;
			write_log('error', $msg . '<BR>' . json_encode($response));
			return false;
		}

		$_SESSION['token'] = $response['access_token'];

		$query = array(
			'access_token' => $response['access_token'],
		);

		$data = $this->curl->get("https://www.googleapis.com/plus/v1/people/me", $query, Curl::RESPONSE_JSON);

		if (empty($data)) {
			$this->error['data'] = _l("There was an error in the response from Google+");
			return false;
		}

		return $this->registerCustomer($data);
	}

	private function registerCustomer($data)
	{
		$customer_id = $this->queryVar("SELECT customer_id FROM {$this->t['customer_meta']} WHERE `key` = 'google+_id' AND `value` = '" . $this->escape($data['id']) . "' LIMIT 1");

		//Lookup Customer or Register new customer
		if (!$customer_id) {
			$no_meta = true;
			$email   = !empty($data['emails'][0]) ? $data['emails'][0]['value'] : '';

			if ($email) {
				$customer = $this->queryRow("SELECT * FROM {$this->t['customer']} WHERE email = '" . $this->escape($email) . "'");
			}

			if (empty($customer)) {
				if (!$data['name']['givenName'] && !$data['name']['familyName'] && $data['displayName']) {
					$names                     = explode(' ', $data['displayName'], 2);
					$data['name']['givenName'] = $names[0];

					if (!empty($names[1])) {
						$data['name']['familyName'] = $names[1];
					}
				}

				$customer = array(
					'first_name' => !empty($data['name']['givenName']) ? $data['name']['givenName'] : 'New',
					'last_name'  => !empty($data['name']['familyName']) ? $data['name']['familyName'] : 'Customer',
					'email'      => $email,
				);

				if (!$this->Model_Customer->save(null, $customer)) {
					$this->error = $this->Model_Customer->fetchError();
					return false;
				}
			}
		} else {
			$customer = $this->Model_Customer->getRecord($customer_id);
			$no_meta  = false;
		}

		//Login Customer
		if (!$this->customer->login($customer['email'], AC_CUSTOMER_OVERRIDE)) {
			$this->error['login'] = _l("Customer login failed. Please try again");
			return false;
		}

		//Set Meta for future login
		if ($no_meta) {
			$this->customer->setMeta('google+_id', $data['id']);
		}

		return true;
	}
}
