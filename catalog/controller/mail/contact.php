<?php
class Catalog_Controller_Mail_Contact extends Controller
{
	public function index($contact_info)
	{
		$this->language->loadTemporary('mail/contact');

		if (!isset($contact_info['name']) || !isset($contact_info['email'])) {
			trigger_error(_l("Invalid Contact information given in mail/contact.") . get_caller(0, 2));

			return false;
		}

		$this->mail->init();

		$this->mail->setTo($this->config->get('config_email'));
		$this->mail->setFrom($contact_info['email']);
		$this->mail->setSender($contact_info['name']);
		$this->mail->setSubject(html_entity_decode(sprintf(_l("Enquiry From %s"), $contact_info['name']), ENT_QUOTES, 'UTF-8'));
		$this->mail->setText(strip_tags(html_entity_decode($contact_info['enquiry'], ENT_QUOTES, 'UTF-8')));

		$this->mail->send();

		$this->language->unloadTemporary();
	}
}