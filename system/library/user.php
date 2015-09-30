<?php

class User extends Library
{
	protected
		$user_id,
		$user,
		$meta,
		$role,
		$alerts,
		$temp_user;

	public function __construct()
	{
		parent::__construct();

		$this->validateUser();
		trigger_error('4');
	}

	public function getId()
	{
		return $this->user_id;
	}

	public function info($key = null)
	{
		if ($key) {
			return isset($this->user[$key]) ? $this->user[$key] : null;
		}

		return $this->user;
	}

	public function meta($key = null, $default = null)
	{
		if ($key) {
			return isset($this->meta[$key]) ? $this->meta[$key] : $default;
		}

		return $this->meta;
	}

	public function loginSystemUser()
	{
		//Change User Role and user ID to the system user
		$this->temp_user = array(
			'user_id' => $this->user_id,
			'info'    => $this->info,
			'meta'    => $this->meta,
			'role'    => $this->role,
		);

		$role = array(
			'user_role_id' => 1,
			'type'         => App_Model_UserRole::TYPE_ADMIN,
			'name'         => 'Top Administrator',
		);

		$this->setUser(-1, array(), array(), $role);
	}

	public function logoutSystemUser()
	{
		$this->user_id = $this->temp_user['user_id'];
		$this->role    = $this->temp_user['role'];
	}

	public function setUser($user_id, $user = array(), $meta = array(), $role = array())
	{
		$this->user_id = $user_id;
		$this->user    = $user;
		$this->meta    = $meta;
		$this->role    = $role;

		if ($role) {
			$this->user['user_role_id'] = $this->role['user_role_id'];
			$this->user['role']         = $this->role['name'];
			$this->user['role_type']    = $this->role['type'];
		}
	}

	public function loadUser($user)
	{
		if (!is_array($user)) {
			$user = $this->queryRow("SELECT * FROM {$this->t['user']} WHERE user_id = " . (int)$user);
		}

		if (!empty($user['user_id'])) {
			$meta = $this->Model_Meta->get('user', $user['user_id']);
			$role = $this->Model_UserRole->getRole($user['user_role_id']);

			$this->setUser($user['user_id'], $user, $meta, $role);

			$this->session->set('user_id', $user['user_id']);
		}
	}

	public function validateUser()
	{
		$user_id = (int)_session('user_id');

		if ($user_id) {
			$session_token = _session('token');
			$cookie_token  = _cookie('token');

			if ($session_token && $cookie_token === $session_token) {
				$user = $this->queryRow("SELECT * FROM {$this->t['user']} WHERE user_id = $user_id AND status = 1");

				if ($user) {
					$this->loadUser($user);

					return true;
				}
			}

			message("notify", "Your session has expired. Please log in again.");
			$this->logout();

			if ($this->route->getPath() !== 'user/logout') {
				$this->request->setRedirect($this->url->here());
			}
		}

		return false;
	}

	public function login($username, $password)
	{
		$username = $this->escape($username);

		$user = $this->queryRow("SELECT * FROM `{$this->t['user']}` WHERE (username = '$username' OR LCASE(email) = '" . strtolower($username) . "') AND status = '1'");

		if ($user) {
			if (!password_verify($password, $user['password'])) {
				$this->error['password'] = _l("The username / password combination was unable to be authenticated.");

				return false;
			}

			$this->loadUser($user);

			$this->session->setToken();
			$this->session->saveTokenSession();

			return true;
		}

		$this->error = _l("Unable to authenticate your log in credentials.");

		return false;
	}

	public function logout()
	{
		$this->user_id = null;
		$this->user    = null;
		$this->meta    = null;
		$this->role    = null;

		$this->session->endTokenSession();
	}

	public function is($roles)
	{
		if ($this->role) {
			return in_array($this->role['type'], (array)$roles) || in_array($this->role['name'], (array)$roles);
		}

		return false;
	}

	public function can($level, $action)
	{
		if ($this->isTopAdmin()) {
			return true;
		}

		return $this->Model_UserRole->can($this->user['user_role_id'], $level, $action);
	}

	public function canDoAction($action)
	{
		if (!IS_ADMIN) {
			return true;
		}

		$path = rtrim($action->getClassPath(), '/') . '/' . $action->getMethod();

		if (!is_logged()) {
			$allowed = array(
				'admin/user/forgotten',
				'admin/user/reset_request',
				'admin/user/reset',
				'admin/user/reset_form',
				'admin/user/login',
				'admin/user/authenticate',
			);

			if (!in_array($path, $allowed)) {
				return false;
			}
		} else {
			$ignore = array(
				'admin/user/logout',
				'admin/user/reset',
				'admin/user/reset_form',
				'admin/error/not_found',
				'admin/error/permission',
			);

			if (!in_array($path, $ignore) && !$this->can('r', $path)) {
				return false;
			}
		}

		return true;
	}

	public function setMeta($key, $value = null)
	{
		if (is_array($key)) {
			return $this->Model_Meta->setAll('user', $this->user_id, $key);
		}

		return $this->Model_Meta->set('user', $this->user_id, $key, $value);
	}

	public function removeMeta($key, $value = null)
	{
		return $this->Model_Meta->removeKey('user', $this->user_id, $key, $value);
	}

	public function alert($user_id, $type, $key, $message)
	{
		//Save Alert for when user logs in
		if ($user_id !== $this->user_id) {
			$alerts              = $this->getAlerts($user_id);
			$alerts[$type][$key] = $message;
			$this->Model_Meta->set('user', $user_id, 'alert', $alerts);
		} else {
			//Alert user immediately
			if ($this->alerts === null) {
				$this->getAlerts();
			}

			$this->alerts[$type][$key] = $message;
		}
	}

	public function getAlerts($user_id = null)
	{
		if ($user_id === null) {
			$user_id = $this->user_id;
		}

		//Get only Save alerts (if user is not logged in)
		if ($user_id !== $this->user_id) {
			return (array)$this->Model_Meta->get('user', $user_id, 'alert');
		}

		if ($this->alerts === null) {
			if (!isset($_SESSION['user_alerts'])) {
				$_SESSION['user_alerts'] = array();
			}

			//Get alerts for current user
			$this->alerts = &$_SESSION['user_alerts'];
			$this->alerts += (array)$this->Model_Meta->get('user', $user_id, 'alert');
		}

		return $this->alerts;
	}

	public function fetchAlerts($user_id = null)
	{
		$alerts = $this->getAlerts($user_id);

		if (!$user_id || $user_id === $this->user_id) {
			unset($_SESSION['user_alerts']);
		}

		$this->Model_Meta->removeKey('user', $user_id, 'alert');

		return $alerts;
	}

	public function renderAlerts($user_id = null, $style = 'inline')
	{
		$alerts = new Message(false);
		$alerts->set($this->fetchAlerts($user_id));

		return $alerts->render(null, true, $style);
	}

	public function isTopAdmin()
	{
		return $this->role ? $this->role['name'] === 'Top Administrator' : false;
	}

	public function isLogged()
	{
		return $this->user_id ? true : false;
	}

	public function showAdminBar()
	{
		return $this->isLogged() && !_cookie('disable_admin_bar') && $this->is('admin');
	}

	public function encrypt($password)
	{
		return password_hash($password, PASSWORD_DEFAULT, array('cost' => PASSWORD_COST));
	}

	public function requestReset($email)
	{
		$user_id = $this->Model_User->findRecord(array('email' => $email));

		if (!$user_id) {
			$this->error['email'] = _l("The E-Mail Address was not found in our records, please try again!");

			return false;
		}

		$code = $this->generateCode();

		$this->Model_Meta->set('user', $user_id, 'pass_reset_code', $code);

		$email_data = array(
			'reset' => site_url('admin/user/reset-form', 'code=' . $code),
			'email' => $email,
		);

		call('admin/mail/forgotten', $email_data);

		return true;
	}

	public function lookupResetCode($code)
	{
		if ($code) {
			return $this->queryVar("SELECT record_id FROM {$this->t['meta']} WHERE `type` = 'user' AND `key` = 'pass_reset_code' AND value = '" . $this->escape($code) . "'");
		}
	}

	public function clearResetCode($user_id)
	{
		return $this->Model_Meta->removeKey('user', $user_id, 'pass_reset_code');
	}

	public function generatePassword()
	{
		return substr(str_shuffle(md5(microtime())), 0, (int)rand(10, 13));
	}

	public function generateCode()
	{
		return str_shuffle(md5(microtime(true) * rand()));
	}
}
