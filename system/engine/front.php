<?php
final class Front
{
	protected $registry;
	private $error_path = 'error/not_found';
	private $path;

	public function __construct($registry)
	{
		$this->registry = $registry;
	}

	public function __get($key)
	{
		return $this->registry->get($key);
	}

	public function routeAdmin()
	{
		if (isset($_GET['run_cron'])) {
			$this->cron->run();
			exit;
		}

		$this->path = $this->url->getPath();

		if (!$this->user->isLogged()) {
			$allowed = array(
				'common/forgotten',
				'common/reset',
				'common/login',
			);

			if (!$this->pathIsIn($allowed)) {
				$this->path = 'common/login';
			}
		}
		else {
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
				}
				elseif (!$this->user->hasPermission('access', $parts[0] . '/' . $parts[1])) {
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
						$this->path = str_replace('admin/controller/','',$override['alternate']);
					}
				}
			}
		}
	}

	public function routeFront()
	{
		if (isset($_GET['run_cron'])) {
			$this->cron->run();
			exit;
		}

		$this->path = $this->url->getPath();

		//Do not show maintenance page if user is an admin
		// or if the path is a a request by a payment provider (IPN from Paypal, etc.)
		if ($this->config->get('config_maintenance') && !$this->user->isAdmin() && strpos($this->path, 'payment') !== 0 ) {
			$this->path = 'common/maintenance';
		}

		//Controller Overrides
		$controller_overrides = $this->config->load('controller_override', 'controller_override');

		if ($controller_overrides) {
			foreach ($controller_overrides as $override) {
				if (('catalog/controller/' . $this->path) === $override['original']) {
					if (empty($override['condition']) || preg_match("/.*" . $override['condition'] . ".*/", $this->url->getQuery())) {
						$this->path = str_replace('catalog/controller/','',$override['alternate']);
					}
				}
			}
		}
	}

	public function pathIsIn($paths)
	{
		foreach($paths as $path) {
			if(strpos($this->path, $path) === 0){
				return true;
			}
		}

		return false;
	}

  	public function dispatch()
  	{
  		//Page Views tracking
  		$path = $this->db->escape($this->path);
		$query = $this->url->getQueryExclude('_path_', 'sort', 'order', 'limit', 'redirect', 'filter');
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
