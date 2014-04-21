<?php
class Catalog_Controller_Mail_NewCustomer extends Controller
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
			'store_name' => $this->config->get('config_name'),
			'store_url'  => $this->url->site(),
		);

		//TODO: How can we better handle easy customizaable emails with integrated HTML template?
		$subject = $this->tool->insertables($insertables, $this->config->get('mail_registration_subject'));
		$message = $this->tool->insertables($insertables, $this->config->get('mail_registration_message'));

		$data['store'] = $this->config->getStore();

		$logo_width  = $this->config->get('config_email_logo_width');
		$logo_height = $this->config->get('config_email_logo_height');

		$data['logo'] = $this->image->resize($this->config->get('config_logo'), $logo_width, $logo_height);

		//Get resized image width x height. Note: $logo_width / $logo_height may be null or 0 meaning auto resize
		$data['logo_width']  = $this->image->info('width');
		$data['logo_height'] = $this->image->info('height');

		if ($this->config->get('config_account_approval')) {
			$data['approval'] = _l('Your account must be approved before you can login. You will be notified once your account has been approved');
		}

		//If the customer did not generate their own password
		if (!empty($customer['no_password_set'])) {
			$data['reset_password'] = $this->url->link('account/forgotten');
		} else {
			$data['login'] = $this->url->link('account/login');
		}

		$this->mail->init();

		$this->mail->setTo($customer['email']);
		$this->mail->setFrom($this->config->get('config_email'));
		$this->mail->setSender($this->config->get('config_name'));
		$this->mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));

		$this->mail->setHtml($this->render('mail/new_customer', $data));

		$this->mail->send();

		// Send to main admin email if new account email is enabled
		if ($this->config->get('config_account_mail')) {
			$this->mail->setTo($this->config->get('config_email'));
			$this->mail->setCc($this->config->get('config_alert_emails'));
			$this->mail->send();
		}
	}
}
