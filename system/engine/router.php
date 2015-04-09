<?php

class Router
{
	protected
		$path,
		$segments,
		$site,
		$routing_hooks = array();

	public function __construct()
	{
		global $registry;
		$registry->set('route', $this);

		$uri   = path_format(preg_replace("/\\?.*$/", '', $_SERVER['REQUEST_URI']));

		$base = trim(SITE_BASE, '/');

		if ($base && strpos($uri, $base) === 0) {
			$uri = trim(substr($uri, strlen($base)), '/');
		}

		if ($uri) {
			$url_alias = $this->url->alias2Path($uri);

			if ($url_alias) {
				$uri = $url_alias['path'];

				if ($url_alias['query']) {
					$_GET = $url_alias['query'] + $_GET;
				}
			}
		}

		$this->path = $uri ? $uri : DEFAULT_PATH;

		$this->segments = explode('/', $this->path);
	}

	public function __get($key)
	{
		global $registry;

		return $registry->get($key);
	}

	public function getPath()
	{
		return $this->path;
	}

	public function setPath($path)
	{
		$this->path     = str_replace('-', '_', $path);
		$this->segments = explode('/', $this->path);
	}

	public function getSegment($index = null)
	{
		if ($index === null) {
			return $this->segments;
		}

		return isset($this->segments[$index]) ? $this->segments[$index] : '';
	}

	public function getSite()
	{
		return $this->site;
	}

	public function setSite($site)
	{
		$this->site = $site;
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
		$path = $this->path;

		//Resolve routing hooks
		$args = array();

		uasort($this->routing_hooks, function ($a, $b) {
			return $a['sort_order'] > $b['sort_order'];
		});

		foreach ($this->routing_hooks as $hook) {
			$params = array(
				&$path,
				$this->segments,
				$this->path,
				&$args,
			);

			if (call_user_func_array($hook['callable'], $params) === false) {
				break;
			}
		}

		//Resolve Layout ID
		set_option('config_layout_id', $this->getLayoutForPath($path));

		//Dispatch Route
		$action = new Action($path, $args);

		$valid = $action->isValid();

		if ($valid) {
			if (IS_ADMIN) {
				if (!$this->user->canDoAction($action)) {
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

					redirect('admin/error/permission');
				}
			} else {
				//Login Verification
				if (!$this->customer->canDoAction($action)) {
					$this->request->setRedirect($this->url->here());

					if (request_accepts('application/json')) {
						echo json_encode(array('error' => _l("Please log in to access this page. You are being redirected to the log in page.<script>window.location = '%s'</script>", site_url('customer/login'))));
						exit;
					}

					redirect('customer/login');
				}
			}
		}

		if (!$valid || !$action->execute()) {
			$action = new Action(ERROR_404_PATH);
			$action->execute();
		}
	}

	public function getSites()
	{
		return $this->Model_Site->getRecords(array('cache' => true), null, '*', false, 'store_id');
	}

	public function routeSite()
	{
		$sites = $this->getSites();

		$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
		$url    = $scheme . str_replace('www', '', $_SERVER['HTTP_HOST']) . '/' . trim($_SERVER['REQUEST_URI'], '/');

		$prefix = DB_PREFIX;

		foreach ($sites as $site) {
			if (strpos($url, trim($site['url'], '/ ')) === 0 || strpos($url, trim($site['ssl'], '/ ')) === 0) {
				if (!empty($site['prefix'])) {
					$prefix = $site['prefix'];
				}

				$this->site = $site;
				break;
			}
		}

		define('SITE_PREFIX', $prefix);
		_set_prefix($prefix);
	}
}
