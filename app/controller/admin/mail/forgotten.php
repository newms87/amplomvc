<?php
class App_Controller_Admin_Mail_Forgotten extends Controller
{
	public function index(array $data)
	{
		$this->mail->init();

		$this->mail->setTo($data['email']);
		$this->mail->setFrom(option('config_email'));
		$this->mail->setSender(option('config_name'));
		$this->mail->setSubject(_l("Password Reset for %s", option('config_name')));

		//Template Data
		$data += $data;

		$data['store_name'] = option('config_name');

		$this->mail->setHtml($this->render('mail/forgotten', $data));

		$this->mail->send();
	}
}
