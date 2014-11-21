<?php

class App_Controller_Admin_Mail_Forgotten extends Controller
{
	public function index(array $data)
	{
		$data['store_name'] = option('config_name');

		send_mail(array(
			'to'      => $data['email'],
			'subject' => _l("Password Reset for %s", option('config_name')),
			'html'    => $this->render('mail/forgotten', $data),
		));
	}
}
