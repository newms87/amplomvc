<?php
class Session extends Library
{
	public $data = array();
			
  	public function __construct($registry)
  	{
		parent::__construct($registry);
		
		if (!session_id()) {
			ini_set('session.use_cookies', 'On');
			ini_set('session.use_trans_sid', 'Off');
			
			session_name(AMPLOCART_SESSION);
			
			session_set_cookie_params(0, '/', COOKIE_DOMAIN);
			
			session_start();
		}
		
		$this->data =& $_SESSION;
		
		//TODO: validate this is safe? Since the token has to be in database and we will only save to db right before calling an admin page only.
		
		//These will load the session / token if we are using curlopt
		if (!isset($this->data['token']) && isset($_COOKIE['token'])) {
			$this->loadTokenSession($_COOKIE['token']);
			unset($this->data['session_token_saved']);
		}
		
		//refresh this logged in session
		if (isset($_COOKIE['token'])) {
			$this->setCookie('token', $_COOKIE['token'], 3600);
			if (isset($this->data['session_token_saved'])) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "session WHERE `ip` = '" . $this->db->escape($_SERVER['REMOTE_ADDR']) . "'");
				unset($this->data['session_token_saved']);
			}
		}
		elseif (isset($this->data['token']) && empty($_COOKIE)) {
			unset($this->data['token']);
			$this->data['messages']['warning'][] = "You must enable cookies to login to the admin portal!";
			$this->url->redirect($this->url->link('common/home'));
			exit();
		}
		elseif (!isset($this->data['session_token_saved'])) {
			$ip_session_exists = $this->db->queryVar("SELECT COUNT(*) as total FROM " . DB_PREFIX . "session WHERE ip = '" . $_SERVER['REMOTE_ADDR'] . "'");
			
			if ($ip_session_exists) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "session WHERE `ip` = '" . $this->db->escape($_SERVER['REMOTE_ADDR']) . "'");
				$this->data['messages']['warning'][] = "You must enable cookies to login to the admin portal!";
			}
		}
	}
	
	public function loadTokenSession($token)
	{
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "session WHERE `token` = '" . $this->db->escape($token) . "' LIMIT 1");
			
		if ($query->num_rows) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "session WHERE `token` = '" . $this->db->escape($token) . "'");
			$this->data['token'] = $query->row['token'];
			$this->data['user_id'] = $query->row['user_id'];
		
			if ($query->row['data']) {
				$this->data += unserialize($query->row['data']);
			}
		}
	}
	
	public function saveTokenSession()
	{
		if (empty($this->data['token']) || empty($this->data['user_id'])) {
			return false;
		}
		
		$this->db->query("INSERT INTO " . DB_PREFIX . "session SET `token` = '" . $this->db->escape($this->data['token']) . "', `user_id` = '" . $this->db->escape($this->data['user_id']) . "', `data` = '" . $this->db->escape(serialize($this->data)) . "', `ip` = '" . $_SERVER['REMOTE_ADDR'] . "'");
		$this->data['session_token_saved'] = 1;
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
	
	public function setCookie($name, $value, $expire = 3600)
	{
		//TODO: ADD EXPIRATION TIME BACK IN! Remove because Chrome was not working
		setcookie($name, $value, time() + $expire, '/', COOKIE_DOMAIN);
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
		$this->setCookie("token", $token, 3600);
		$this->data['token'] = $token;
	}
}
