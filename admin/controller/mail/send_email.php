<?php
class Admin_Controller_Mail_SendEmail extends Controller
{

	public function index()
	{
		$this->language->load('mail/send_email');

		$this->template->load('mail/send_email');

		$this->document->setTitle(_l("Send Email"));

		if ($this->request->isPost()) {
			if (!$this->send()) {
				$this->message->add('warning', $this->_('error_send_email'));
			} else {
				$this->message->add('success', _l("Success: Your message has been sent!"));
			}
		}

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Send Email"), $this->url->link('mail/send_email'));

		$this->data['action'] = $this->url->link('mail/send_email');

		$this->data['cancel'] = $this->url->link('common/home');

		$defaults = array(
			'sender'     => $this->config->get('config_title'),
			'from'       => $this->config->get('config_email'),
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
				$this->data[$key] = $_POST[$key];
			} elseif ($this->config->get($key)) {
				$this->data[$key] = $this->config->get($key);
			} else {
				$this->data[$key] = $default;
			}
		}

		if ($this->request->isPost()) {
			$this->data['allow_html'] = !isset($_POST['allow_html']) ? 0 : 1;
		}

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
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

		$this->mail->init();

		$this->mail->setData($mail);

		if (!$this->mail->send()) {
			return false;
		}

		return true;
	}

	public function validate()
	{
		if (!$this->user->can('modify', 'mail/send_email')) {
			$this->error['permission'] = _l("Warning: You do not have permission to modify mail messages!");
		}

		if (!$_POST['from']) {
			$this->error['from'] = _l("You must specify an email to send from!");
		} elseif (!$this->validation->email($_POST['from'])) {
			$this->error['from'] = _l("The From email address is invalid!");
		}

		if (!$_POST['to']) {
			$this->error['to'] = _l("You must specify an email to send to!");
		} else {
			$emails = explode(',', $_POST['to']);

			foreach ($emails as $e) {
				if (!$this->validation->email(trim($e))) {
					$this->error['to'] = _l("The To email address is invalid!");
				}
			}
		}

		if ($_POST['cc']) {
			$emails = explode(',', $_POST['cc']);

			foreach ($emails as $e) {
				if (!$this->validation->email(trim($e))) {
					$this->error['cc'] = _l("The Copy To email address is invalid!");
				}
			}
		}

		if ($_POST['bcc']) {
			$emails = explode(',', $_POST['bcc']);

			foreach ($emails as $e) {
				if (!$this->validation->email(trim($e))) {
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

		return $this->error ? false : true;
	}
}
