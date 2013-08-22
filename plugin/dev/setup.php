<?php
/**
 * The Development Plugin
 *
 * Version: 0.7
 * Title: AmploCart Development
 * Description: Makes Development for AmploCart a thousands times easier! A Necessity for any and all AmlpoCart Devs.
 * Author: Daniel Newman
 * Date: 3/15/2013
 * Link: http://www.amplocart.com/plugins/dev
 *
 */

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
			'name'         => DEV_NAVIGATION_LINK_NAME,
			'href'         => 'dev/dev',
			'parent'       => 'system',
			'sort_order'   => 15,
		);

		$this->extend->addNavigationLink('admin', $link);
	}

	public function uninstall($keep_data = false)
	{
		$this->extend->removeNavigationLink('admin', DEV_NAVIGATION_LINK_NAME);
	}
}