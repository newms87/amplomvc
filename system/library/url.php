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

		if (option('config_use_ssl')) {
			//TODO - finish secure pages
			$this->secure_pages = $this->queryRows("SELECT * FROM {$this->t['secure_page']}");
		}

		$this->loadAliases();
	}

	public function setUrl($url)
	{
		$this->url = $url;
	}

	public function setSsl($ssl)
	{
		$this->ssl = $ssl;
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

		return $this->link($this->router->getPath(), $query);
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
			$this->user->saveTokenSession();
		}

		session_write_close();

		$ch      = curl_init();
		$timeout = 5;

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

		if ($admin) {
			curl_setopt($ch, CURLOPT_COOKIE, 'token=' . _session('token'));
		}

		$data = curl_exec($ch);

		curl_close($ch);

		return $data;
	}

	public function link($path, $query = '', $ssl = null, $site_id = null)
	{
		static $sites;

		//Calculate Query & Fragment
		if ($query && !is_array($query)) {
			if (strpos($query, '#') !== false) {
				list($query, $fragment) = explode('#', $query, 2);
			}

			parse_str($query, $query);
		}

		$fragment = !empty($fragment) ? '#' . $fragment : '';

		//Resolve URL and Alias if no scheme set
		if (has_scheme($path)) {
			$url = '';

			if ($ssl !== null) {
				$path = cast_protocol($path, $ssl ? 'https' : 'http');
			}
		} else {
			if ($ssl === null) {
				$ssl = IS_SSL;
			}

			//Resolve Site URL
			if ($site_id) {
				if (!$sites) {
					$options = array(
						'cache' => true,
						'index' => 'site_id'
					);

					$sites = $this->Model_Site->getRecords(null, null, $options);
				}

				if (!empty($sites[$site_id])) {
					$url = $ssl ? $sites[$site_id]['ssl'] : $sites[$site_id]['url'];
				} else {
					$url = URL_SITE;
				}
			} else {
				$url = $ssl ? $this->ssl : $this->url;
			}

			$url_alias = $this->path2Alias($path, $query);

			//Get Original Query without URL Alias query
			if ($url_alias) {
				$path = $url_alias['alias'];
				if ($url_alias['query']) {
					$query = array_diff_assoc($query, $url_alias['query']);
				}
			}
		}

		//Build the New URL
		return $url . $path . ($query ? ((strpos($path, '?') === false) ? '?' : '&') . http_build_query($query) : '') . $fragment;
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
	 * @param       $url    - The full url or the controller path. If the full URL (eg: starting with http(s):// ) is
	 *                      given, Url::redirect() will ignore $query.
	 * @param mixed $query  - a string URI or associative array to be converted into a string URI
	 * @param int   $status - The header redirect status to send back to the requesting client.
	 */

	public function redirect($url, $query = '', $ssl = null, $status = 302)
	{
		header('Location: ' . str_replace('&amp;', '&', $this->link($url, $query, $ssl)), true, $status);
		exit();
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
		$path = path_format($path);

		//If already is an alias (or no path) then
		if ($path && !isset($this->aliases[$path])) {
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
