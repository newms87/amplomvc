<?php 
class SetupDev extends SetupPlugin {
	private $registry;
	
	function __construct($registry){
		parent::__construct($registry);
		
		define("DEV_NAVIGATION_LINK_NAME", 'system_development');
	}
	
	public function install(&$controller_adapters, &$db_requests){
		$link = array(
			'display_name' => "Development",
			'name' => DEV_NAVIGATION_LINK_NAME,
			'href' => 'dev/dev',
			'is_route' => 1,
			'sort_order' => 15,
		);
		
		$this->extend->add_navigation_link($link, 'system', 'admin');
	}
	
	public function uninstall($keep_data = false){
		$this->extend->remove_navigation_link(DEV_NAVIGATION_LINK_NAME);
	}
}