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

class App_Controller_Mail_NewCustomer extends Controller
{
	public function index($data)
	{
		if (!isset($data['email'])) {
			write_log('error', __METHOD__ . "(): Customer Email was not provided!");

			return false;
		}

		$data += array(
			'header'  => array(
				'title' => _l("Customer Registration"),
			),
			'subject' => option('mail_registration_subject', _l("Thank you for registering %first_name%")),
			'message' => option('mail_registration_message'),
		);

		$insertables = array(
			'first_name' => !empty($data['first_name']) ? $data['first_name'] : '',
			'last_name'  => !empty($data['last_name']) ? $data['last_name'] : '',
			'store_name' => option('site_name'),
			'store_url'  => site_url(),
		);

		//TODO: How can we better handle easy customizaable emails with integrated HTML template?
		$data['subject'] = insertables($insertables, $data['subject']);
		$data['message'] = insertables($insertables, $data['message']);

		//If the customer did not generate their own password
		if (!empty($data['no_password_set'])) {
			$data['reset_password'] = site_url('customer/forgotten');
		} else {
			$data['login'] = site_url('customer/login');
		}

		$mail = array(
			'to'      => $data['email'],
			'subject' => html_entity_decode($data['subject'], ENT_QUOTES, 'UTF-8'),
			'html'    => $this->render('mail/new_customer', $data),
		);

		send_mail($mail);

		// Send to main admin email if new account email is enabled
		if (option('config_account_mail')) {
			$mail['to'] = option('site_email');
			$mail['cc'] = option('config_alert_emails');

			send_mail($mail);
		}
	}
}
