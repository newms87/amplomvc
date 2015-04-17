<?php

class Api extends Library
{
	protected $api_user;

	public function __construct()
	{
		parent::__construct();
		$this->loadApiUser();
	}

	protected function loadApiUser()
	{
		$token = _request('token');

		if ($token) {
			$this->api_user = $this->queryRow("SELECT * FROM {$this->t['api_user']} WHERE `token` = '" . $this->escape($token) . "' AND token_expires > NOW()");
		} else {
			$username = !empty($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : _post('api_user');
			$key      = !empty($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : _post('api_key');

			$this->api_user = $this->queryRow("SELECT * FROM {$this->t['api_user']} WHERE `username` = '" . $this->escape($username) . "' AND api_key = '" . $this->escape($key) . "'");

			if ($this->api_user) {
				$set_token = array(
					'token'         => $this->generateToken(),
					'token_expires' => $this->date->add('12 hours'),
				);

				$this->update('api_user', $set_token, $this->api_user['api_user_id']);

				$this->api_user = $set_token + $this->api_user;
			}
		}
	}

	public function authorize($block_request = true)
	{
		if ($this->api_user) {
			$user = $this->Model_User->getRecord($this->api_user['user_id']);
		}

		if (empty($user)) {
			if ($block_request) {
				header('HTTP/1.1 401 Unauthorized');

				output_json(array(
					'status'  => 'error',
					'code'    => 401,
					'message' => _l("Unauthorized request. The API User was not verified."),
				));

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

		return true;
	}

	public function getToken()
	{
		if ($this->api_user) {
			return $this->api_user['token'];
		}
	}

	protected function generateToken()
	{
		return bin2hex(openssl_random_pseudo_bytes(30));
	}

	public function generateApiKey()
	{
		return bin2hex(openssl_random_pseudo_bytes(45));
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
