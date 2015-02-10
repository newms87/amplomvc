<?php

class App_Controller_Admin_Mail_Messages extends Controller
{

	public function index()
	{
		set_page_info('title', _l("Mail Messages"));

		if (IS_POST && $this->validate()) {

			$this->config->saveGroup('mail_messages', $_POST);

			message('success', _l("Success: You have modified mail messages!"));
		}

		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Mail Messages"), site_url('admin/mail/messages'));

		$data['action'] = site_url('admin/mail/messages');

		$data['cancel'] = site_url('admin');

		$defaults = array(
			'mail_registration_subject' => '',
			'mail_registration_message' => '',
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

		output($this->render('mail/messages', $data));
	}

	public function validate()
	{
		if (!user_can('w', 'admin/mail/messages')) {
			$this->error['permission'] = _l("Warning: You do not have permission to modify mail messages!");
		}

		return empty($this->error);
	}
}
