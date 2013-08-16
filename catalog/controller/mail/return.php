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
		$this->mail->setSubject($this->_('text_mail_subject'));
		$this->mail->setHtml($this->_('text_mail_message', $return_data['rma']));
		$this->mail->send();

		//Send Admin Notification Email
		$this->mail->init();

		$this->mail->setTo($this->config->get('config_email'));
		$this->mail->setFrom($this->config->get('config_email'));
		$this->mail->setSender($this->config->get('config_name'));
		$this->mail->setSubject($this->_('text_mail_admin_subject'));
		$this->mail->setHtml($this->_('text_mail_admin_message', $return_data['rma']));
		$this->mail->send();

		//Unload Temporary Language Data
		$this->language->unloadTemporary();
	}
}