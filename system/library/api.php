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
}
