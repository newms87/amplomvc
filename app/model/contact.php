<?php

class App_Model_Contact extends Model
{
	public function sendMessage($data)
	{
		if (empty($data['name'])) {
			$this->error['name'] = _l("Please provide your name so we know what to call you!");
		}

		if (empty($data['email']) || !validate('email', $data['email'])) {
			$this->error['email'] = _l("Your email address appears to be invalid.");
		}

		if (empty($data['message'])) {
			$this->error['message'] = _l("Please enter text for your message.");
		}

		if (!empty($this->error)) {
			return false;
		}

		$to = option('site_support');

		if (!$to) {
			$to = option('site_email');
		}

		$result = send_mail(array(
			'to'      => $to,
			'from'    => _post('email'),
			'subject' => _l("A customer has contacted you on %s", option('site_name', _l("Your Site"))),
			'text'    => _post('message'),
		));

		if (!$result) {
			$this->error = $this->mail->getError();
			return false;
		}

		return true;
	}
}
