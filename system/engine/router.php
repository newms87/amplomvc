<?php
final class Router
{
	private $path;
	private $segments;

	public function __construct()
	{
		$uri = trim(preg_replace("/\\?.*$/", '', $_SERVER['REQUEST_URI']), '/ ');

		$base = trim(SITE_BASE, '/');
		if ($base && strpos($uri, $base) === 0) {
			$uri = trim(substr($uri, strlen($base)), '/');
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
		$this->path = $path;
		$this->segments = explode('/', $path);
	}

	public function getSegment($index = null)
	{
		if (is_null($index)) {
			return $this->segments;
		}

		return isset($this->segments[$index]) ? $this->segments[$index] : '';
	}

	public function isAdmin()
	{
		return $this->getSegment(0) === 'admin';
	}

	public function route()
	{
		if ($this->route->isAdmin()) {
			$this->routeAdmin();
		} else {
			$this->routeFront();
		}
	}

	public function routeAdmin()
	{
		$this->config->set('store_id', -1);

		if (count($this->segments) === 1) {
			$this->setPath(defined("DEFAULT_ADMIN_PATH") ? DEFAULT_ADMIN_PATH : 'admin/common/home');
		}

		//Initialize site configurations
		$this->config->run_site_config();

		//Controller Overrides
		$controller_overrides = $this->config->load('controller_override', 'controller_override');

		if ($controller_overrides) {
			foreach ($controller_overrides as $override) {
				if (('app/controller/admin/' . $this->path) === $override['original']) {
					if (empty($override['condition']) || preg_match("/.*" . $override['condition'] . ".*/", $this->url->getQuery())) {
						$this->path = str_replace('app/controller/admin/', '', $override['alternate']);
					}
				}
			}
		}
	}

	public function routeFront()
	{
		if (option('config_maintenance')) {
			//Do not show maintenance page if user is an admin
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
				if (('app/controller/' . $this->path) === $override['original']) {
					if (empty($override['condition']) || preg_match("/" . $override['condition'] . "/", urldecode($this->url->getQuery()))) {
						$this->path = str_replace('app/controller/', '', $override['alternate']);
					}
				}
			}
		}

		//Tracking
		if (isset($_GET['tracking']) && !isset($_COOKIE['tracking'])) {
			setcookie('tracking', $_GET['tracking'], _time() + 3600 * 24 * 1000, '/');
		}

		//Resolve Layout ID
		$layout    = $this->db->queryRow("SELECT layout_id FROM " . DB_PREFIX . "layout_route WHERE '" . $this->db->escape($this->path) . "' LIKE CONCAT(route, '%') AND store_id = '" . option('store_id') . "' ORDER BY route ASC LIMIT 1");
		$layout_id = $layout ? $layout['layout_id'] : option('config_default_layout_id');
		$this->config->set('config_layout_id', $layout_id);
	}

	public function dispatch()
	{
		//Page Views tracking
		$path     = $this->db->escape($this->path);
		$query    = $this->url->getQueryExclude('sort', 'order', 'limit', 'redirect', 'filter');
		$store_id = (int)option('store_id');

		$this->db->query("INSERT INTO " . DB_PREFIX . "view_count SET path = '$path', query = '$query', store_id = '$store_id', count = 1 ON DUPLICATE KEY UPDATE count = count + 1");

		if (strpos($this->path, 'page/') === 0) {
			$path = 'page';
		} else {
			$path = $this->path;
		}

		$action = new Action($path);

		$valid = $action->isValid();

		if ($valid) {
			if ($this->isAdmin()) {
				if (!$this->user->canDoAction($action)) {
					if (!$this->user->isLogged()) {
						$this->request->setRedirect($this->url->here());
						redirect('admin/common/login');
					}

					redirect('admin/error/permission');
				}
			} else {
				//Login Verification
				if (!$this->customer->canDoAction($action)) {
					$this->request->setRedirect($this->url->here());
					redirect('customer/login');
				}
			}
		}

		if (!$valid || !$action->execute()) {
			$action = new Action(ERROR_404_PATH);
			$action->execute();
		}
	}
}
