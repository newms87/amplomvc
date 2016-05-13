<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC
 * @link http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYING.txt and LICENSE.txt files in the root directory.
 */

class Condition extends Library
{
	static $conditions = array(
		''                => "Always",
		'user_logged'     => "The User is Logged In",
		'user_logged_out' => "The user is not Logged In",
	);

	public function is($condition)
	{
		switch ($condition) {
			case 'user_logged':
				return is_logged();

			case 'user_logged_out':
				return !is_logged();
		}

		return true;
	}
}
