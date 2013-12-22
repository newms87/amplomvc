<?php
class User extends Library
{
	private $user_id;
	private $user;
	private $permissions = array();

	private $temp_user;

	public function __construct($registry)
	{
		parent::__construct($registry);

		if (isset($this->session->data['user_id']) && $this->validate_token()) {

			$user = $this->queryRow("SELECT * FROM " . DB_PREFIX . "user WHERE user_id = '" . (int)$this->session->data['user_id'] . "' AND status = '1'");

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
		   'user_id' => $this->user_id,
		);

		$this->user_id = -1;
		$this->user['group_type'] = "Top Administrator";
	}

	public function logoutSystemUser()
	{
		$this->user_id = $this->temp_user['user_id'];
		$this->user['group_type'] = $this->temp_user['group_type'];
	}

	private function loadUser($user)
	{
		$this->user_id = $user['user_id'];
		$this->session->set('user_id', $user['user_id']);

		$user_group = $this->queryRow("SELECT name as group_type, permission FROM " . DB_PREFIX . "user_group WHERE user_group_id = '" . (int)$user['user_group_id'] . "'");

		$this->permissions = unserialize($user_group['permission']);

		$this->user = $user + $user_group;

		//TODO: Do we need this??
		$this->query("UPDATE " . DB_PREFIX . "user SET ip = '" . $this->escape($_SERVER['REMOTE_ADDR']) . "' WHERE user_id = '" . (int)$user['user_id'] . "'");
	}

	public function validate_token()
	{
		if (!empty($this->session->data['token']) && !empty($_COOKIE['token']) && $_COOKIE['token'] === $this->session->data['token']) {
			return true;
		}

		if (isset($this->session->data['user_id'])) {
			$this->message->add("notify", "Your session has expired. Please log in again.");
		}

		$this->logout();

		return false;
	}

	public function login($username, $password)
	{
		$username = $this->escape($username);

		$user = $this->queryRow("SELECT * FROM `" . DB_PREFIX . "user` WHERE (username = '$username' OR email='$username') AND status = '1'");

		if ($user) {
			if (!password_verify($password, $user['password'])) {
				return false;
			}

			$this->loadUser($user);

			$this->session->setToken();
			$this->session->saveTokenSession();

			return true;
		}

		return false;
	}

	public function logout()
	{
		$this->user = null;
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

	public function updatePassword($user_id, $password)
	{
		$this->Model_User_User->editPassword($user_id, $password);
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
}
