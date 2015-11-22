<?php

class Router
{
	protected
		$action,
		$path,
		$segments,
		$nodes,
		$args = array(),
		$site,
		$routing_hooks = array();

	public function __construct()
	{
		global $registry;
		$registry->set('route', $this);
		$registry->set('router', $this);

		$path = preg_replace("/\\?.*$/", '', $_SERVER['REQUEST_URI']);

		$this->setPath($path);
	}

	public function __get($key)
	{
		global $registry;

		return $registry->get($key);
	}

	public function isPath($path)
	{
		return preg_match("#^" . str_replace('-', '_', $path) . "$#", $this->path);
	}

	public function setPath($path, $nodes = null, $segments = null)
	{
		$path = path_format($path, false);

		$base = trim(SITE_BASE, '/');

		if ($base && strpos($path, $base) === 0) {
			$path = trim(substr($path, strlen($base)), '/');
		}

		if ($path) {
			$url_alias = $this->url->alias2Path($path);

			if ($url_alias) {
				$path = $url_alias['path'];

				if ($url_alias['query']) {
					$_GET = $url_alias['query'] + $_GET;
				}
			}
		} else {
			$path = DEFAULT_PATH;
		}

		$this->path = str_replace('-', '_', $path);

		$this->segments = $segments === null ? explode('/', $path) : (array)$segments;
		$this->nodes    = $nodes === null ? explode('/', $this->path) : (array)$segments;

		foreach ($this->segments as &$seg) {
			$seg = str_replace('-', '_', $seg);
		}
	}

	public function getPath()
	{
		return $this->path;
	}

	public function getSegment($index = null)
	{
		if ($index === null) {
			return $this->segments;
		}

		return isset($this->segments[$index]) ? $this->segments[$index] : '';
	}

	public function getNode($index = null)
	{
		if ($index === null) {
			return $this->nodes;
		}

		return isset($this->nodes[$index]) ? $this->nodes[$index] : '';
	}

	public function getAction()
	{
		return $this->action;
	}

	public function setArgs($args)
	{
		$this->args = (array)$args;
	}

	public function getArgs()
	{
		return $this->args;
	}

	public function setSite($site)
	{
		$this->site = $site;
	}

	public function getSite()
	{
		return $this->site;
	}

	public function registerHook($name, $callable, $sort_order = 0)
	{
		if (is_callable($callable)) {
			$this->routing_hooks[$name] = array(
				'callable'   => $callable,
				'sort_order' => $sort_order,
			);

			return true;
		}

		return false;
	}

	public function unregisterHook($name)
	{
		unset($this->routing_hooks[$name]);
	}

	public function getLayoutForPath($path)
	{
		$layouts = cache('layout.routes');

		if ($layouts === null) {
			$layouts = $this->Model_Layout->getLayoutRoutes();

			cache('layout.routes', $layouts);
		}

		foreach ($layouts as $layout) {
			if (strpos($path, $layout['route']) === 0) {
				return $layout['layout_id'];
			}
		}

		return option('config_default_layout_id');
	}

	public function dispatch()
	{
		//Resolve routing hooks
		uasort($this->routing_hooks, function ($a, $b) {
			return $a['sort_order'] > $b['sort_order'];
		});

		foreach ($this->routing_hooks as $hook) {
			if ($hook['callable']($this) === false) {
				break;
			}
		}

		//Resolve Layout ID
		set_option('config_layout_id', $this->getLayoutForPath($this->path));

		if (AMPLO_ACCESS_LOG) {
			$this->logRequest();
		}

		//Dispatch Route
		$this->action = new Action($this->path, $this->args);

		$valid = $this->action->isValid();

		if ($valid) {
			if (IS_ADMIN) {
				if (!$this->user->canDoAction($this->action)) {
					if (!is_logged()) {
						$invalid_paths = array(
							'admin/user/login',
							'admin/user/logout',
						);

						if (in_array($this->path, $invalid_paths)) {
							$this->request->setRedirect('admin');
						} else {
							$this->request->setRedirect($this->url->here());
						}

						if (request_accepts('application/json')) {
							echo json_encode(array('error' => _l("You are not logged in. You are being redirected to the log in page.<script>window.location = '%s'</script>", site_url('admin/user/login'))));
							exit;
						}

						redirect('admin/user/login');
					}

					$this->action = new Action('admin/error/permission');
				}
			}
		}

		if (!$valid || !$this->action->execute()) {
			if (strpos($this->path, 'api/') === 0) {
				output_api('error', _l("The API resource %s was not found.", $this->path), null, 404);
			} else {
				$this->action = new Action(ERROR_404_PATH);
				$this->action->execute();
			}
		}

		output_flush();
	}

	public function routeSite()
	{
		$options = array(
			'cache' => true,
			'index' => 'site_id',
		);

		$sites = $this->Model_Site->getRecords(null, null, $options);

		$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
		$url    = $scheme . str_replace('www', '', $_SERVER['HTTP_HOST']) . '/' . trim($_SERVER['REQUEST_URI'], '/');

		$prefix = DB_PREFIX;

		foreach ($sites as $site) {
			if (strpos($url, trim($site['url'], '/ ')) === 0 || strpos($url, trim($site['ssl'], '/ ')) === 0) {
				$this->site = $site;
				break;
			}
		}

		if (!$this->site) {
			$this->site = array(
				'domain' => DOMAIN,
				'url'    => '//' . DOMAIN . '/',
				'ssl'    => 'https://' . DOMAIN . '/',
				'name'   => 'Amplo MVC',
				'prefix' => DB_PREFIX,
			);
		}

		if (!empty($this->site['prefix'])) {
			$prefix = $this->site['prefix'];
		}

		define('SITE_PREFIX', $prefix);
		_set_prefix($prefix);
	}

	protected function logRequest()
	{
		global $_access_log;

		if (!empty($_access_log['only'])) {
			$match = false;

			foreach ($_access_log['only'] as $only) {
				if (preg_match("#$only#", $this->path)) {
					$match = true;
					break;
				}
			}

			if (!$match) {
				return;
			}
		}

		if (!empty($_access_log['skip'])) {
			foreach ($_access_log['skip'] as $skip) {
				if (preg_match("#$skip#", $this->path)) {
					return;
				}
			}
		}

		if (IS_POST) {
			$post = $_POST;

			$private = array(
				'password',
			);

			if (!empty($_access_log['private'])) {
				$private = array_merge($private, $_access_log['private']);
			}

			foreach ($private as $p) {
				if (isset($post[$p])) {
					$post[$p] = '...';
				}
			}
		}

		write_log('access-log', (IS_ADMIN ? 'ADMIN ' : '') . (IS_POST ? "POST " : "GET ") . (IS_AJAX ? 'AJAX ' : '') . $this->path . (IS_POST ? "<BR><BR>" . json_encode($post) : ''));
	}
}
