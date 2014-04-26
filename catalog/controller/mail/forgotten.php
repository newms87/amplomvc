<?php
class Catalog_Controller_Mail_Forgotten extends Controller
{
	public function index($data)
	{
		$this->mail->init();

		$this->mail->setTo($this->config->get('config_email'));
		$this->mail->setFrom($this->config->get('config_name'));
		$this->mail->setSender($data['email']);
		$this->mail->setSubject(_l("Password Reset Requested for your account with %s!", $this->config->get('config_name')));

		//Template Data
		$data['store_name'] = $this->config->get('config_name');

		$this->mail->setHtml($this->render('mail/forgotten_password', $data));

		$this->mail->send();
	}
}
