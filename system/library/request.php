<?php

class Request extends Library
{
	public function __construct()
	{
		parent::__construct();

		$action = array(
			$this,
			'clean',
		);

		array_walk_recursive($_GET, $action);
		array_walk_recursive($_POST, $action);
		array_walk_recursive($_REQUEST, $action);
		array_walk_recursive($_COOKIE, $action);
		array_walk_recursive($_SERVER, $action);

		if (empty($_SESSION['page_requests'])) {
			$_SESSION['page_requests'] = array();
		}

		if (!IS_AJAX) {
			$query                       = http_build_query($_GET);
			$_SESSION['page_requests'][] = $this->route->getPath() . ($query ? '?' . $query : '');
		}
	}

	public function clean(&$value)
	{
		$value = html_entity_decode($value, ENT_COMPAT);

		if (get_magic_quotes_gpc()) {
			$value = stripslashes($value);
		}
	}

	public function get($key, $default = null)
	{
		return isset($_GET[$key]) ? $_GET[$key] : $default;
	}

	public function post($key, $default = null)
	{
		return isset($_POST[$key]) ? $_POST[$key] : $default;
	}

	public function request($key, $default = null)
	{
		return isset($_REQUEST[$key]) ? $_REQUEST[$key] : $default;
	}

	public function hasRedirect($context = '')
	{
		$key = $context ? 'redirect_' . $context : 'redirect';
		return !empty($_SESSION[$key]);
	}

	public function doRedirect($context = '')
	{
		if ($this->hasRedirect($context)) {
			redirect($this->fetchRedirect($context));
		}
	}

	public function fetchRedirect($context = '')
	{
		$redirect = $this->getRedirect($context);

		$this->clearRedirect($context);

		return $redirect;
	}

	public function setRedirect($url = '', $query = '', $context = '')
	{
		$key = $context ? 'redirect_' . $context : 'redirect';
		$this->session->set($key, site_url($url, $query));
	}

	public function clearRedirect($context = '')
	{
		$key = $context ? 'redirect_' . $context : 'redirect';
		unset($_SESSION[$key]);
	}

	public function getRedirect($context = '')
	{
		$key = $context ? 'redirect_' . $context : 'redirect';
		return !empty($_SESSION[$key]) ? $_SESSION[$key] : null;
	}

	/**
	 * Redirect the browser by sending a javascript redirect call.
	 * Warning: This will only work if the users browser has JS enabled! Make sure this is the case.
	 *
	 * @param $url - The full url or the controller path. If the full URL (eg: starting with http(s):// ) is given, Url::redirect() will ignore $query.
	 * @param mixed $query - a string URI or associative array to be converted into a string URI
	 */
	public function redirectBrowser($url)
	{
		echo "<script type=\"text/javascript\">location=\"$url\"</script>";
		exit;
	}

	public function getPrevPageRequest($offset = -2)
	{
		return current(array_slice($_SESSION['page_requests'], $offset, 1));
	}
}
