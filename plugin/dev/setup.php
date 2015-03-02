<?php

/**
 * The Development Plugin
 *
 * Version: 0.8
 * Name: dev
 * Title: Amplo MVC Development
 * Description: Makes Development for Amplo MVC a thousands times easier! A Necessity for any and all Amlpo MVC Devs.
 * Author: Daniel Newman
 * Date: 3/15/2013
 * Link: http://www.amplomvc.com/plugins/dev
 *
 */
class Plugin_Dev_Setup extends Plugin_Setup
{
	public function install()
	{
		$link = array(
			'group'        => 'admin',
			'display_name' => "Development",
			'name'         => 'development',
			'path'         => 'admin/dev',
			'parent'       => 'system',
			'sort_order'   => 15,
		);

		$this->Model_Navigation->save(null, $link);
	}

	public function uninstall($keep_data = false)
	{
		$this->Model_Navigation->removeGroupLink('admin', 'development');
	}
}
