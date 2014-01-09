<?php
class Admin_Controller_Common_Forgotten extends Controller
{
	public function index()
	{
		$this->template->load('common/forgotten');

		if ($this->user->isLogged()) {
			$this->url->redirect('common/home');
		}

		$this->language->load('common/forgotten');

		$this->document->setTitle(_l("Forgot Your Password?"));

		if ($this->request->isPost() && $this->validate()) {
			$this->language->load('mail/forgotten');

			$code = md5(rand());

			$this->Model_User_User->editCode($_POST['email'], $code);

			$subject = sprintf($this->_('text_subject'), $this->config->get('config_name'));

			$message = sprintf($this->_('text_greeting'), $this->config->get('config_name')) . "\n\n";
			$message .= sprintf($this->_('text_change'), $this->config->get('config_name')) . "\n\n";
			$message .= $this->url->link('common/reset', 'code=' . $code) . "\n\n";
			$message .= sprintf($this->_('text_ip'), $_SERVER['REMOTE_ADDR']) . "\n\n";

			$this->mail->init();

			$this->mail->setTo($_POST['email']);
			$this->mail->setFrom($this->config->get('config_email'));
			$this->mail->setSender($this->config->get('config_name'));
			$this->mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
			$this->mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));

			$this->mail->send();

			$this->message->add('success', _l("An email with a confirmation link has been sent your admin email address."));

			$this->url->redirect('common/login');
		}

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Forgotten Password"), $this->url->link('common/forgotten'));

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		$this->data['action'] = $this->url->link('common/forgotten');

		$this->data['cancel'] = $this->url->link('common/login');

		if (isset($_POST['email'])) {
			$this->data['email'] = $_POST['email'];
		} else {
			$this->data['email'] = '';
		}

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validate()
	{
		if (empty($_POST['email'])) {
			$this->error['email'] = _l("Warning: The E-Mail Address was not found in our records, please try again!");
		} elseif (!$this->Model_User_User->getTotalUsersByEmail($_POST['email'])) {
			$this->error['email'] = _l("Warning: The E-Mail Address was not found in our records, please try again!");
		}

		return $this->error ? false : true;
	}
}
