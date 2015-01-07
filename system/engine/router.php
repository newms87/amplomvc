<?php

final class Router
{
	private $path;
	private $segments;
	private $site;

	public function __construct()
	{
		$this->routeStore();

		$uri = trim(preg_replace("/\\?.*$/", '', $_SERVER['REQUEST_URI']), '/ ');

		$base = trim(SITE_BASE, '/');
		if ($base && strpos($uri, $base) === 0) {
			$uri = trim(substr($uri, strlen($base)), '/');
		}

		$this->path = $uri ? $uri : DEFAULT_PATH;

		$this->segments = explode('/', $this->path);
		html_dump($this->site, 'store');
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
		$this->path     = $path;
		$this->segments = explode('/', $path);
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

		_set_db_prefix(isset($site['prefix']) ? $site['prefix'] : DB_PREFIX);
	}

	public function registerHook($name, $callable, $sort_order = 0)
	{
		$routing_hooks = option('_routing_hooks_', array());

		$routing_hooks[$name] = array(
			'callable'   => $callable,
			'sort_order' => $sort_order,
		);

		uasort($routing_hooks, function ($a, $b) {
			return $a['sort_order'] > $b['sort_order'];
		});

		save_option('_routing_hooks_', $routing_hooks);

		return $routing_hooks;
	}

	public function unregisterHook($name)
	{
		$routing_hooks = option('_routing_hooks_', array());

		unset($routing_hooks[$name]);

		save_option('_routing_hooks_', $routing_hooks);

		return $routing_hooks;
	}

	public function dispatch()
	{
		$path = $this->path;

		//Resolve routing hooks
		$routing_hooks = option('_routing_hooks_');

		if (!$routing_hooks) {
			$routing_hooks = $this->registerHook('default', 'amplo_routing_hook');
		}

		foreach ($routing_hooks as $hook) {
			if (is_callable($hook['callable'])) {
				$params = array(
					&$path,
					$this->segments,
					$this->path
				);

				if (call_user_func_array($hook['callable'], $params) === false) {
					break;
				}
			}
		}

		//Resolve Layout ID
		$layout    = $this->db->queryRow("SELECT layout_id FROM " . DB_PREFIX . "layout_route WHERE '" . $this->db->escape($path) . "' LIKE CONCAT(route, '%') ORDER BY route ASC LIMIT 1");
		$layout_id = $layout ? $layout['layout_id'] : option('config_default_layout_id');
		$this->config->set('config_layout_id', $layout_id);

		//Dispatch Route
		$action = new Action($path);

		$valid = $action->isValid();

		if ($valid) {
			if (IS_ADMIN) {
				if (!$this->user->canDoAction($action)) {
					if (!is_logged()) {
						$this->request->setRedirect($this->url->here());

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

	public function routeStore()
	{
		global $registry;

		$stores = cache('store.all');

		if ($stores === null) {
			$stores = $registry->get('db')->queryRows("SELECT * FROM " . DB_PREFIX . 'store');
			cache('store.all', $stores);
		}

		$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
		$url    = $scheme . str_replace('www', '', $_SERVER['HTTP_HOST']) . '/' . trim($_SERVER['REQUEST_URI'], '/');

		$prefix = DB_PREFIX;

		foreach ($stores as $store) {
			if (strpos($url, trim($store['url'], '/ ')) === 0 || strpos($url, trim($store['ssl'], '/ ')) === 0) {
				if (!empty($store['prefix'])) {
					$prefix = $store['prefix'];
				}

				$this->site = $store;
				break;
			}
		}

		define('SITE_PREFIX', $prefix);
		_set_db_prefix($prefix);
	}
}
