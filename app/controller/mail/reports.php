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

class App_Controller_Mail_Reports extends Controller
{
	public function index($data)
	{
		$data += array(
			'title'   => '',
			'subject' => _l("Daily Reports"),
			'to'      => option('site_email'),
			'cc'      => '',
			'bcc'     => '',
			'from'    => option('site_email'),
			'sender'  => option('site_name'),
		);

		$data['header'] = array(
			'title' => $data['title'] ? $data['title'] : $data['subject'],
		);

		//If the customer did not generate their own password
		if (!empty($customer['no_password_set'])) {
			$data['reset_password'] = site_url('customer/forgotten');
		} else {
			$data['login'] = site_url('customer/login');
		}

		$data['html'] = $this->render('mail/reports', $data, 'admin');

		send_mail($data);
	}
}
