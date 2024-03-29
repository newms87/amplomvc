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

class System_Mod_Ganon extends System_Mod_Mod
{
	public function apply($source, $mod, $file_type, $meta)
	{
		ini_set('max_execution_time', 300);

		require_once DIR_RESOURCES . 'ganon.php';

		if (!($node = file_get_dom($source))) {
			message('warning', "There was an error while parsing the source file $source with Ganon!");

			return false;
		}

		require $mod;

		return $node->html();
	}
}
