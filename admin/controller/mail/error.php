<?php
class Admin_Controller_Mail_Error extends Controller
{
	public function index()
	{
		$this->language->load('mail/error');
		$this->template->load('mail/error');

		$this->document->setTitle(_l("Failed Email Messages"));

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Failed Email Messages"), $this->url->link('mail/error'));

		$this->data['cancel'] = $this->url->link('common/home');

		$messages = $this->Model_Mail_Error->getFailedMessages();

		$this->data['messages'] = $messages;

		$this->data['send_message']   = $this->url->link('mail/error/resend');
		$this->data['resend_message'] = $this->url->link('mail/error/resend');
		$this->data['delete_message'] = $this->url->link('mail/error/delete');
		$this->data['load_message']   = $this->url->link('mail/error/load_message');

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	public function load_message()
	{
		$message = '';

		if (!empty($_GET['mail_fail_id'])) {
			$message = $this->Model_Mail_Error->getFailedMessage($_GET['mail_fail_id']);

			if ($message) {
				$message = $message['html'] ? $message['html'] : '__TEXT__' . $message['text'];
			}
		}

		$this->response->setOutput($message);
	}

	public function resend()
	{
		if ($this->validate()) {
			$mail = $_POST;

			if (!empty($_FILES['attachment']) && empty($_FILES['attachment']['error'])) {
				$files = $_FILES['attachment'];

				for ($i = 0; $i < count($files['name']); $i++) {
					$file_name = dirname($files['tmp_name'][$i]) . '/' . $files['name'][$i];
					rename($files['tmp_name'][$i], $file_name);
					$mail['attachment'][] = $file_name;
				}
			}

			$message = $this->Model_Mail_Error->getFailedMessage($mail['mail_fail_id']);
			$this->Model_Mail_Error->deleteFailedMessage($mail['mail_fail_id']);

			if (!empty($mail['allow_html'])) {
				$mail['html'] = html_entity_decode($message['html'], ENT_QUOTES, 'UTF-8');
			} else {
				$mail['text'] = htmlentities($message['text']);
			}

			$this->mail->init();

			$this->mail->setData($mail);

			if ($this->mail->send()) {
				$this->message->add('success', _l("Successfully resent the message!"));
			}
		}

		$this->index();
	}

	public function delete()
	{
		if (!isset($_POST['mail_fail_id'])) {
			return;
		}

		$this->Model_Mail_Error->deleteFailedMessage($_POST['mail_fail_id']);
	}

	public function validate()
	{
		if (!$this->user->can('modify', 'mail/error')) {
			$this->error['permission'] = _l("Warning: You do not have permission to access Failed Email Messages!");
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

		if (!$_POST['mail_fail_id'] && !$_POST['message']) {
			$this->error['message'] = _l("You must provide a Message!");
		}

		return $this->error ? false : true;
	}
}
