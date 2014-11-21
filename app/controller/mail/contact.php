<?php

class App_Controller_Mail_Contact extends Controller
{
	public function index(array $contact_info)
	{
		if (!isset($contact_info['name']) || !isset($contact_info['email'])) {
			trigger_error(_l("Invalid Contact information given in mail/contact."));
			return false;
		}

		send_mail(array(
			'to'      => option('config_email'),
			'from'    => $contact_info['email'],
			'sender'  => $contact_info['name'],
			'subject' => html_entity_decode(sprintf(_l("Enquiry From %s"), $contact_info['name']), ENT_QUOTES, 'UTF-8'),
			'text'    => strip_tags(html_entity_decode($contact_info['enquiry'], ENT_QUOTES, 'UTF-8')),
		));
	}
}
