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

		send_mail($data);

		$data['text'] = '';
		$data['html'] = $error_msg;

		send_mail($data);
	}
}
