<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC
 * @link http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt files in the root directory.
 */

class User extends Library
{
	protected
		$user,
		$alerts,
		$temp_user;

	public function __construct()
	{
		parent::__construct();

		$this->loadTokenSession();

		$this->validateUser();
	}

	public function getId()
	{
		return $this->user ? $this->user['user_id'] : null;
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
		if (!$this->user) {
			return null;
		}

		if ($key) {
			return isset($this->user['meta'][$key]) ? $this->user['meta'][$key] : $default;
		}

		return $this->user['meta'];
	}

	public function loginSystemUser()
	{
		//Change User Role and user ID to the system user
		$this->temp_user = $this->user;

		$user = array(
			'user_id' => -1,
			'role'    => array(
				'user_role_id' => 1,
				'type'         => App_Model_UserRole::TYPE_ADMIN,
				'name'         => 'Top Administrator',
			)
		);

		$this->setUser($user);
	}

	public function logoutSystemUser()
	{
		$this->user = $this->temp_user;
	}

	public function setUser($user = array())
	{
		$user += array(
			'user_id' => 0,
			'role'    => array(),
			'meta'    => array(),
		);

		if ($user['role']) {
			$user['user_role_id'] = $user['role']['user_role_id'];
			$user['role_name']    = $user['role']['name'];
			$user['role_type']    = $user['role']['type'];
		}

		$this->user = $user;
	}

	public function loadUser($user_id)
	{
		$user = _session('user');

		if (!$user || $user['user_id'] !== $user_id) {
			$user = $this->Model_User->getRecord($user_id);

			if ($user) {
				$user['meta'] = $this->Model_Meta->get('user', $user_id);

				$_SESSION['user_id'] = $user_id;
				$_SESSION['user']    = $user;
			}
		}

		if ($user) {
			$user['role'] = $this->Model_UserRole->getRole($user['user_role_id']);

			$this->setUser($user);

			return true;
		}

		return false;
	}

	public function validateUser()
	{
		$user_id = (int)_session('user_id');

		if ($user_id) {
			$session_token = _session('token');
			$cookie_token  = _cookie('token');

			if ($session_token && $cookie_token === $session_token) {
				return $this->loadUser($user_id);
			}

			message("notify", "Your session has expired. Please log in again.");
			$this->logout();

			if ($this->router->getPath() !== 'user/logout') {
				$this->request->setRedirect($this->url->here());
			}
		}

		return false;
	}

	public function login($username, $password)
	{
		$user = $this->queryRow("SELECT * FROM `{$this->t['user']}` WHERE (username = '$username' OR email = '" . $this->escape($username) . "') AND status = 1");

		if ($user) {
			if (!password_verify($password, $user['password'])) {
				$this->error['password'] = _l("The username / password combination was unable to be authenticated.");

				return false;
			}

			$this->loadUser($user['user_id']);

			$this->setToken();
			$this->saveTokenSession();

			return true;
		}

		$this->error = _l("Unable to authenticate your log in credentials.");

		return false;
	}

	public function logout()
	{
		$this->user = null;

		$this->endTokenSession();
	}

	public function is($roles)
	{
		if ($this->user && $this->user['role']) {
			return in_array($this->user['role']['type'], (array)$roles) || in_array($this->user['role']['name'], (array)$roles);
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
		if ($this->user) {
			if (is_array($key)) {
				return $this->Model_Meta->setAll('user', $this->user['user_id'], $key);
			}

			return $this->Model_Meta->set('user', $this->user['user_id'], $key, $value);
		}
	}

	public function removeMeta($key, $value = null)
	{
		if ($this->user) {
			return $this->Model_Meta->removeKey('user', $this->user['user_id'], $key, $value);
		}
	}

	public function alert($user_id, $type, $key, $message)
	{
		//Save Alert for when user logs in
		if ($this->user && $user_id !== $this->user['user_id']) {
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
		if (!$this->user) {
			return array();
		}

		$user_id = $user_id ? $user_id : $this->user['user_id'];

		$alerts = (array)$this->Model_Meta->get('user', $user_id, 'alert');

		//Get only Save alerts (if user is not logged in)
		if ($user_id !== $this->user['user_id']) {
			return $alerts;
		}

		if ($this->alerts === null) {
			if (!isset($_SESSION['user_alerts'])) {
				$_SESSION['user_alerts'] = array();
			}

			//Get alerts for current user
			$this->alerts = &$_SESSION['user_alerts'];
			$this->alerts += $alerts;
		}

		return $this->alerts;
	}

	public function fetchAlerts($user_id = null)
	{
		$alerts = $this->getAlerts($user_id);

		if ($alerts) {
			if ($user_id === $this->user['user_id']) {
				unset($_SESSION['user_alerts']);
			}

			$this->Model_Meta->removeKey('user', $user_id, 'alert');
		}

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
		return $this->user ? $this->user['role']['name'] === 'Top Administrator' : false;
	}

	public function isLogged()
	{
		return $this->user ? true : false;
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

	protected function loadTokenSession()
	{
		$session_token = _session('token');
		$cookie_token  = _cookie('token');

		//These will load the session / token if we are using curlopt
		if (!$session_token && $cookie_token) {
			$session = $this->Model_Session->findRecord(array('token' => $cookie_token), '*');

			if ($session) {
				$this->Model_Session->remove($session['session_id']);

				$_SESSION['token']   = $session['token'];
				$_SESSION['user_id'] = $session['user_id'];

				if ($session['data']) {
					$_SESSION += unserialize($session['data']);
				}
			}

			unset($_SESSION['session_token_saved']);
		}

		if ($cookie_token) {
			//Refresh the token
			set_cookie('token', $cookie_token, AMPLO_SESSION_TIMEOUT);

			if (isset($_SESSION['session_token_saved'])) {
				$this->Model_Session->removeWhere(array('ip' => $_SERVER['REMOTE_ADDR']));
				unset($_SESSION['session_token_saved']);
			}
		} elseif ($session_token && empty($_COOKIE)) {
			unset($_SESSION['token']);
			message('warning', _l("You must enable cookies to use this system!"));
			redirect();
		} elseif (!isset($_SESSION['session_token_saved'])) {
			$ip_session_exists = $this->Model_Session->getTotalRecords(array('ip' => $_SERVER['REMOTE_ADDR']));

			if ($ip_session_exists) {
				$this->Model_Session->removeWhere(array('ip' => $_SERVER['REMOTE_ADDR']));
				message('warning', _l("Unable to authenticate user. Please check that cookies are enabled."));
			}
		}
	}

	public function saveTokenSession()
	{
		if (empty($_SESSION['token']) || empty($_SESSION['user_id'])) {
			return false;
		}

		$session = array(
			'token'   => $_SESSION['token'],
			'user_id' => $_SESSION['user_id'],
			'data'    => serialize($_SESSION),
			'ip'      => $_SERVER['REMOTE_ADDR'],
		);

		$_SESSION['session_token_saved'] = $this->Model_Session->save(null, $session);
	}

	public function endTokenSession()
	{
		delete_cookie('token');

		$to_save = array(
			'messages' => 1,
			'language' => 1,
			'redirect' => 1,
		);

		$_SESSION = array_intersect_key($_SESSION, $to_save);
	}

	public function setToken($token = null)
	{
		if (!$token) {
			$token = md5(mt_rand());
		}

		set_cookie('token', $token, AMPLO_SESSION_TIMEOUT);
		$_SESSION['token'] = $token;
	}
}
