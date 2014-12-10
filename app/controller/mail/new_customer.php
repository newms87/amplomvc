<?php

class App_Controller_Mail_NewCustomer extends Controller
{
	public function index($customer)
	{
		if (!isset($customer['email'])) {
			write_log('error', __METHOD__ . "(): Customer Email was not provided!");

			return false;
		}

		$insertables = array(
			'first_name' => $customer['firstname'],
			'last_name'  => $customer['lastname'],
			'store_name' => option('config_name'),
			'store_url'  => $this->url->site(),
		);

		//TODO: How can we better handle easy customizaable emails with integrated HTML template?
		$subject = insertables($insertables, option('mail_registration_subject'));
		$message = insertables($insertables, option('mail_registration_message'));

		$store = $this->config->getStore();

		$data['header'] = array(
			'store' => $store,
			'title' => _l("Customer Registration"),
		);

		//If the customer did not generate their own password
		if (!empty($customer['no_password_set'])) {
			$data['reset_password'] = site_url('customer/forgotten');
		} else {
			$data['login'] = site_url('customer/login');
		}

		$data['store'] = $store;

		$mail = array(
			'to'      => $customer['email'],
			'subject' => html_entity_decode($subject, ENT_QUOTES, 'UTF-8'),
			'html'    => $this->render('mail/new_customer', $data),
		);

		send_mail($mail);

		// Send to main admin email if new account email is enabled
		if (option('config_account_mail')) {
			$mail['to'] = option('config_email');
			$mail['cc'] = option('config_alert_emails');

			send_mail($mail);
		}
	}
}
