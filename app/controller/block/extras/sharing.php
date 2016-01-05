<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC
 * @link http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt files in the root directory.
 */

/**
 * Class App_Controller_Block_Extras_Sharing
 * Name: Social Sharing for Amplo MVC
 */
class App_Controller_Block_Extras_Sharing extends App_Controller_Block_Block
{
	public function build($settings)
	{
		$this->render('block/extras/sharing', $settings);
	}
}
