<?php
class Url extends Library
{
	private $url = '';
	private $ssl = '';
	private $rewrite = array();
	private $seo_url;
	private $secure_pages = array();
	private $aliases = array();

	public function __construct()
	{
		parent::__construct();

		$this->url = option('url');

		if (!$this->url) {
			$this->url = URL_SITE;
		}

		if (option('config_use_ssl')) {
			$this->ssl = option('ssl');

			//TODO - finish secure pages
			$this->secure_pages = $this->queryRows("SELECT * FROM " . DB_PREFIX . "secure_page");
		}

		$this->loadAliases();

		if (option('config_seo_url')) {
			$this->loadSeoUrl();
		}
	}

	public function getQuery()
	{
		$args = func_get_args();

		if (empty($args)) {
			return http_build_query($_GET); //We do not use the query string for SEO URLs to function
		}

		$query = array();

		foreach ($_GET as $key => $value) {
			if (in_array($key, $args)) {
				$query[$key] = $value;
			}
		}

		return http_build_query($query);
	}

	public function getQueryExclude()
	{
		$args = func_get_args();

		if (empty($args)) {
			return http_build_query($_GET); //We do not use the query string for SEO URLs to function
		}

		$query = array();

		foreach ($_GET as $key => $value) {
			if (!in_array($key, $args)) {
				$query[$key] = $value;
			}
		}

		return http_build_query($query);
	}

	public function here($append_query = '')
	{
		return $this->link($this->route->getPath(), $this->getQuery() . '&' . $append_query);
	}

	public function reload_page()
	{
		header("Location: " . $this->here());
		exit;
	}

	public function load($url, $admin = false)
	{

		if ($admin) {
			//we save the session to the DB because we lose sessions when using cURL
			$this->session->saveTokenSession();
		}

		session_write_close();

		$ch      = curl_init();
		$timeout = 5;

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

		if ($admin) {
			curl_setopt($ch, CURLOPT_COOKIE, 'token=' . $this->session->get('token'));
		}

		$data = curl_exec($ch);

		curl_close($ch);

		return $data;
	}

	public function getSeoUrl()
	{
		return $this->seo_url;
	}

	public function store($store_id, $path = '', $query = '', $ssl = false)
	{
		static $stores;

		if (!$stores) {
			$stores = $this->queryRows("SELECT * FROM " . DB_PREFIX . "store", 'store_id');
		}

		if (!empty($stores[$store_id])) {
			$url = $ssl ? $stores[$store_id]['ssl'] : $stores[$store_id]['url'];
		} else {
			$url = URL_SITE;
		}

		return $url . $this->findAlias($path, $query, $store_id);
	}

	public function link($path, $query = '', $ssl = false)
	{
		return ($ssl ? $this->ssl : $this->url) . $this->findAlias($path, $query);
	}

	public function site($uri = '', $query = '', $base_site = false)
	{
		return ($base_site ? URL_SITE : $this->url) . $uri . (!empty($query) ? "?$query" : '');
	}

	public function urlencode_link($uri = '', $query = '')
	{
		return preg_replace("/%26amp%3B/i", "%26", urlencode($this->link($uri, $query)));
	}

	public function format($url)
	{
		$patterns = array(
			"/[^A-Za-z0-9\/\\\\]+/" => "-",
			"/(^-)|(-$)/"           => '',
			"/[\/\\\\]-/"           => "/",
		);

		return preg_replace(array_keys($patterns), array_values($patterns), strtolower($url));
	}

	public function decodeURIcomponent($uri)
	{
		$patterns     = array(
			'/&gt;/',
			'/&lt;/'
		);
		$replacements = array(
			'>',
			'<'
		);
		return preg_replace($patterns, $replacements, rawurldecode($uri));
	}

	public function addRewrite($rewrite)
	{
		$this->rewrite[] = $rewrite;
	}

	/**
	 * Redirect the request to a new URL.
	 *
	 * @param $url - The full url or the controller path. If the full URL (eg: starting with http(s):// ) is given, Url::redirect() will ignore $query.
	 * @param mixed $query - a string URI or associative array to be converted into a string URI
	 * @param int $status - The header redirect status to send back to the requesting client.
	 */

	public function redirect($url, $query = '', $status = 302)
	{
		//Check if this is a controller path
		if (!preg_match("/https?:\\/\\//", $url)) {
			$url = $this->link($url, $query);
		}

		header('Location: ' . str_replace('&amp;', '&', $url), true, $status);
		exit();
	}

	private function loadSeoUrl()
	{
		$url_alias = $this->lookupAlias($this->route->getPath(), $this->getQuery());

		if ($url_alias) {
			//Get Original Query without URL Alias query
			parse_str($url_alias['query'], $alias_query);

			$query = $this->getQueryExclude($alias_query);

			$_GET += $alias_query;

			$this->route->setPath($url_alias['path']);

			//Build the New URL
			$this->seo_url = $this->site_url . $url_alias['alias'] . ($query ? '?' . $query : '');
		} else {
			$this->seo_url = $this->here();
		}
	}

	private function findAlias($path = '', $query = '')
	{
		if (is_array($query)) {
			$query_str = http_build_query($query);
		} else {
			$query_str = urldecode($query);
			parse_str($query, $args);
			$query = $args;
		}

		//If already an alias, or has a URL scheme (eg: http://, ftp:// etc..) skip lookup
		if (!$path || isset($this->aliases[$path]) || parse_url($path, PHP_URL_SCHEME) || strpos($path, '//') === 0) {
			if (!$path) {
				$path = URL_SITE;
			}

			if ($query_str) {
				$path .= (strpos($path, '?') === false ? '?' : '&') . $query_str;
			}

			return $path;
		}

		$url_alias = $this->lookupAlias($path, $query_str);

		//Get Original Query without URL Alias query
		if ($url_alias) {
			$path = $url_alias['alias'];

			parse_str($url_alias['query'], $alias_query);
			$query = array_diff_assoc($query, $alias_query);
		}

		//Build the New URL
		return $path . ($query ? ((strpos($path, '?') === false) ? '?' : '&') . http_build_query($query) : '');
	}

	public function lookupAlias($path, $query)
	{
		if (isset($this->aliases[$path])) {
			return $this->aliases[$path];
		}

		//Lookup URL Alias
		foreach ($this->aliases as $alias) {
			if (preg_match("|^" . $alias['path'] . "|", $path)) {
				if (!$alias['query'] || preg_match("|" . $alias['query'] . "|", $query)) {
					return $alias;
				}
			}
		}
	}

	public function getAlias($path, $query = '')
	{
		return $this->queryVar("SELECT alias FROM " . $this->prefix . "url_alias WHERE `path` = '" . $this->escape($path) . "' AND `query` = '" . $this->escape($query) . "'");
	}

	public function setAlias($alias, $path, $query = '')
	{
		$this->removeAlias($path, $query);

		if ($alias) {
			$url_alias = array(
				'alias'    => $alias,
				'path'     => $path,
				'query'    => $query,
				'status'   => 1,
			);

			return $this->Model_Setting_UrlAlias->addUrlAlias($url_alias);
		}

		return true;
	}

	public function removeAlias($path, $query = '', $alias = '')
	{
		$sql_query =
			"SELECT url_alias_id FROM " . DB_PREFIX . "url_alias" .
			" WHERE `path` = '" . $this->escape($path) . "'" .
			" AND `query` = '" . $this->escape($query) . "'";

		if ($alias) {
			$sql_query .= " AND alias = '" . $this->escape($alias) . "'";
		}

		$url_alias_ids = $this->queryColumn($sql_query);

		foreach ($url_alias_ids as $url_alias_id) {
			$this->Model_Setting_UrlAlias->deleteUrlAlias($url_alias_id);
		}

		return $this->Model_Setting_UrlAlias->hasError();
	}

	public function loadAliases()
	{
		$this->aliases = cache('url_alias.all');

		if (is_null($this->aliases)) {
			$this->aliases = $this->queryRows("SELECT * FROM " . DB_PREFIX . "url_alias WHERE status = 1", 'alias');

			cache('url_alias.all', $this->aliases);
		}
	}
}
