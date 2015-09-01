<?php

class Router
{
	protected
		$path,
		$segments,
		$args = array(),
		$site,
		$routing_hooks = array();

	public function __construct()
	{
		global $registry;
		$registry->set('route', $this);

		$uri = path_format(preg_replace("/\\?.*$/", '', $_SERVER['REQUEST_URI']));

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

	public function isPath($path)
	{
		return $this->path === str_replace('-', '_', $path);
	}

	public function setPath($path, $segments = null)
	{
		$this->path     = str_replace('-', '_', $path);

		if ($segments === null) {
			$this->segments = explode('/', $this->path);
		} else {
			$this->segments = (array)$segments;
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
			$params = array(
				$this
			);

			if (call_user_func_array($hook['callable'], $params) === false) {
				break;
			}
		}

		//Resolve Layout ID
		set_option('config_layout_id', $this->getLayoutForPath($this->path));

		//Dispatch Route
		$action = new Action($this->path, $this->args);

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
				if (AMPLO_USER_PAGE_LOG) {
					global $user_page_log_private;
					$post = $_POST;
					$private = array(
						'password',
					) + (array)$user_page_log_private;

					foreach ($private as $p) {
						if (isset($post[$p])) {
							$post[$p] = '...';
						}
					}

					write_log('user-page-log', IS_POST ? "POST: " . json_encode($_POST) : "GET");
				}

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
			if (strpos($this->path, 'api/') === 0) {
				header('HTTP/1.1 404 Not Found');

				$response = array(
					'status'  => 'error',
					'code'    => 404,
					'message' => _l("The API request for %s was not found.", $this->path),
				);

				output_json($response);
			} else {
				$action = new Action(ERROR_404_PATH);
				$action->execute();
			}
		}

		output_flush();
	}

	public function routeSite()
	{
		$options = array(
			'cache' => true,
			'index' => 'site_id'
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
}
