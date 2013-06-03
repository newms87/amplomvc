<?php
class Admin_Mail extends Controller 
{
	
	public function mail_settings()
	{
		
		$this->language->plugin('mail', 'admin/mail');

		$configs = array(
			'mail_are_you_a_designer_emails','mail_are_you_a_designer_subject','mail_are_you_a_designer_message',
			'mail_designer_invoice_emails','mail_designer_invoice_subject','mail_designer_invoice_message',
			'mail_designer_active_emails','mail_designer_active_subject','mail_designer_active_message',
			'mail_designer_expire_emails','mail_designer_expire_subject','mail_designer_expire_message',
			'mail_designer_expiring_emails','mail_designer_expiring_subject','mail_designer_expiring_message',
		);
		
		foreach ($configs as $c) {
			$this->data[$c] = isset($_POST[$c])?$_POST[$c]:$this->config->get($c);
		}
	}
	
	public function settings_validate($return)
	{
		return $this->error?false:$return;
	}
}
