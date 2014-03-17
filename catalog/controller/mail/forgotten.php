<?php
class Catalog_Controller_Mail_Forgotten extends Controller
{
	public function index(array $data)
	{
		$this->mail->init();

		$this->mail->setTo($this->config->get('config_email'));
		$this->mail->setFrom($this->config->get('config_name'));
		$this->mail->setSender($data['email']);
		$this->mail->setSubject(_l("Password Reset Requested for your account with %s!", $this->config->get('config_name')));

		//Template Data
		$this->data += $data;
		$this->data['store_name'] = $this->config->get('config_name');

		//Render Mail Template
		$this->view->load('mail/forgotten_password');
		$this->mail->setHtml($this->render());

		$this->mail->send();
	}
}
