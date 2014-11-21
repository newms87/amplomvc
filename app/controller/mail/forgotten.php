<?php

class App_Controller_Mail_Forgotten extends Controller
{
	public function index($data)
	{
		//Template Data
		$data['store_name'] = option('config_name');

		send_mail(array(
			'to'      => option('config_email'),
			'sender'  => $data['email'],
			'subject' => _l("Password Reset Requested for your account with %s!", option('config_name')),
			'html'    => $this->render('mail/forgotten_password', $data),
		));
	}
}
