<?php

class User extends Library
{
	protected $user_id;
	protected $user;
	protected $permissions = array();

	protected $temp_user;

	public function __construct()
	{
		parent::__construct();

		if (isset($_SESSION['user_id']) && $this->validate_token()) {
			$user = $this->queryRow("SELECT * FROM " . DB_PREFIX . "user WHERE user_id = '" . (int)$_SESSION['user_id'] . "' AND status = '1'");

			if ($user) {
				$this->loadUser($user);
			} else {
				$this->logout();
			}
		}
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

	protected function loadUser($user)
	{
		$this->user_id = $user['user_id'];
		$this->session->set('user_id', $user['user_id']);

		$user_role = $this->Model_Setting_Role->getRole($user['user_role_id']);

		if ($user_role) {
			$this->permissions = $user_role['permissions'];
			$user['role']      = $user_role['name'];
		} else {
			$this->permissions = array();
			$user['role']      = '';
		}

		$this->user = $user;
	}

	public function lookupUserByEmail($email)
	{
		return $this->queryRow("SELECT * FROM " . DB_PREFIX . "user WHERE email = '$email'");
	}

	public function validate_token()
	{
		if (!empty($_SESSION['token']) && !empty($_COOKIE['token']) && $_COOKIE['token'] === $_SESSION['token']) {
			return true;
		}

		if (isset($_SESSION['user_id'])) {
			message("notify", "Your session has expired. Please log in again.");
		}

		$this->logout();

		return false;
	}

	public function login($username, $password)
	{
		$username = $this->escape($username);

		$user = $this->queryRow("SELECT * FROM `" . DB_PREFIX . "user` WHERE (username = '$username' OR LCASE(email) = '" . strtolower($username) . "') AND status = '1'");

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

		$path = $action->getClassPath() . '/' . $action->getMethod();

		if (!is_logged()) {
			$allowed = array(
				'admin/user/forgotten',
				'admin/user/reset_request',
				'admin/user/reset',
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
				'admin/error/not_found',
				'admin/error/permission'
			);

			if (!in_array($path, $ignore)) {
				if (!$this->can('r', $path)) {
					return false;
				}

				$class  = $action->getClass();
				$method = $action->getMethod();

				if (property_exists($class, 'allow')) {
					$allow = $class::$allow;

					if (!empty($allow['modify']) && in_array($method, $allow['modify'])) {
						if (!$this->can('w', $path)) {
							return false;
						}
					}
				}
			}
		}

		return true;
	}

	public function updatePassword($user_id, $password)
	{
		$this->Model_User->editPassword($user_id, $password);
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

	public function deleteMeta($user_id, $key, $value = null)
	{
		$where = array(
			'user_id' => $user_id,
			'key'     => $key,
		);

		if (!is_null($value)) {
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
			return $this->queryRow("SELECT * FROM " . DB_PREFIX . "user_meta WHERE user_id = " . (int)$user_id . " AND `key` = '" . $this->escape($key) . "' LIMIT 1");
		}

		return $this->queryRows("SELECT * FROM " . DB_PREFIX . "user_meta WHERE user_id = " . (int)$user_id . " AND `key` = '" . $this->escape($key) . "'");
	}

	//TODO: Make this current
	public function canPreview($type)
	{
		switch ($type) {
			case 'flashsale':
				return $this->can('w', 'catalog/flashsale');
			case 'designer':
				return $this->can('w', 'catalog/designer');
			case 'product':
				return $this->can('w', 'catalog/product');
			default:
				return false;
		}
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
		return $this->isLogged() && empty($_COOKIE['disable_admin_bar']);
	}

	public function info($key = null)
	{
		if ($key) {
			return isset($this->user[$key]) ? $this->user[$key] : null;
		}

		return $this->user;
	}

	public function getId()
	{
		return $this->user_id;
	}

	public function encrypt($password)
	{
		return password_hash($password, PASSWORD_DEFAULT, array('cost' => PASSWORD_COST));
	}

	public function requestReset($email)
	{
		$filter = array(
			'email' => $email,
		);

		if (!$this->Model_User->getTotalUsers($filter)) {
			$this->error['email'] = _l("Warning: The E-Mail Address was not found in our records, please try again!");
			return false;
		}

		$code = $this->generateCode();

		$this->setResetCode($_POST['email'], $code);

		$email_data = array(
			'reset' => site_url('admin/user/reset', 'code=' . $code),
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
		$user = $this->lookupUserByEmail($email);

		if (!$user) {
			$this->error = _l("The email %s is not associated to an account.", $email);
			return false;
		}

		return $this->setMeta($user['user_id'], 'pass_reset_code', $code);
	}

	public function lookupResetCode($code)
	{
		if ($code) {
			return $this->queryVar("SELECT user_id FROM " . DB_PREFIX . "user_meta WHERE `key` = 'pass_reset_code' AND value = '" . $this->escape($code) . "'");
		}
	}

	public function clearResetCode($user_id)
	{
		$this->deleteMeta($user_id, 'pass_reset_code');
	}

	public function generateCode()
	{
		return str_shuffle(md5(microtime(true) * rand()));
	}
}
