<?php
/**
 * The Development Plugin
 *
 * Version: 0.7
 * Title: Amplo MVC Development
 * Description: Makes Development for Amplo MVC a thousands times easier! A Necessity for any and all Amlpo MVC Devs.
 * Author: Daniel Newman
 * Date: 3/15/2013
 * Link: http://www.amplomvc.com/plugins/dev
 *
 */
class Dev_Setup extends Plugin_Setup
{
	public function install()
	{
		$link = array(
			'display_name' => "Development",
			'name'         => 'development',
			'href'         => 'dev/dev',
			'sort_order'   => 15,
		);

		$this->extend->addNavigationLink('admin', $link);
	}

	public function uninstall($keep_data = false)
	{
		$this->extend->removeNavigationLink('admin', 'development');
	}
}
