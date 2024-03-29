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

class App_Model_Block_Login_Facebook extends Model
{
	private $settings;

	public function __construct()
	{
		parent::__construct();

		$this->settings = $this->config->load('login_settings', 'facebook');
	}

	public function getStateToken()
	{
		if (!empty($_SESSION['fb_state'])) {
			$_SESSION['fb_state'] = md5(rand());
		}

		return $_SESSION['fb_state'];
	}

	public function getConnectUrl()
	{
		//Redirect after login
		if (strpos($this->router->getPath(), 'customer/logout') !== 0) {
			$this->request->setRedirect($this->url->here(), null, 'fb_redirect');
		} else {
			$this->request->setRedirect(site_url('account'), null, 'fb_redirect');
		}

		$query = array(
			'app_id'        => $this->settings['app_id'],
			'state'         => $this->getStateToken(),
			'redirect_uri'  => site_url('block/login/facebook/connect'),
			'response_type' => 'code',
			'scope'         => 'email',
		);

		return site_url("https://www.facebook.com/v2.2/dialog/oauth", $query);
	}

	public function authenticate()
	{
		$state    = _get('state');
		$fb_state = _session('fb_state');

		if (!$state || $state !== $fb_state) {
			$this->error['state'] = _l("Unable to verify the User");
			return false;
		}

		if (!empty($_GET['error_code'])) {
			$this->error['error_code'] = _get('error_message');
			return false;
		}

		if (empty($_GET['code'])) {
			$this->error['code'] = _l("Your access code was unable to be verified");
			return false;
		}

		$query = array(
			'client_id'     => $this->settings['app_id'],
			'redirect_uri'  => site_url('block/login/facebook/connect'),
			'client_secret' => $this->settings['app_secret'],
			'code'          => $_GET['code'],
		);

		$response = $this->curl->get("https://graph.facebook.com/oauth/access_token", $query);

		$values = explode('&', $response);

		$tokens = array();

		foreach ($values as $value) {
			if (strpos($value, '=')) {
				list($key, $value) = explode('=', $value);
				$tokens[$key] = $value;
			}
		}


		if (empty($tokens['access_token'])) {
			$this->error['access_token'] = _l("Access Token was not acquired");
			return false;
		}

		$query = array(
			'access_token' => $tokens['access_token'],
		);

		$user_info = $this->curl->get("https://graph.facebook.com/me", $query, Curl::RESPONSE_JSON);

		return $this->registerCustomer($user_info);
	}

	private function registerCustomer($user_info)
	{
		if (empty($user_info)) {
			$this->error['user_info'] = _l("We were unable to find your user information on Facebook");
			return false;
		}

		$customer_id = $this->queryVar("SELECT customer_id FROM {$this->t['customer_meta']} WHERE `key` = 'facebook_id' AND `value` = '" . $this->escape($user_info['id']) . "' LIMIT 1");

		//Lookup Customer or Register new customer
		if (!$customer_id) {
			$no_meta = true;

			if (!empty($user_info['email'])) {
				$customer = $this->queryRow("SELECT * FROM {$this->t['customer']} WHERE email = '" . $this->escape($user_info['email']) . "'");
			}

			if (empty($customer)) {
				$customer = array(
					'first_name' => $user_info['first_name'],
					'last_name'  => $user_info['last_name'],
					'email'      => $user_info['email'],
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
		$this->customer->login($customer['email'], AC_CUSTOMER_OVERRIDE);

		//Set Meta for future login
		if ($no_meta) {
			$this->customer->setMeta('facebook_id', $user_info['id']);
		}

		return true;
	}
}
