<?php
class App_Controller_Mail_Error extends Controller
{
	public function index($error_msg, $data = array())
	{
		$defaults = array(
			'to'      => option('config_email_error'),
			'cc'      => '',
			'bcc'     => '',
			'from'    => option('config_email'),
			'sender'  => option('config_name'),
			'subject' => "There was a critical error encountered that requires immediate attention!",
			'text'    => html2text($error_msg),
		);

		$data += $defaults;

		$this->mail->init();

		$this->mail->setTo($data['to']);
		$this->mail->setCc($data['cc']);
		$this->mail->setBcc($data['bcc']);
		$this->mail->setFrom($data['from']);
		$this->mail->setSender($data['sender']);
		$this->mail->setSubject($data['subject']);
		$this->mail->setText($data['text']);

		$this->mail->send();

		$this->mail->setText('');
		$this->mail->setHtml($error_msg);
		$this->mail->send();
	}
}
