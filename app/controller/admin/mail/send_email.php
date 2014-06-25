<?php
class App_Controller_Admin_Mail_SendEmail extends Controller
{

	public function index()
	{
		$this->document->setTitle(_l("Send Email"));

		if ($this->request->isPost()) {
			if (!$this->send()) {
				$this->message->add('warning', _l("Error sending email! Message was not sent!"));
			} else {
				$this->message->add('success', _l("Success: Your message has been sent!"));
			}
		}

		$this->breadcrumb->add(_l("Home"), site_url('admin'));
		$this->breadcrumb->add(_l("Send Email"), site_url('admin/mail/send_email'));

		$data['action'] = site_url('admin/mail/send_email');

		$data['cancel'] = site_url('admin');

		$defaults = array(
			'sender'     => option('config_title'),
			'from'       => option('config_email'),
			'to'         => '',
			'cc'         => '',
			'bcc'        => '',
			'subject'    => '',
			'message'    => '',
			'attachment' => '',
			'allow_html' => 1,
		);

		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$data[$key] = $_POST[$key];
			} elseif (option($key)) {
				$data[$key] = option($key);
			} else {
				$data[$key] = $default;
			}
		}

		if ($this->request->isPost()) {
			$data['allow_html'] = !isset($_POST['allow_html']) ? 0 : 1;
		}

		output($this->render('mail/send_email', $data));
	}

	public function send()
	{
		if (!$this->validate()) {
			return false;
		}

		$mail = $_POST;

		if (!empty($_FILES['attachment']) && empty($_FILES['attachment']['error'])) {
			$mail['attachment'] = $_FILES['attachment'];
		}

		if (!empty($mail['allow_html'])) {
			$mail['html'] = html_entity_decode($mail['message'], ENT_QUOTES, 'UTF-8');
		} else {
			$mail['text'] = htmlentities($mail['message']);
		}

		$this->mail->init($mail);

		if (!$this->mail->send()) {
			return false;
		}

		return true;
	}

	public function validate()
	{
		if (!user_can('modify', 'mail/send_email')) {
			$this->error['permission'] = _l("Warning: You do not have permission to modify mail messages!");
		}

		if (!$_POST['from']) {
			$this->error['from'] = _l("You must specify an email to send from!");
		} elseif (!validate('email', $_POST['from'])) {
			$this->error['from'] = _l("The From email address is invalid!");
		}

		if (!$_POST['to']) {
			$this->error['to'] = _l("You must specify an email to send to!");
		} else {
			$emails = explode(',', $_POST['to']);

			foreach ($emails as $e) {
				if (!validate('email', trim($e))) {
					$this->error['to'] = _l("The To email address is invalid!");
				}
			}
		}

		if ($_POST['cc']) {
			$emails = explode(',', $_POST['cc']);

			foreach ($emails as $e) {
				if (!validate('email', trim($e))) {
					$this->error['cc'] = _l("The Copy To email address is invalid!");
				}
			}
		}

		if ($_POST['bcc']) {
			$emails = explode(',', $_POST['bcc']);

			foreach ($emails as $e) {
				if (!validate('email', trim($e))) {
					$this->error['bcc'] = _l("The Blind Copy To email address is invalid!");
				}
			}
		}

		if (!$_POST['subject']) {
			$this->error['subject'] = _l("You must provide a Subject!");
		}

		if (!$_POST['message']) {
			$this->error['message'] = _l("You must provide a Message!");
		}

		return empty($this->error);
	}
}
