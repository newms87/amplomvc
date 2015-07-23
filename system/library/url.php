<?php

class Url extends Library
{
	private
		$url = '',
		$ssl = '',
		$rewrite = array(),
		$secure_pages = array(),
		$aliases = array();

	public function __construct()
	{
		parent::__construct();

		$this->setSite($this->route->getSite());

		if (option('config_use_ssl')) {
			//TODO - finish secure pages
			$this->secure_pages = $this->queryRows("SELECT * FROM {$this->t['secure_page']}");
		}

		$this->loadAliases();
	}

	public function setSite($site)
	{
		//TODO: Test if always setting site to the URL that is currently being accessed is best policy.
		$this->url = URL_SITE;//isset($site['url']) ? $site['url'] : URL_SITE;

		$this->ssl = isset($site['ssl']) ? $site['ssl'] : HTTPS_SITE;
	}

	public function here($append_query = '')
	{
		if ($append_query) {
			if (is_string($append_query)) {
				parse_str($append_query, $append_query);
			}

			$query = $append_query + $_GET;
		} else {
			$query = $_GET;
		}

		return $this->link($this->route->getPath(), $query);
	}

	public function reload_page()
	{
		header("Location: " . $this->here());
		exit;
	}

	public function download($source, $destination = null)
	{
		if (!$destination) {
			$pathinfo    = pathinfo($destination);
			$destination = DIR_DOWNLOAD . 'url/' . $pathinfo['filename'];

			$count = 1;

			while (is_file($destination . $pathinfo['extension'])) {
				$destination = DIR_DOWNLOAD . 'url/' . $pathinfo['filename'] . '-' . $count++;
			}

			$destination .= $pathinfo['extension'];
		}

		if (!file_put_contents($destination, fopen($source, 'r'))) {
			$this->error['write'] = _l("Failed to download file to %s", $destination);

			return false;
		}

		return $destination;
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

	public function link($path, $query = '', $ssl = null, $site_id = null)
	{
		static $sites;

		if ($ssl === null) {
			$ssl = IS_SSL;
		}

		if ($site_id) {
			if (!$sites) {
				$sites = $this->route->getSites();
			}

			if (!empty($sites[$site_id])) {
				$url = $ssl ? $sites[$site_id]['ssl'] : $sites[$site_id]['url'];
			} else {
				$url = URL_SITE;
			}
		} else {
			$url = $ssl ? $this->ssl : $this->url;
		}

		return $this->findAlias($url, $path, $query, $site_id);
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

	public function redirect($url, $query = '', $ssl = null, $status = 302)
	{
		//Check if this is a controller path
		if (!preg_match("/https?:\\/\\//", $url)) {
			$url = $this->link($url, $query, $ssl);
		}

		header('Location: ' . str_replace('&amp;', '&', $url), true, $status);
		exit();
	}

	private function findAlias($base_url, $path = '', $query = '')
	{
		$lower_path = strtolower(str_replace('-', '_', $path));

		if (is_array($query)) {
			$query_str = $query ? http_build_query($query) : '';
		} else {
			$query_str = urldecode($query);
			parse_str($query, $query);
		}

		//If already has a URL scheme (eg: http://, ftp:// etc..) not an alias, and no base can be prepended
		$has_scheme = parse_url($path, PHP_URL_SCHEME) || strpos($path, '//') === 0;

		//If no path, or already is an alias, no lookup needed
		if (!$path || isset($this->aliases[$lower_path]) || $has_scheme) {
			if ($query_str) {
				$query_str = (strpos($path, '?') === false ? '?' : '&') . $query_str;
			}

			return ($has_scheme ? '' : $base_url) . $path . $query_str;
		}

		$url_alias = $this->path2Alias($path, $query);

		//Get Original Query without URL Alias query
		if ($url_alias) {
			$path  = $url_alias['alias'];
			if ($url_alias['query']) {
				$query = array_diff_assoc($query, $url_alias['query']);
			}
		}

		//Build the New URL
		return $base_url . $path . ($query ? ((strpos($path, '?') === false) ? '?' : '&') . http_build_query($query) : '');
	}

	public function alias2Path($alias)
	{
		$alias = path_format($alias);

		if (isset($this->aliases[$alias])) {
			return $this->aliases[$alias];
		}

		return false;

	}

	public function path2Alias($path, $query = '')
	{
		if ($query && is_string($query)) {
			parse_str($query, $query);
		}

		foreach ($this->aliases as $alias) {
			if (preg_match("|^" . $alias['path'] . "$|i", $path)) {
				if ($alias['query']) {
					foreach ($alias['query'] as $key => $value) {
						if (!isset($query[$key]) || $query[$key] !== $value) {
							continue 2;
						}
					}
				}

				return $alias;
			}
		}

		return false;
	}

	private function loadAliases()
	{
		//TODO: Need a better way to handle large sets of Aliases... consider switch to disable caching and query only aliases needed
		$this->aliases = cache('url_alias.all');

		if ($this->aliases === null) {
			$aliases = $this->Model_UrlAlias->getRecords(null, array('status' => 1));

			$this->aliases = array();

			foreach ($aliases as $alias) {
				if ($alias['query']) {
					parse_str($alias['query'], $alias['query']);
				}

				$this->aliases[path_format($alias['alias'])] = $alias;
			}

			cache('url_alias.all', $this->aliases);
		}
	}

	public function getAlias($path, $query = '')
	{
		return $this->queryVar("SELECT alias FROM {$this->t['url_alias']} WHERE `path` = '" . $this->escape($path) . "' AND `query` = '" . $this->escape($query) . "'");
	}

	public function setAlias($alias, $path, $query = '')
	{
		$this->removeAlias($path, $query);

		if ($alias) {
			$url_alias = array(
				'alias'  => $alias,
				'path'   => $path,
				'query'  => $query,
				'status' => 1,
			);

			return $this->Model_UrlAlias->save(null, $url_alias);
		}

		return true;
	}

	public function removeAlias($path, $query = '', $alias = '')
	{
		$sql_query =
			"SELECT url_alias_id FROM {$this->t['url_alias']} WHERE `path` = '" . $this->escape($path) . "'" .
			" AND `query` = '" . $this->escape($query) . "'";

		if ($alias) {
			$sql_query .= " AND alias = '" . $this->escape($alias) . "'";
		}

		$url_alias_ids = $this->queryColumn($sql_query);

		foreach ($url_alias_ids as $url_alias_id) {
			$this->Model_UrlAlias->remove($url_alias_id);
		}

		return $this->Model_UrlAlias->hasError();
	}
}
