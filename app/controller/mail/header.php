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

class App_Controller_Mail_Header extends Controller
{
	public function index($data = array())
	{
		$data += array(
			'title' => option('site_name'),
			'logo'  => str_replace("./", '', option('site_logo')),
		);

		$this->render('mail/header', $data);
	}
}
