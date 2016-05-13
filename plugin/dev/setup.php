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
 * @package Amplo MVC Dev Plugin
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYING.txt and LICENSE.txt files in the root directory.
 */
class Plugin_Dev_Setup extends Plugin_Setup
{
	static $admin_nav = array(
		'development' => array(
			'display_name' => "Development",
			'path'         => 'admin/dev',
			'parent'       => 'system',
			'sort_order'   => 15,
		)
	);

	public function install()
	{
		$this->Model_Navigation->saveGroupLinks('admin', self::$admin_nav);
	}

	public function uninstall($keep_data = false)
	{
		$this->Model_Navigation->removeGroupLinks('admin', self::$admin_nav);
	}
}
