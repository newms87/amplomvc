<?php
class User extends Library
{
	private $user_id;
	private $user;
	private $permissions = array();

	private $temp_user;

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
			'group_type' => $this->user['group_type'],
			'user_id'    => $this->user_id,
		);

		$this->user_id            = -1;
		$this->user['group_type'] = "Top Administrator";
	}

	public function logoutSystemUser()
	{
		$this->user_id            = $this->temp_user['user_id'];
		$this->user['group_type'] = $this->temp_user['group_type'];
	}

	private function loadUser($user)
	{
		$this->user_id = $user['user_id'];
		$this->session->set('user_id', $user['user_id']);

		$user_group = $this->queryRow("SELECT name as group_type, permission FROM " . DB_PREFIX . "user_group WHERE user_group_id = '" . (int)$user['user_group_id'] . "'");

		if (!$user_group) {
			$msg = _l("User was assigned an invalid group!");
			$this->error_log->write($msg);
			$this->message->add('error', $msg);
			return;
		}

		$this->permissions = unserialize($user_group['permission']);

		$this->user = $user + $user_group;
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
			$this->message->add("notify", "Your session has expired. Please log in again.");
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

		if (isset($this->permissions[$key])) {
			return in_array($value, $this->permissions[$key]);
		}

		return false;
	}

	public function canDoAction($action)
	{
		if (!$this->isAdmin()) {
			return true;
		}

		$path = $action->getControllerPath();

		if (!$this->isLogged()) {
			$allowed = array(
				'common/forgotten',
				'common/reset',
				'common/login',
			);

			if (!in_array($path, $allowed)) {
				return false;
			}
		} else {
			$ignore = array(
				'common/home',
				'common/login',
				'common/logout',
				'common/forgotten',
				'common/reset',
				'error/not_found',
				'error/permission'
			);

			if (!in_array($path, $ignore)) {
				if (!$this->can('access', $path)) {
					return false;
				}

				$class = $action->getClass();
				$method = $action->getMethod();

				if (property_exists($class, 'can_modify') && in_array($method, $class::$can_modify)) {
					if (!$this->can('modify', $path)) {
						return false;
					}
				}
			}
		}

		return true;
	}

	public function updatePassword($user_id, $password)
	{
		$this->Model_User_User->editPassword($user_id, $password);
	}

	public function addMeta($user_id, $key, $value)
	{
		if (is_array($value) || is_object($value) || is_resource($value)) {
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
		   'key' => $key,
		);

		if (!is_null($value)) {
			if (is_array($value) || is_object($value) || is_resource($value)) {
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
				return $this->can('modify', 'catalog/flashsale');
			case 'designer':
				return $this->can('modify', 'catalog/designer');
			case 'product':
				return $this->can('modify', 'catalog/product');
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

		return in_array($this->info('group_type'), $admin_types);
	}

	public function isTopAdmin()
	{
		return $this->info('group_type') === "Top Administrator";
	}

	public function isLogged()
	{
		return $this->user_id ? true : false;
	}

	public function showAdminBar()
	{
		return $this->isLogged();
	}

	public function info($key)
	{
		return isset($this->user[$key]) ? $this->user[$key] : null;
	}

	public function getId()
	{
		return $this->user_id;
	}

	public function encrypt($password)
	{
		return password_hash($password, PASSWORD_DEFAULT, array('cost' => PASSWORD_COST));
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
			return $this->queryVar("SELECT user_id FROM " . DB_PREFIX . "user_meta WHERE `key` = 'pass_reset_code' AND value = " . $this->escape($code) . "'");
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
