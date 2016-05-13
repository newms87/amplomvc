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

class App_Controller_Admin_Mail_Forgotten extends Controller
{
	public function index(array $data)
	{
		$data['store_name'] = option('site_name');

		send_mail(array(
			'to'      => $data['email'],
			'subject' => _l("Password Reset for %s", option('site_name')),
			'html'    => $this->render('mail/forgotten', $data),
		));
	}
}
