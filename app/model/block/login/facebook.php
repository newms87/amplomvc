<?php

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
		if (!$this->session->has('fb_state')) {
			$this->session->set('fb_state', md5(rand()));
		}

		return $this->session->get('fb_state');
	}

	public function getConnectUrl()
	{
		//Redirect after login
		if (strpos($this->route->getPath(), 'customer/logout') !== 0) {
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

		return site_url("https://www.facebook.com/dialog/oauth", $query);
	}

	public function authenticate()
	{
		$state = _get('state');
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
			if (strpos($value,'=')) {
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

		$customer_id = $this->queryVar("SELECT customer_id FROM " . DB_PREFIX . "customer_meta WHERE `key` = 'facebook_id' AND `value` = '" . $this->escape($user_info->id) . "' LIMIT 1");

		//Lookup Customer or Register new customer
		if (!$customer_id) {
			$no_meta = true;

			if (!empty($user_info->email)) {
				$customer = $this->queryRow("SELECT * FROM " . DB_PREFIX . "customer WHERE email = '" . $this->escape($user_info->email) . "'");
			}

			if (empty($customer)) {
				$customer = array(
					'firstname' => $user_info->first_name,
					'lastname'  => $user_info->last_name,
					'email'     => $user_info->email,
				);

				if (!$this->customer->add($customer)) {
					$this->error = $this->customer->getError();
					return false;
				}
			}
		} else {
			$customer = $this->customer->getCustomer($customer_id);
			$no_meta  = false;
		}

		//Login Customer
		$this->customer->login($customer['email'], AC_CUSTOMER_OVERRIDE);

		//Set Meta for future login
		if ($no_meta) {
			$this->customer->setMeta('facebook_id', $user_info->id);
		}

		return true;
	}
}
