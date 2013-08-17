<?php
class Admin_Controller_Mail_Error extends Controller
{
	public function index()
	{
		$this->language->load('mail/error');
		$this->template->load('mail/error');

		$this->document->setTitle($this->_('head_title'));

		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('mail/error'));

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
				$mail['attachment'] = $_FILES['attachment'];
			}

			$message = $this->Model_Mail_Error->getFailedMessage($_POST['mail_fail_id']);

			if (!empty($mail['allow_html'])) {
				$mail['html'] = html_entity_decode($message['html'], ENT_QUOTES, 'UTF-8');
			} else {
				$mail['text'] = htmlentities($message['text']);
			}

			$this->mail->init();

			$this->Model_Mail_Error->deleteFailedMessage($mail['mail_fail_id']);

			if ($this->mail->send($mail)) {
				$this->message->add('success', 'text_message_sent');
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
		if (!$this->user->hasPermission('modify', 'mail/error')) {
			$this->error['permission'] = $this->_('error_permission');
		}

		if (!$_POST['from']) {
			$this->error['from'] = $this->_('error_from');
		} elseif (!$this->validation->email($_POST['from'])) {
			$this->error['from'] = $this->_('error_from_email');
		}

		if (!$_POST['to']) {
			$this->error['to'] = $this->_('error_to');
		} else {
			$emails = explode(',', $_POST['to']);

			foreach ($emails as $e) {
				if (!$this->validation->email(trim($e))) {
					$this->error['to'] = $this->_('error_to_email');
				}
			}
		}

		if ($_POST['cc']) {
			$emails = explode(',', $_POST['cc']);

			foreach ($emails as $e) {
				if (!$this->validation->email(trim($e))) {
					$this->error['cc'] = $this->_('error_cc');
				}
			}
		}

		if ($_POST['bcc']) {
			$emails = explode(',', $_POST['bcc']);

			foreach ($emails as $e) {
				if (!$this->validation->email(trim($e))) {
					$this->error['bcc'] = $this->_('error_bcc');
				}
			}
		}

		if (!$_POST['subject']) {
			$this->error['subject'] = $this->_('error_subject');
		}

		if (!$_POST['mail_fail_id'] && !$_POST['message']) {
			$this->error['message'] = $this->_('error_message');
		}

		return $this->error ? false : true;
	}
}
