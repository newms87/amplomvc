<?php

class User extends Library
{
	protected
		$user_id,
		$user,
		$meta,
		$alerts,
		$permissions = array(),
		$temp_user;

	public function __construct()
	{
		parent::__construct();

		$this->validateUser();
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
		//Change User permissions and user ID to the system user
		$this->temp_user = array(
			'role'    => $this->user['role'],
			'user_id' => $this->user_id,
		);

		$this->user_id      = -1;
		$this->user['role'] = "Top Administrator";
	}

	public function logoutSystemUser()
	{
		$this->user_id      = $this->temp_user['user_id'];
		$this->user['role'] = $this->temp_user['role'];
	}

	public function setUser($user_id, $info = array(), $meta = array(), $permissions = array())
	{
		$this->user_id     = $user_id;
		$this->info        = $info;
		$this->meta        = $meta;
		$this->permissions = $permissions;
	}

	public function loadUser($user)
	{
		if (!is_array($user)) {
			$user = $this->queryRow("SELECT * FROM {$this->t['user']} WHERE user_id = " . (int)$user);
		}

		if (!empty($user['user_id'])) {
			$this->user_id = $user['user_id'];
			$this->session->set('user_id', $user['user_id']);

			$user_role = $this->Model_UserRole->getRole($user['user_role_id']);

			if ($user_role) {
				$this->permissions = $user_role['permissions'];
				$user['role']      = $user_role['name'];
			} else {
				$this->permissions = array();
				$user['role']      = '';
			}

			$this->user = $user;
			$this->meta = $this->Model_User->getMeta($user['user_id']);
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
		$this->user    = null;
		$this->user_id = null;

		$this->session->endTokenSession();
	}

	public function can($key, $value)
	{
		if ($this->isTopAdmin()) {
			return true;
		}

		if (!$value) {
			return true;
		}

		$path = explode('/', $value);
		$perm = $this->permissions;

		foreach ($path as $p) {
			if (isset($perm[$p])) {
				$perm = $perm[$p];
				continue;
			}

			if (!isset($perm['*'])) {
				return false;
			}

			if (count($perm) === 1) {
				return $key === 'w' ? $perm['*'] === 'w' : (bool)$perm['*'];
			}

			return false;
		}

		if (isset($perm['index'])) {
			return $key === 'w' ? $perm['index']['*'] === 'w' : (bool)$perm['index']['*'];
		}

		if (isset($perm['*'])) {
			return $key === 'w' ? $perm['*'] === 'w' : (bool)$perm['*'];
		}

		return false;
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
				'admin/error/permission'
			);

			if (!in_array($path, $ignore) && !$this->can('r', $path)) {
				return false;
			}
		}

		return true;
	}

	public function addMeta($user_id, $key, $value)
	{
		if (_is_object($value)) {
			$serialized = 1;
			$value      = serialize($value);
		} else {
			$serialized = 0;
		}

		$meta = array(
			'user_id'    => $user_id,
			'key'        => $key,
			'value'      => $value,
			'serialized' => $serialized,
		);

		return $this->insert('user_meta', $meta);
	}

	public function setMeta($user_id, $key, $value)
	{
		$where = array(
			'user_id' => $user_id,
			'key'     => $key,
		);

		$this->delete('user_meta', $where);

		return $this->addMeta($user_id, $key, $value);
	}

	public function removeMeta($user_id, $key, $value = null)
	{
		$where = array(
			'user_id' => $user_id,
			'key'     => $key,
		);

		if ($value !== null) {
			if (_is_object($value)) {
				$value = serialize($value);
			}

			$where['value'] = $value;
		}

		return $this->delete('user_meta', $where);
	}

	public function getMeta($user_id, $key, $single = true)
	{
		if ($single) {
			return $this->queryRow("SELECT * FROM {$this->t['user_meta']} WHERE user_id = " . (int)$user_id . " AND `key` = '" . $this->escape($key) . "' LIMIT 1");
		}

		return $this->queryRows("SELECT * FROM {$this->t['user_meta']} WHERE user_id = " . (int)$user_id . " AND `key` = '" . $this->escape($key) . "'");
	}

	public function alert($user_id, $type, $key, $message)
	{
		if ($user_id !== $this->user_id) {
			$alerts              = $this->getAlerts($user_id);
			$alerts[$type][$key] = $message;
			$this->Model_User->addMeta($user_id, 'alert', $alerts);
		} else {
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

		if ($user_id !== $this->user_id) {
			return (array)$this->Model_User->getMeta($user_id, 'alert', false);
		}

		if ($this->alerts === null) {
			if (!isset($_SESSION['user_alerts'])) {
				$_SESSION['user_alerts'] = array();
			}

			$this->alerts = &$_SESSION['user_alerts'];
			$this->alerts += (array)$this->Model_User->getMeta($user_id, 'alert', false);
		}

		return $this->alerts;
	}

	public function fetchAlerts($user_id = null)
	{
		$alerts = $this->getAlerts($user_id);

		if (!$user_id || $user_id === $this->user_id) {
			unset($_SESSION['user_alerts']);
		}

		$this->Model_User->deleteMeta($user_id, 'alert');

		return $alerts;
	}

	public function renderAlerts($user_id = null, $style = 'inline')
	{
		$alerts = new Message(false);
		$alerts->set($this->fetchAlerts($user_id));

		return $alerts->render(null, true, $style);
	}

	public function isAdmin()
	{
		$admin_types = array(
			"Administrator",
			"Top Administrator"
		);

		return in_array($this->info('role'), $admin_types);
	}

	public function isTopAdmin()
	{
		return $this->info('role') === "Top Administrator";
	}

	public function isLogged()
	{
		return $this->user_id ? true : false;
	}

	public function showAdminBar()
	{
		return $this->isLogged() && !_cookie('disable_admin_bar');
	}

	public function encrypt($password)
	{
		return password_hash($password, PASSWORD_DEFAULT, array('cost' => PASSWORD_COST));
	}

	public function requestReset($email)
	{
		if (!$this->Model_User->getTotalRecords(array('email' => $email))) {
			$this->error['email'] = _l("The E-Mail Address was not found in our records, please try again!");

			return false;
		}

		$code = $this->generateCode();

		$this->setResetCode($_POST['email'], $code);

		$email_data = array(
			'reset' => site_url('admin/user/reset_form', 'code=' . $code),
			'email' => $_POST['email'],
		);

		call('admin/mail/forgotten', $email_data);

		return true;
	}

	public function generatePassword()
	{
		return substr(str_shuffle(md5(microtime())), 0, (int)rand(10, 13));
	}

	public function setResetCode($email, $code)
	{
		$user = $this->Model_User->findRecord(array('email' => $email));

		if (!$user) {
			$this->error = _l("The email %s is not associated to an account.", $email);

			return false;
		}

		return $this->setMeta($user['user_id'], 'pass_reset_code', $code);
	}

	public function lookupResetCode($code)
	{
		if ($code) {
			return $this->queryVar("SELECT user_id FROM {$this->t['user_meta']} WHERE `key` = 'pass_reset_code' AND value = '" . $this->escape($code) . "'");
		}
	}

	public function clearResetCode($user_id)
	{
		$this->removeMeta($user_id, 'pass_reset_code');
	}

	public function generateCode()
	{
		return str_shuffle(md5(microtime(true) * rand()));
	}
}
