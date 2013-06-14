<?php
final class Front 
{
	protected $registry;
	private $error_route = 'error/not_found';
	private $route;
	
	public function __construct($registry)
	{
		$this->registry = $registry;
	}
	
	public function __get($key)
	{
		return $this->registry->get($key);
	}
	
	public function setErrorRoute($route)
	{
		$this->error_route = $route;
	}
	
	public function setRoute($route)
	{
		$this->route = $route;
	}
	
	public function getRoute()
	{
		return $this->route;
	}
	
	public function routeAdmin()
	{
		$this->route = !empty($_GET['route']) ? $_GET['route'] : 'common/home';
		
		if (!$this->user->isLogged()) {
			$allowed = array(
				'common/forgotten',
				'common/reset',
				'common/login',
			);
			
			if (!$this->isInRoutes($allowed)) {
				$this->route = 'common/login';
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
			
			if (!$this->isInRoutes($ignore)) {
				$parts = explode('/', $this->route);
			
				if (!isset($parts[0]) || !isset($parts[1])) {
					$this->route = 'common/home';
				}
				elseif (!$this->user->hasPermission('access', $parts[0] . '/' . $parts[1])) {
					$this->route = 'error/permission';
				}
			}
		}
	}
	
	public function routeFront()
	{
		$this->route = !empty($_GET['route']) ? $_GET['route'] : 'common/home';
		
		//Do not show maintenance page if user is an admin
		// or if the route is a a request by a payment provider (IPN from Paypal, etc.)
		if ($this->config->get('config_maintenance') && !$this->user->isAdmin() && strpos($this->route, 'payment') !== 0 ) {
			$this->route = 'common/maintenance';
		}
	}
	
	public function isInRoutes($routes)
	{
		foreach($routes as $route) {
			if(strpos($this->route, $route) === 0){
				return true;
			}
		}
		
		return false;
	}
	
  	public function dispatch()
  	{
  		//Page Views tracking
  		$route = $this->db->escape($this->route);
		$query = $this->url->get_query_exclude('route', '_route_');
		$store_id = (int)$this->config->get('config_store_id');
		
  		$this->db->query("INSERT INTO " . DB_PREFIX . "view_count SET route = '$route', query = '$query', store_id = '$store_id', count = 1 ON DUPLICATE KEY UPDATE count = count + 1");
		
  		$action = new Action($this->registry, $this->route);
		
		if (!$action->execute()) {
			$action = new Action($this->registry, $this->error_route);
			
			if (!$action->execute()) {
				trigger_error("Front::dispatch(): There is a problem with the system. Unable to execute any actions!");
			}
		}
  	}
}
