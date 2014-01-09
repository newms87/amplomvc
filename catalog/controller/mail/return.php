<?php
class Catalog_Controller_Mail_Order extends Controller
{
	public function index($return_data)
	{
		//Load Language Temporarily
		$this->language->loadTemporary('mail/return');

		//Send Customer Confirmation Email
		$this->mail->init();

		$this->mail->setTo($return_data['email']);
		$this->mail->setCc($this->config->get('config_email'));
		$this->mail->setFrom($this->config->get('config_email'));
		$this->mail->setSender($this->config->get('config_name'));
		$this->mail->setSubject(_l("Your return request has been submitted!"));
		$this->mail->setHtml(_l("We have received your return request. Please do not ship your product(s) back to us until we confirm your request. We will notify you when your product is eligible for return.", $return_data['rma']));
		$this->mail->send();

		//Send Admin Notification Email
		$this->mail->init();

		$this->mail->setTo($this->config->get('config_email'));
		$this->mail->setFrom($this->config->get('config_email'));
		$this->mail->setSender($this->config->get('config_name'));
		$this->mail->setSubject(_l("A product return request has been received!"));
		$this->mail->setHtml(_l("Please review the return request and notify the customer if there product is eligible for a return.", $return_data['rma']));
		$this->mail->send();

		//Unload Temporary Language Data
		$this->language->unloadTemporary();
	}
}