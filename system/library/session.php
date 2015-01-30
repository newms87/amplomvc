<?php

class Session extends Library
{
	public function __construct()
	{
		parent::__construct();

		//TODO: validate this is safe? Since the token has to be in database and we will only save to db right before calling an admin page only.

		//These will load the session / token if we are using curlopt
		if (!isset($_SESSION['token']) && isset($_COOKIE['token'])) {
			$this->loadTokenSession($_COOKIE['token']);
			unset($_SESSION['session_token_saved']);
		}

		//refresh this logged in session
		if (isset($_COOKIE['token'])) {
			$this->setCookie('token', $_COOKIE['token'], AMPLO_SESSION_TIMEOUT);
			if (isset($_SESSION['session_token_saved'])) {
				$this->query("DELETE FROM {$this->t['session']} WHERE `ip` = '" . $this->escape($_SERVER['REMOTE_ADDR']) . "'");
				unset($_SESSION['session_token_saved']);
			}
		} elseif (isset($_SESSION['token']) && empty($_COOKIE)) {
			unset($_SESSION['token']);
			message('warning', _l("You must enable cookies to login to the admin portal!"));
			redirect();
		} elseif (!isset($_SESSION['session_token_saved'])) {
			$ip_session_exists = $this->queryVar("SELECT COUNT(*) as total FROM {$this->t['session']} WHERE ip = '" . $_SERVER['REMOTE_ADDR'] . "'");

			if ($ip_session_exists) {
				$this->query("DELETE FROM {$this->t['session']} WHERE `ip` = '" . $this->escape($_SERVER['REMOTE_ADDR']) . "'");
				message('warning', _l("Unable to authenticate user. Please check that cookies are enabled."));
			}
		}
	}

	public function has($key)
	{
		return isset($_SESSION[$key]);
	}

	public function get($key)
	{
		return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
	}

	public function set($key, $value)
	{
		$_SESSION[$key] = $value;
	}

	public function remove($key)
	{
		unset($_SESSION[$key]);
	}

	public function loadTokenSession($token)
	{
		$query = $this->query("SELECT * FROM {$this->t['session']} WHERE `token` = '" . $this->escape($token) . "' LIMIT 1");

		if ($query->num_rows) {
			$this->query("DELETE FROM {$this->t['session']} WHERE `token` = '" . $this->escape($token) . "'");
			$_SESSION['token']   = $query->row['token'];
			$_SESSION['user_id'] = $query->row['user_id'];

			if ($query->row['data']) {
				$_SESSION += unserialize($query->row['data']);
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

		$this->insert('session', $session);

		$_SESSION['session_token_saved'] = 1;
	}

	public function endTokenSession()
	{
		$this->deleteCookie('token');

		$this->end();
	}

	public function end()
	{
		$to_save = array(
			'messages' => 1,
			'language' => 1,
			'redirect' => 1,
		);

		$_SESSION = array_intersect_key($_SESSION, $to_save);
	}

	public function getCookie($name)
	{
		return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
	}

	public function setCookie($name, $value, $expire = 31536000)
	{
		if (!headers_sent()) {
			$expire = $expire ? time() + $expire : 0;
			return setcookie($name, $value, $expire, '/', COOKIE_DOMAIN);
		}

		$this->error['headers'] = _l("Unable to set cookie because headers were already sent!");

		return false;
	}

	public function deleteCookie($name)
	{
		$this->setCookie($name, '', -3600);
	}

	public function setToken($token = null)
	{
		if (!$token) {
			$token = md5(mt_rand());
		}

		$this->setCookie("token", $token, AMPLO_SESSION_TIMEOUT);
		$_SESSION['token'] = $token;
	}
}
