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

class App_Model_ContactForm extends Model
{
	public function sendMessage($mail)
	{
		if (empty($mail['name'])) {
			$this->error['name'] = _l("Please provide your name so we know what to call you!");
		}

		if (empty($mail['email']) || !validate('email', $mail['email'])) {
			$this->error['email'] = _l("Your email address appears to be invalid.");
		}

		if (empty($mail['message'])) {
			$this->error['message'] = _l("Please enter text for your message.");
		}

		if (!empty($this->error)) {
			return false;
		}

		$to = option('site_support');

		if (!$to) {
			$to = option('site_email');
		}

		$mail += array(
				'to'      => $to,
				'subject' => _l("The Customer %s has contacted you on %s", $mail['email'], option('site_name', _l("Your Site"))),
				'text'    => $mail['message'],
			) + $mail;

		$mail['from'] = 'contact@' . DOMAIN;

		$result = send_mail($mail);

		if (!$result) {
			$this->error = $this->mail->fetchError();
			return false;
		}

		return true;
	}
}
