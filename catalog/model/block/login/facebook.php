<?php

class Catalog_Model_Block_Login_Facebook extends Model
{
	private $app_id = '229627630541727';
	private $app_secret = '45053593dfc8fab0cbee7a1a452b9e6e';

	public function getStateToken()
	{
		if (empty($this->session->data['fb_state'])) {
			$this->session->set('fb_state', md5(rand()));
		}

		return $this->session->data['fb_state'];
	}

	public function getConnectUrl()
	{
		//Redirect after login
		$this->request->setRedirect($this->url->here(), null, 'fb_redirect');

		$query = array(
			'app_id'        => $this->app_id,
			'state'         => $this->getStateToken(),
			'redirect_uri'  => $this->url->link('block/login/facebook/connect'),
			'response_type' => 'code',
			'scope'         => 'email',
		);

		return $this->url->link("https://www.facebook.com/dialog/oauth", $query);
	}

	public function authenticate()
	{
		if (empty($_GET['state']) || empty($this->session->data['fb_state']) || $_GET['state'] !== $this->session->data['fb_state']) {
			$this->error['state'] = _l("Unable to verify the User");
			return false;
		}

		if (!empty($_GET['error_code'])) {
			$this->error['error_code'] = $_GET['error_message'];
			return false;
		}

		if (empty($_GET['code'])) {
			$this->error['code'] = _l("sYour access code was unable to be verified");
			return false;
		}

		$query = array(
			'client_id'     => $this->app_id,
			'redirect_uri'  => $this->url->link('block/login/facebook/connect'),
			'client_secret' => $this->app_secret,
			'code'          => $_GET['code'],
		);

		$response = $this->curl->get("https://graph.facebook.com/oauth/access_token", $query);

		$values = explode('&', $response['content']);

		$tokens = array();

		foreach ($values as $value) {
			list($key, $value) = explode('=', $value);
			$tokens[$key] = $value;
		}


		if (empty($tokens['access_token'])) {
			$this->error['access_token'] = _l("Access Token was not acquired");
			return false;
		}

		$query = array(
			'access_token' => $tokens['access_token'],
		);

		$response = $this->curl->get("https://graph.facebook.com/me", $query);

		$user_info = json_decode($response['content']);

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

				$this->customer->add($customer);
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
