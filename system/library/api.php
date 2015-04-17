<?php

class Api extends Library
{
	public function authorize($user = null, $key = null)
	{
		if (!$user) {
			$user = _post('user');
		}

		if (!$key) {
			$key = _post('api_key');
		}

		$api_user = $this->queryVar("SELECT * FROM {$this->t['api_user']} WHERE `user` = '" . $this->escape($user) . "' AND api_key = '" . $this->escape($key) . "'");

		$user = $this->Model_User->getRecord($api_user['user_id']);

		if (!$user) {
			header('HTTP/1.1 401 Unauthorized');
			exit;
		}

		$meta = $this->Model_User->getMeta($user['user_id']);

		$user_role = $this->Model_UserRole->getRole($user['user_role_id']);

		if ($user_role) {
			$permissions  = $user_role['permissions'];
			$user['role'] = $user_role['name'];
		} else {
			$permissions  = array();
			$user['role'] = '';
		}

		$this->user->setUser($user['user_id'], $user, $meta, $permissions);
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
