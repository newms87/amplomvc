<?php

class App_Controller_Mail_Reports extends Controller
{
	public function index($data)
	{
		$data += array(
			'title'   => '',
			'subject' => _l("Daily Reports"),
			'to'      => option('config_email'),
			'cc'      => '',
			'bcc'     => '',
			'from'    => option('config_email'),
			'sender'  => option('config_name'),
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
