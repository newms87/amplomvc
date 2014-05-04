<?php
class Url extends Library
{
	private $path;
	private $url = '';
	private $ssl = '';
	private $is_ssl;
	private $rewrite = array();
	private $seo_url;
	private $secure_pages = array();
	private $store_info = array();

	public function __construct()
	{
		parent::__construct();

		$this->url = option('config_url');

		if (option('config_use_ssl')) {
			$this->ssl = option('config_ssl');

			//TODO - finish secure pages
			$query              = $this->query("SELECT * FROM " . DB_PREFIX . "secure_page");
			$this->secure_pages = $query->rows;
		}

		$this->is_ssl = isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'));

		if (isset($_GET['_path_'])) {
			$this->path = trim($_GET['_path_'], '/ ');

			$this->path = preg_replace("/^admin\/?/", '', $this->path);
			$this->path = preg_replace("/^controller\/?/", '', $this->path);

			unset($_GET['_path_']);
		} else {
			$this->path = 'common/home';
		}

		if (option('config_seo_url')) {
			$this->loadSeoUrl();
		}
	}

	/**
	 * Checks if the current path starts with $path
	 *
	 * @param string $path - The path to check
	 * @return bool true if the current path starts with $path
	 */

	public function is($path)
	{
		return strpos($this->path, $path) === 0;
	}

	public function getPath()
	{
		return $this->path;
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
		return $this->link($this->path, $this->getQuery() . '&' . $append_query);
	}

	public function reload_page()
	{
		header("Location: " . $this->here());
		exit;
	}

	public function is_ssl()
	{
		return $this->is_ssl;
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

	public function admin($path = 'common/home', $query = '')
	{
		$link = $this->find_alias($path, $query, -1);

		return $link;
	}

	public function store($store_id, $path = 'common/home', $query = '')
	{
		if (!$store_id) {
			$store_id = option('config_default_store');
		}

		return $this->find_alias($path, $query, $store_id);
	}

	public function link($path, $query = '')
	{
		return $this->find_alias($path, $query);
	}

	public function store_base($store_id, $ssl = false)
	{
		if ((int)$store_id === 0 || $store_id == option('config_store_id')) {
			return $ssl ? option('config_ssl') : option('config_url');
		}

		$scheme = $ssl ? 'ssl' : 'url';

		//TODO: Need to Rebase stores so 0 is all stores (not an entry in the DB).
		//-1 is an entry in the DB but is for the admin and 1 will be the initial store (deleteable, if it is not set as the default)
		if ((int)$store_id === -1) {
			return URL_SITE . 'admin/';
		}

		$link = $this->queryVar("SELECT $scheme as link FROM " . DB_PREFIX . "store WHERE store_id = '" . (int)$store_id . "'");

		if (!is_string($link)) {
			trigger_error(__METHOD__ . _l("(): Store did not exist! Store ID: %s.", $store_id));
			return '';
		}

		return $link;
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

		header('Status: ' . $status);
		header('Location: ' . str_replace('&amp;', '&', $url));
		exit();
	}

	private function loadSeoUrl()
	{
		// Decode URL
		$path  = $this->escape($this->path);
		$query = $this->escape($this->getQuery());

		//Default Store
		$default = $this->config->isAdmin() ? -1 : 0;

		$sql =
			"SELECT * FROM " . DB_PREFIX . "url_alias" .
			" WHERE (alias = '$path' OR (path = '$path' AND (query = '*' OR '$query' like CONCAT('%', query, '%'))) )" .
			" AND status = '1' AND store_id IN ($default, " . (int)option('config_store_id') . ") LIMIT 1";

		$url_alias = $this->queryRow($sql);

		if ($url_alias) {
			//TODO: We need to reconsider how we handle all stores...
			if ($url_alias['store_id'] === 0) {
				if (!$this->config->isAdmin()) {
					$url_alias['store_id'] = (int)option('config_store_id');
				} else {
					$this->redirect($this->store(option('default_store_id'), $url_alias['path'], $url_alias['query']));
				}
			}

			$url_query     = $this->getQuery();
			$this->seo_url = $this->store_base($url_alias['store_id']) . $this->path . ($url_query ? '?' . $url_query : '');

			if ($url_alias['redirect']) {
				if (!parse_url($url_alias['redirect'], PHP_URL_SCHEME)) {
					$url_alias['redirect'] = $this->get_base($url_alias['store_id']) . 'index.php?' . $url_alias['redirect'];
				}

				$this->redirect($url_alias['redirect']);
			}


			if ((int)$url_alias['store_id'] !== (int)option('config_store_id') && (int)$url_alias['store_id'] !== 0) {
				if ((int)$url_alias['store_id'] === -1) {
					$this->redirect($this->admin($url_alias['path'], $url_alias['query']));
				} else {
					$this->redirect($this->store($url_alias['store_id'], $url_alias['path'], $url_alias['query']));
				}
			}

			$args = null;

			parse_str($url_alias['query'], $args);

			$_GET += $args;

			$this->path = $url_alias['path'];
		} else {
			$this->seo_url = $this->here();
		}
	}

	private function find_alias($path, $query = '', $store_id = false, $redirect = false)
	{
		if (!$path && $path !== '') {
			trigger_error(__METHOD__ . _l("(): Path was not specified!"));

			return false;
		}

		if (is_array($query)) {
			$query = http_build_query($query);
		} else {
			$query = urldecode($query);
		}

		if (strpos($path, 'http') === 0 || strpos($path, '//') === 0) {
			return $path . ($query ? '?' . $query : '');
		}

		if (!$store_id && $store_id !== 0) {
			$store_id = option('config_store_id');
		}

		if ($query) {
			$query_sql = "'" . $this->escape($query) . "' like CONCAT('%', query, '%')";
		} else {
			$query_sql = "query = ''";
		}

		$where = "WHERE $query_sql AND status='1'";
		$where .= " AND store_id IN (0, " . (int)$store_id . ")";
		$where .= " AND path = '" . $this->escape($path) . "'";

		//TODO: Validate that we need to ORDER BY query here... can be costly with a large number of aliases
		$sql = "SELECT * FROM " . DB_PREFIX . "url_alias $where ORDER BY query DESC LIMIT 1";

		$url_alias = $this->queryRow($sql);

		if ($url_alias) {
			if ($url_alias['redirect']) {
				$scheme = parse_url($url_alias['redirect'], PHP_URL_SCHEME);

				if (!$scheme) {
					if ($url_alias['store_id']) {
						$url_alias['redirect'] = $this->url->store($url_alias['store_id'], '', $url_alias['redirect']);
					} else {
						$url_alias['redirect'] = $this->url->admin('', $url_alias['redirect']);
					}
				}

				if ($redirect) {
					$this->redirect($url_alias['redirect']);
				} else {
					return $url_alias['redirect'];
				}
			}

			$alias = $url_alias['alias'];

			$alias_query = null;

			parse_str($url_alias['query'], $alias_query);
		}

		$url = $this->store_base($store_id);

		//rewrite query without path (or alias query if set)

		parse_str($query, $args);

		$disclude = array(
			'_path_',
		);

		if (!empty($alias_query)) {
			$disclude = array_merge($disclude, array_keys($alias_query));
		}

		foreach ($disclude as $key) {
			unset($args[$key]);
		}

		$query = !empty($args) ? http_build_query($args) : '';

		if (empty($alias)) {
			return $url . $path . ($query ? '?' . $query : '');
		} else {
			return $url . $alias . ($query ? '?' . $query : '');
		}
	}

	public function getAlias($path, $query = '', $store_id = 0)
	{
		$sql_query =
			"SELECT alias FROM " . DB_PREFIX . "url_alias" .
			" WHERE `path` = '" . $this->escape($path) . "'" .
			" AND `query` = '" . $this->escape($query) . "'" .
			" AND store_id IN (0, '" . (int)$store_id . "')";

		return $this->queryVar($sql_query);
	}

	public function setAlias($alias, $path, $query = '', $store_id = 0)
	{
		$this->removeAlias($path, $query, $store_id);

		if ($alias) {
			$url_alias = array(
				'alias'    => $alias,
				'path'     => $path,
				'query'    => $query,
				'store_id' => $store_id,
				'status'   => 1,
			);

			return $this->Model_Setting_UrlAlias->addUrlAlias($url_alias);
		}

		return true;
	}

	public function removeAlias($path, $query = '', $store_id = 0, $alias = '')
	{
		$sql_query =
			"SELECT url_alias_id FROM " . DB_PREFIX . "url_alias" .
			" WHERE `path` = '" . $this->escape($path) . "'" .
			" AND `query` = '" . $this->escape($query) . "'" .
			" AND store_id = '" . (int)$store_id . "'";

		if ($alias) {
			$sql_query .= " AND alias = '" . $this->escape($alias) . "'";
		}

		$url_alias_ids = $this->queryColumn($sql_query);

		foreach ($url_alias_ids as $url_alias_id) {
			$this->Model_Setting_UrlAlias->deleteUrlAlias($url_alias_id);
		}

		return $this->Model_Setting_UrlAlias->hasError();
	}
}
