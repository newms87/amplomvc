<?php
class Catalog_Controller_Mail_ForgottenAdmin extends Controller
{
	public function index(array $data)
	{
		$this->mail->init();

		$this->mail->setTo($data['email']);
		$this->mail->setFrom($this->config->get('config_email'));
		$this->mail->setSender($this->config->get('config_name'));
		$this->mail->setSubject(_l("Password Reset for %s", $this->config->get('config_name')));

		//Template Data
		$this->data += $data;

		$this->data['store_name'] = $this->config->get('config_name');

		//Render Mail Template
		$this->view->load('mail/forgotten_admin');
		$this->mail->setHtml($this->render());

		$this->mail->send();
	}
}
