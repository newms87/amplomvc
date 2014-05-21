<?php
class App_Controller_Mail_NewCustomer extends Controller
{
	public function index($customer)
	{
		if (!isset($customer['email'])) {
			$this->error_log->write(__METHOD__ . "(): Customer Email was not provided!");

			return false;
		}

		$insertables = array(
			'first_name' => $customer['firstname'],
			'last_name'  => $customer['lastname'],
			'store_name' => option('config_name'),
			'store_url'  => $this->url->site(),
		);

		//TODO: How can we better handle easy customizaable emails with integrated HTML template?
		$subject = $this->tool->insertables($insertables, option('mail_registration_subject'));
		$message = $this->tool->insertables($insertables, option('mail_registration_message'));

		$data['store'] = $this->config->getStore();

		$logo_width  = option('config_email_logo_width');
		$logo_height = option('config_email_logo_height');

		$data['logo'] = $this->image->resize(option('config_logo'), $logo_width, $logo_height);

		//Get resized image width x height. Note: $logo_width / $logo_height may be null or 0 meaning auto resize
		$data['logo_width']  = $this->image->info('width');
		$data['logo_height'] = $this->image->info('height');

		if (option('config_account_approval')) {
			$data['approval'] = _l('Your account must be approved before you can login. You will be notified once your account has been approved');
		}

		//If the customer did not generate their own password
		if (!empty($customer['no_password_set'])) {
			$data['reset_password'] = site_url('customer/forgotten');
		} else {
			$data['login'] = site_url('customer/login');
		}

		$this->mail->init();

		$this->mail->setTo($customer['email']);
		$this->mail->setFrom(option('config_email'));
		$this->mail->setSender(option('config_name'));
		$this->mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));

		$this->mail->setHtml($this->render('mail/new_customer', $data));

		$this->mail->send();

		// Send to main admin email if new account email is enabled
		if (option('config_account_mail')) {
			$this->mail->setTo(option('config_email'));
			$this->mail->setCc(option('config_alert_emails'));
			$this->mail->send();
		}
	}
}
