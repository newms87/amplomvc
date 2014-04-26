<?php
final class Router
{
	private $registry;
	private $error_path = 'error/not_found';
	private $path;
	private $segments;

	public function __construct($registry)
	{
		$this->registry = $registry;
	}

	public function __get($key)
	{
		return $this->registry->get($key);
	}

	public function getPath()
	{
		return $this->path;
	}

	public function getSegment($index = null)
	{
		if (is_null($index)) {
			return $this->segments;
		}

		return isset($this->segments[$index]) ? $this->segments[$index] : '';
	}

	public function route()
	{
		$this->path = $this->url->getPath();

		if ($this->config->isAdmin()) {
			$this->routeAdmin();
		} else {
			$this->routeFront();
		}

		$this->segments = explode('/', $this->path);
	}

	public function routeAdmin()
	{
		//Initialize site configurations
		$this->config->run_site_config();

		//TODO: We should not validate pages user can access in router.
		if (!$this->user->isLogged()) {
			$allowed = array(
				'common/forgotten',
				'common/reset',
				'common/login',
			);

			if (!$this->pathIsIn($allowed)) {
				$this->path = 'common/login';
			}
		} else {
			$ignore = array(
				'common/home',
				'common/login',
				'common/logout',
				'common/forgotten',
				'common/reset',
				'error/not_found',
				'error/permission'
			);

			if (!$this->pathIsIn($ignore)) {
				$parts = explode('/', $this->path);

				if (!isset($parts[0]) || !isset($parts[1])) {
					$this->path = 'common/home';
				} elseif (!$this->user->can('access', $parts[0] . '/' . $parts[1])) {
					$this->path = 'error/permission';
				}
			}
		}

		//Controller Overrides
		$controller_overrides = $this->config->load('controller_override', 'controller_override');

		if ($controller_overrides) {
			foreach ($controller_overrides as $override) {
				if (('admin/controller/' . $this->path) === $override['original']) {
					if (empty($override['condition']) || preg_match("/.*" . $override['condition'] . ".*/", $this->url->getQuery())) {
						$this->path = str_replace('admin/controller/', '', $override['alternate']);
					}
				}
			}
		}
	}

	public function routeFront()
	{
		//Do not show maintenance page if user is an admin
		// or if the path is a a request by a payment provider (IPN from Paypal, etc.)
		if ($this->config->get('config_maintenance')) {
			if ($this->user->isAdmin()) {
				if (isset($_GET['hide_maintenance_msg'])) {
					$_SESSION['hide_maintenance_msg'] = 1;
				} elseif (!isset($_SESSION['hide_maintenance_msg'])) {
					$hide = $this->url->here('hide_maintenance_msg=1');
					$this->message->add('notify', _l("Site is in maintenance mode. You may still access the site when signed in as an administrator. <a href=\"$hide\">(hide message)</a> "));
				}
			} //Allow payment for payment callbacks (eg: IPN from PayPal, etc.)
			else if (strpos($this->path, 'payment') !== 0) {
				$this->path = 'common/maintenance';
			}
		}

		//Product Class Routing
		if ($this->path === 'product/product' && !empty($_GET['product_id'])) {
			$this->path = $this->Model_Catalog_Product->getClassController($_GET['product_id']);
		}

		//Controller Overrides
		$controller_overrides = $this->config->load('controller_override', 'controller_override');

		if ($controller_overrides) {
			foreach ($controller_overrides as $override) {
				if (('catalog/controller/' . $this->path) === $override['original']) {
					if (empty($override['condition']) || preg_match("/" . $override['condition'] . "/", urldecode($this->url->getQuery()))) {
						$this->path = str_replace('catalog/controller/', '', $override['alternate']);
					}
				}
			}
		}

		//Tracking
		if (isset($_GET['tracking']) && !isset($_COOKIE['tracking'])) {
			setcookie('tracking', $_GET['tracking'], _time() + 3600 * 24 * 1000, '/');
		}

		//Resolve Layout ID
		$layout    = $this->db->queryRow("SELECT layout_id FROM " . DB_PREFIX . "layout_route WHERE '" . $this->db->escape($this->path) . "' LIKE CONCAT(route, '%') AND store_id = '" . $this->config->get('config_store_id') . "' ORDER BY route ASC LIMIT 1");
		$layout_id = $layout ? $layout['layout_id'] : $this->config->get('config_default_layout_id');
		$this->config->set('config_layout_id', $layout_id);
	}

	public function pathIsIn($paths)
	{
		foreach ($paths as $path) {
			if (strpos($this->path, $path) === 0) {
				return true;
			}
		}

		return false;
	}

	public function dispatch()
	{
		//Page Views tracking
		$path     = $this->db->escape($this->path);
		$query    = $this->url->getQueryExclude('_path_', 'sort', 'order', 'limit', 'redirect', 'filter');
		$store_id = (int)$this->config->get('config_store_id');

		$this->db->query("INSERT INTO " . DB_PREFIX . "view_count SET path = '$path', query = '$query', store_id = '$store_id', count = 1 ON DUPLICATE KEY UPDATE count = count + 1");

		$action = new Action($this->registry, $this->path);

		if (!$action->isValid() || !$action->execute()) {
			$action = new Action($this->registry, $this->error_path);

			if (!$action->execute()) {
				trigger_error("Front::dispatch(): There is a problem with the system. Unable to execute any actions!");
			}
		}
	}
}
