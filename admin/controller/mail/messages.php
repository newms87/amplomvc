<?php
class Admin_Controller_Mail_Messages extends Controller
{

	public function index()
	{
		$this->document->setTitle(_l("Mail Messages"));

		if ($this->request->isPost() && $this->validate()) {

			$this->config->saveGroup('mail_messages', $_POST);

			$this->message->add('success', _l("Success: You have modified mail messages!"));
		}

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Mail Messages"), $this->url->link('mail/messages'));

		$data['action'] = $this->url->link('mail/messages');

		$data['cancel'] = $this->url->link('common/home');

		$defaults = array(
			'mail_registration_subject' => '',
			'mail_registration_message' => '',
		);

		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$data[$key] = $_POST[$key];
			} elseif ($this->config->get($key)) {
				$data[$key] = $this->config->get($key);
			} else {
				$data[$key] = $default;
			}
		}

		$this->response->setOutput($this->render('mail/messages', $data));
	}

	public function validate()
	{
		if (!$this->user->can('modify', 'mail/messages')) {
			$this->error['permission'] = _l("Warning: You do not have permission to modify mail messages!");
		}

		return empty($this->error);
	}
}
