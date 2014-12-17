<?php

class App_Controller_Mail_Forgotten extends Controller
{
	public function index($data)
	{
		send_mail(array(
			'to'      => $data['email'],
			'subject' => _l("Password Reset Requested for your account with %s!", option('site_name')),
			'html'    => $this->render('mail/forgotten_password', $data),
		));
	}
}
