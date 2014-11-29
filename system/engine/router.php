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

	public function route()
	{
		if (IS_ADMIN) {
			$this->routeAdmin();
		} else {
			$this->routeFront();
		}
	}

	public function routeAdmin()
	{
		$this->config->set('store_id', -1);

		if (count($this->segments) === 1) {
			$this->setPath(defined("DEFAULT_ADMIN_PATH") ? DEFAULT_ADMIN_PATH : 'admin/index');
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
			if (IS_ADMIN) {
				if (isset($_GET['hide_maintenance_msg'])) {
					$_SESSION['hide_maintenance_msg'] = 1;
				} elseif (!isset($_SESSION['hide_maintenance_msg'])) {
					$hide = $this->url->here('hide_maintenance_msg=1');
					message('notify', _l("Site is in maintenance mode. You may still access the site when signed in as an administrator. <a href=\"$hide\">(hide message)</a> "));
				}
			} //Allow payment for payment callbacks (eg: IPN from PayPal, etc.)
			else if (strpos($this->path, 'payment') !== 0) {
				$this->path = 'common/maintenance';
			}
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
		$path = $this->path;

		//TODO: Move this to an extend / plugin feature! Possibly resort to using a hook here... PRODUCT should be a part of AmploCart Plugin!
		//Path Rerouting
		switch ($this->getSegment(0)) {
			case 'page':
				if ($this->getSegment(1) !== 'preview') {
					$path = 'page';
				}
				break;

			case 'product':
				if (!empty($_GET['product_id'])) {
					$product_class = $this->Model_Product->getProductClass($_GET['product_id']);
					$path = preg_replace("#^product/product#", 'product/' . $product_class, $this->path);
				}
				break;
		}

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
}
