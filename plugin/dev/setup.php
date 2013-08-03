<?php
class Dev_Setup extends PluginSetup
{
	private $registry;
	
	function __construct($registry)
	{
		parent::__construct($registry);
		
		define("DEV_NAVIGATION_LINK_NAME", 'system_development');
	}
	
	public function install()
	{
		$link = array(
			'display_name' => "Development",
			'name' => DEV_NAVIGATION_LINK_NAME,
			'href' => 'dev/dev',
			'parent' => 'system',
			'sort_order' => 15,
		);
		
		$this->extend->addNavigationLink('admin', $link);
	}
	
	public function uninstall($keep_data = false)
	{
		$this->extend->removeNavigationLink(DEV_NAVIGATION_LINK_NAME);
	}
}