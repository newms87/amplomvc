<?php
class Url extends Library
{
	private $path;
	private $url = '';
	private $ssl = '';
	private $is_ssl;
	private $rewrite = array();
	private $seo_url;
	private $ie_version = null;
	private $secure_pages = array();
	private $store_info = array();
	
	public function __construct($registry)
	{
		parent::__construct($registry);
		
		$this->url = $this->config->get('config_url');
		
		if ($this->config->get('config_use_ssl')) {
			$this->ssl = $this->config->get('config_ssl');

			//TODO - finish secure pages
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "secure_page");
			$this->secure_pages = $query->rows;
		}
		
		$this->is_ssl = isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'));
		
		if (isset($_GET['_path_'])) {
			$this->path = trim($_GET['_path_'], '/ ');
			
			if (preg_match("/^admin(\/|a^)/", $this->path)) {
				$this->path = preg_replace("/^admin\/?/", '', $this->path);
			}
			
			unset($_GET['_path_']);
		}
		else {
			$this->path = 'common/home';
		}
		
		$this->ie_version = $this->is_IE();
		
		if($this->config->get('config_seo_url')) {
			$this->loadSeoUrl();
		}
	}
	
	public function getPath()
	{
		return $this->path;
	}
	
	public function here()
	{
		return $this->link($this->path, $this->getQuery());
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
		
		$ch = curl_init();
		$timeout = 5;
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		
		if ($admin) {
			curl_setopt($ch, CURLOPT_COOKIE, 'token=' . $this->session->data['token']);
		}
		
		$data = curl_exec($ch);
		
		curl_close($ch);
		
		return $data;
	}
	
	public function getQuery()
	{
		$args = func_get_args();
		
		if (empty($args)) {
			return $_SERVER['QUERY_STRING'];
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
			return $_SERVER['QUERY_STRING'];
		}
		
		$query = array();
		
		foreach ($_GET as $key => $value) {
			if (!in_array($key, $args)) {
				$query[$key] = $value;
			}
		}
		
		return http_build_query($query);
	}
	
	public function getSeoUrl()
	{
		return $this->seo_url;
	}
	
	public function admin($path, $query = '')
	{
		$link = $this->find_alias($path, $query, -1);
		
		return $link;
	}
	
	public function store($store_id = false, $path = 'common/home', $query = '')
	{
		if (!$store_id) {
			$store_id = $this->config->get('config_default_store');
		}
		
		return $this->find_alias($path, $query, $store_id);
	}
	
	public function link($path, $query = '')
	{
		return $this->find_alias($path, $query);
	}
	
	public function ajax($path, $query = '')
	{
		//TODO: for ajax, no alias needed. Just route to /ajax/route or /admin/ajax/route
		return $this->find_alias($path, $query);
	}
	
	public function store_base($store_id, $ssl = false)
	{
		if ((int)$store_id === 0 || $store_id == $this->config->get('config_store_id')) {
			return $ssl ? $this->config->get('config_ssl') : $this->config->get('config_url');
		}
		
		$scheme = $ssl ? 'ssl':'url';
		
		$link = $this->db->queryVar("SELECT $scheme as link FROM " . DB_PREFIX . "store WHERE store_id = '" . (int)$store_id . "'");
		
		if (!is_string($link)) {
			trigger_error("Error in Url Library: Store did not exist! store_id = " . $store_id . '.  ' . get_caller(0, 3));
			return '';
		}
		
		return $link;
	}
	
	/**
	* This determines if the browser being used is Internet Explorer and returns the version number
	*
	* @return int or bool - version # of IE, or false if it is not IE
	*/
	public function is_IE()
	{
		$match = null;
		
		$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER["HTTP_USER_AGENT"] : '';
		
		if(preg_match("/MSIE\s*\d{1,2}.\d{1,2}/i", $user_agent, $match) == 0){
			return false;
		}
		else {
			return (float)str_replace('msie ', '', strtolower($match[0]));
		}
		
	}
	
	public function site($uri='', $query='', $base_site = false)
	{
		return ($base_site ? SITE_URL : $this->url) . $uri . (!empty($query)?"?$query":'');
	}
	
	public function urlencode_link($uri='',$query='')
	{
		return preg_replace("/%26amp%3B/i","%26",urlencode($this->link($uri,$query)));
	}
	
	public function format($url)
	{
		$patterns = array(
			"/[^A-Za-z0-9\/\\\\]+/" => "-",
			"/(^-)|(-$)/" 				=> '',
			"/[\/\\\\]-/"				=> "/",
		);
		
		return preg_replace(array_keys($patterns), array_values($patterns), strtolower($url));
	}
		
	public function decodeURIcomponent($uri)
	{
		$patterns = array('/&gt;/','/&lt;/');
		$replacements = array('>','<');
		return preg_replace($patterns, $replacements, rawurldecode($uri));
	}
	
	public function addRewrite($rewrite)
	{
		$this->rewrite[] = $rewrite;
	}
	
	public function redirect($url, $status = 302)
	{
		header('Status: ' . $status);
		header('Location: ' . str_replace('&amp;', '&', $url));
		exit();
	}
	
	private function loadSeoUrl()
	{
		// Decode URL
		$path = $this->db->escape($this->path);
		$query = $this->db->escape($this->getQuery());
		
		$sql =
			"SELECT * FROM " . DB_PREFIX . "url_alias" .
			" WHERE (alias = '$path' OR (path = '$path' AND (query = '*' OR '$query' like CONCAT('%', query, '%'))) )" .
			" AND status = '1' AND store_id IN (0, " . (int)$this->config->get('config_store_id') . ") LIMIT 1";
		
		$url_alias = $this->db->queryRow($sql);
		
		if ($url_alias) {
			//TODO: We need to reconsider how we handle all stores...
			if ($url_alias['store_id'] === 0) {
				if(!$this->config->isAdmin()) {
					$url_alias['store_id'] = (int)$this->config->get('config_store_id');
				} else {
					$this->redirect($this->store($this->config->get('default_store_id'), $url_alias['path'], $url_alias['query']));
				}
			}
		
			$url_query = $this->getQuery();
			$this->seo_url = $this->store_base($url_alias['store_id']) . $this->path . ($url_query ? '?' . $url_query : '');
			
			if ($url_alias['redirect']) {
				if (!parse_url($url_alias['redirect'], PHP_URL_SCHEME)) {
					$url_alias['redirect'] = $this->get_base($url_alias['store_id']) . 'index.php?' . $url_alias['redirect'];
				}
				
				$this->redirect($url_alias['redirect']);
			}
			
			
			if ((int)$url_alias['store_id'] !== (int)$this->config->get('config_store_id') && (int)$url_alias['store_id'] !== 0) {
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
		}
		else {
			$this->seo_url = $this->here();
		}
	}

	private function find_alias($path, $query = '', $store_id = false, $redirect = false)
	{
		if (!$path) {
			trigger_error("Url::find_alias(): Path was not specified! " . get_caller(0, 2));
			
			return false;
		}
		
		if (is_array($query)) {
			$query = http_build_query($query);
		} else {
			$query = urldecode($query);
		}
		
		if (!$store_id && $store_id !== 0) {
			$store_id = $this->config->get('config_store_id');
		}
		
		if ($query) {
			$query_sql = "'" . $this->db->escape($query) . "' like CONCAT('%', query, '%')";
		} else {
			$query_sql = "query = ''";
		}
		
		$where = "WHERE $query_sql AND status='1'";
		$where .= " AND store_id IN (0, " . (int)$store_id . ")";
		$where .= " AND path = '" . $this->db->escape($path) . "'";
		
		//TODO: Validate that we need to ORDER BY query here... can be costly with a large number of aliases
		$sql = "SELECT * FROM " . DB_PREFIX . "url_alias $where ORDER BY query DESC LIMIT 1";
		
		$url_alias = $this->db->queryRow($sql);
		
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
			$disclude = array_merge($disclude,array_keys($alias_query));
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
			" WHERE `path` = '" . $this->db->escape($path) . "'" .
			" AND `query` = '" . $this->db->escape($query) . "'" .
			" AND store_id IN (0, '" . (int)$store_id . "')";
						
		return $this->db->queryVar($sql_query);
	}
	
	public function setAlias($alias, $path, $query = '', $store_id = 0)
	{
		$url_alias = array(
			'alias' => $alias,
			'path'	=> $path,
			'query'	=> $query,
			'store_id' => $store_id,
			'status'  => 1,
		);
		
		$this->removeAlias($path, $query, $store_id);
		
		$this->Model_Setting_UrlAlias->addUrlAlias($url_alias);
	}
	
	public function removeAlias($path, $query = '', $store_id = 0, $alias = '')
	{
		$sql_query =
			"SELECT url_alias_id FROM " . DB_PREFIX . "url_alias" .
			" WHERE `path` = '" . $this->db->escape($path) . "'" .
			" AND `query` = '" . $this->db->escape($query) . "'" .
			" AND store_id = '" . (int)$store_id . "'";
		
		if ($alias) {
			$sql_query .= " AND alias = '" . $this->db->escape($alias) . "'";
		}
		
		$url_alias_ids = $this->db->queryColumn($sql_query);
		
		foreach ($url_alias_ids as $url_alias_id) {
			$this->Model_Setting_UrlAlias->deleteUrlAlias($url_alias_id);
		}
	}
}
