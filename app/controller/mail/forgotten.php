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

class App_Controller_Mail_Forgotten extends Controller
{
	public function index($data)
	{
		send_mail(array(
			'to'      => $data['email'],
			'subject' => _l("Password Reset Requested for your account with %s!", option('site_name')),
			'html'    => $this->render('mail/forgotten_password', $data),
		));
	}
}
