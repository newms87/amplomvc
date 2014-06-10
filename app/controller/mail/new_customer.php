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

		$store = $this->config->getStore();

		$logo = image($this->config->load('config', 'config_logo', $store['store_id']));

		if ($logo) {
			$data['logo'] = $logo;
			$logo_info = getimagesize($logo);
			$data['logo_width_height'] = $logo_info[3];
		} else {
			$data['logo'] = '';
			$data['logo_width_height'] = '';
		}

		//If the customer did not generate their own password
		if (!empty($customer['no_password_set'])) {
			$data['reset_password'] = site_url('customer/forgotten');
		} else {
			$data['login'] = site_url('customer/login');
		}

		$data['store'] = $store;

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
