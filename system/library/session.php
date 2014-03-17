<?php
class Session extends Library
{
	public $data = array();

	public function __construct($registry)
	{
		parent::__construct($registry);

		$this->data = & $_SESSION;
		//TODO: validate this is safe? Since the token has to be in database and we will only save to db right before calling an admin page only.

		//These will load the session / token if we are using curlopt
		if (!isset($_SESSION['token']) && isset($_COOKIE['token'])) {
			$this->loadTokenSession($_COOKIE['token']);
			unset($this->data['session_token_saved']);
		}

		//refresh this logged in session
		if (isset($_COOKIE['token'])) {
			$this->setCookie('token', $_COOKIE['token'], 3600);
			if (isset($_SESSION['session_token_saved'])) {
				$this->query("DELETE FROM " . DB_PREFIX . "session WHERE `ip` = '" . $this->escape($_SERVER['REMOTE_ADDR']) . "'");
				unset($_SESSION['session_token_saved']);
			}
		} elseif (isset($_SESSION['token']) && empty($_COOKIE)) {
			unset($_SESSION['token']);
			$this->message->add('warning', _l("You must enable cookies to login to the admin portal!"));
			$this->url->redirect('common/home');
		} elseif (!isset($_SESSION['session_token_saved'])) {
			$ip_session_exists = $this->queryVar("SELECT COUNT(*) as total FROM " . DB_PREFIX . "session WHERE ip = '" . $_SERVER['REMOTE_ADDR'] . "'");

			if ($ip_session_exists) {
				$this->query("DELETE FROM " . DB_PREFIX . "session WHERE `ip` = '" . $this->escape($_SERVER['REMOTE_ADDR']) . "'");
				$this->message->add('warning', _l("Unable to authenticate user. Please check that cookies are enabled."));
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

	public function loadTokenSession($token)
	{
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "session WHERE `token` = '" . $this->escape($token) . "' LIMIT 1");

		if ($query->num_rows) {
			$this->query("DELETE FROM " . DB_PREFIX . "session WHERE `token` = '" . $this->escape($token) . "'");
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

		$this->query("INSERT INTO " . DB_PREFIX . "session SET `token` = '" . $this->escape($_SESSION['token']) . "', `user_id` = '" . $this->escape($_SESSION['user_id']) . "', `data` = '" . $this->escape(serialize($_SESSION)) . "', `ip` = '" . $_SERVER['REMOTE_ADDR'] . "'");
		$_SESSION['session_token_saved'] = 1;
	}

	public function endTokenSession()
	{
		$this->deleteCookie('token');

		$this->deleteCookie(AMPLOCART_SESSION);

		$this->end();
	}

	public function end()
	{
		$to_save = array(
			'messages',
			'language',
		);

		foreach ($_SESSION as $key => $s) {
			if (!in_array($key, $to_save)) {
				unset($_SESSION[$key]);
			}
		}
	}

	public function getCookie($name)
	{
		return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
	}

	public function setCookie($name, $value, $expire = 0)
	{
		$expire = $expire ? time() + $expire : 0;
		setcookie($name, $value, $expire, '/', COOKIE_DOMAIN);
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

		$this->setCookie("token", $token, 0);
		$_SESSION['token'] = $token;
	}
}
