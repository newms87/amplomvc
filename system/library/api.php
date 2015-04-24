<?php

class Api extends Library
{
	protected $api_user, $api_token;

	public function authenticate($block_request = true)
	{
		$token = _request('token');

		if ($token) {
			$this->api_token = $this->queryRow("SELECT * FROM {$this->t['api_token']} WHERE `token` = '" . $this->escape($token) . "' AND date_expires > NOW()");

			if ($this->api_token) {
				$this->api_user = $this->queryRow("SELECT * FROM {$this->t['api_user']} WHERE api_user_id = " . $this->api_token['api_user_id']);
			}
		} else {
			$username = !empty($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : _post('api_user');
			$key      = !empty($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : _post('api_key');

			$this->api_user = $this->queryRow("SELECT * FROM {$this->t['api_user']} WHERE `username` = '" . $this->escape($username) . "' AND api_key = '" . $this->escape($key) . "' AND status = 1");
		}

		if ($this->api_user) {
			$user = $this->Model_User->getRecord($this->api_user['user_id']);
		}

		if (empty($user)) {
			if (!isset($_SERVER['PHP_AUTH_USER'])) {
				header('WWW-Authenticate: Basic realm="Amplo API Authentication"');
			}

			if ($block_request) {
				if ($token) {
					output_api('error', _l("Unauthorized request. The token is either invalid or expired."), $_REQUEST, 41, 401);
				} else {
					output_api('error', _l("Unauthorized request. The API User was not verified."), $_REQUEST, 401);
				}

				$this->response->output();
				exit;
			}

			return false;
		}

		$meta = $this->Model_User->getMeta($user['user_id']);

		$user_role = $this->Model_UserRole->getRole($this->api_user['user_role_id']);

		if ($user_role) {
			$permissions  = $user_role['permissions'];
			$user['role'] = $user_role['name'];
		} else {
			$permissions  = array();
			$user['role'] = '';
		}

		$this->user->setUser($user['user_id'], $user, $meta, $permissions);

		if (!$this->api_token) {
			$this->api_token = array(
				'token'        => $this->generateToken(),
				'date_created' => $this->date->now(),
				'date_expires' => $this->date->add('12 hours'),
				'api_user_id'  => $this->api_user['api_user_id'],
				'customer_id'  => null,
			);

			//Validate this user can access this customer
			if (_request('customer_id')) {
				$this->api_token['customer_id'] = _request('customer_id');
			}

			$this->insert('api_token', $this->api_token);
		}

		if ($this->api_token['customer_id']) {
			if (!$this->customer->setCustomer($this->api_token['customer_id'])) {
				if ($block_request) {
					output_api('error', _l("Unauthorized request. The Customer requested did not exist or is not authorized with this account."), null, 401);
					$this->response->output();
					exit;
				}

				$this->error = $this->customer->getError();
				$this->clearToken();

				return false;
			}
		}

		return true;
	}

	public function clearToken()
	{
		$this->api_user  = null;
		$this->api_token = null;
	}

	public function getToken()
	{
		return !empty($this->api_token['token']) ? $this->api_token['token'] : false;
	}

	public function getTokenData()
	{
		return $this->api_token;
	}

	public function refreshToken()
	{
		if ($this->api_token) {
			$this->update('api_token', array('date_expires' => $this->date->add('12 hours')), array('token' => $this->api_token['token']));

			return true;
		}

		return false;
	}

	protected function generateToken()
	{
		return bin2hex(openssl_random_pseudo_bytes(30));
	}

	public function generateApiKey()
	{
		return bin2hex(openssl_random_pseudo_bytes(30));
	}

	public function generateKeys(&$private_key, &$public_key)
	{
		$config = array(
			'config'           => DIR_RESOURCES . 'openssl.cnf',
			"digest_alg"       => "sha512",
			"private_key_bits" => 4096,
			"private_key_type" => OPENSSL_KEYTYPE_RSA,
		);

		$res = openssl_pkey_new($config);

		if (!$res) {
			$this->error['openssl'] = _l("Failed to generate key pair. There was an OpenSSL Error: %s", openssl_error_string());

			return false;
		}

		if (!openssl_pkey_export($res, $private_key, null, $config)) {
			$this->error['openssl'] = _l("Failed to export private key. There was an OpenSSL Error: %s", openssl_error_string());

			return false;
		}

		$key_details = openssl_pkey_get_details($res);
		$public_key  = $key_details["key"];

		return true;
	}
}
