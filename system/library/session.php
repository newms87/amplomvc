<?php

class Session extends Library
{
	public function __construct()
	{
		parent::__construct();

		$this->loadTokenSession();
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

	protected function loadTokenSession()
	{
		$session_token = _session('token');
		$cookie_token = _cookie('token');

		//These will load the session / token if we are using curlopt
		if (!$session_token && $cookie_token) {
			$session = $this->queryRow("SELECT * FROM {$this->t['session']} WHERE `token` = '" . $this->escape($cookie_token) . "' LIMIT 1");

			if ($session) {
				$this->delete('session', array('token' => $cookie_token));
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
				$this->delete('session', array('ip' => $_SERVER['REMOTE_ADDR']));
				unset($_SESSION['session_token_saved']);
			}
		} elseif ($session_token && empty($_COOKIE)) {
			unset($_SESSION['token']);
			message('warning', _l("You must enable cookies to use this system!"));
			redirect();
		} elseif (!isset($_SESSION['session_token_saved'])) {
			$ip_session_exists = $this->queryVar("SELECT COUNT(*) FROM {$this->t['session']} WHERE ip = '" . $_SERVER['REMOTE_ADDR'] . "'");

			if ($ip_session_exists) {
				$this->delete('session', array('ip' => $_SERVER['REMOTE_ADDR']));
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

		$this->insert('session', $session);

		$_SESSION['session_token_saved'] = 1;
	}

	public function endTokenSession()
	{
		delete_cookie('token');

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

	public function setToken($token = null)
	{
		if (!$token) {
			$token = md5(mt_rand());
		}

		set_cookie('token', $token, AMPLO_SESSION_TIMEOUT);
		$_SESSION['token'] = $token;
	}
}
