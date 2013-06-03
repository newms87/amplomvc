<?php
class Admin_Controller_Mail_SendEmail extends Controller 
{
 
	public function index()
	{
		$this->load->language('mail/send_email');
		
		$this->template->load('mail/send_email');
		
		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST')) {
			if (!$this->send()) {
				$this->message->add('warning', $this->_('error_send_email'));
			}
			else {
				$this->message->add('success', $this->_('text_success'));
			}
		}
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('mail/send_email'));
		
		$this->data['action'] = $this->url->link('mail/send_email');
		
		$this->data['cancel'] = $this->url->link('common/home');
		
		$defaults = array(
			'sender' => $this->config->get('config_title'),
			'from'	=> $this->config->get('config_email'),
			'to'	=> '',
			'cc'	=> '',
			'bcc'	=> '',
			'subject' => '',
			'message' => '',
			'attachment' => '',
			'allow_html' => 1,
		);
		
		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$this->data[$key] = $_POST[$key];
			}
			elseif ($this->config->get($key)) {
				$this->data[$key] = $this->config->get($key);
			}
			else {
				$this->data[$key] = $default;
			}
		}
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
		
		if (!$this->mail->send($mail)) {
			return false;
		}
		
		return true;
	}
	
	public function validate()
	{
		if (!$this->user->hasPermission('modify', 'mail/send_email')) {
			$this->error['permission'] = $this->_('error_permission');
		}
		
		if (!$_POST['from']) {
			$this->error['from'] = $this->_('error_from');
		}
		elseif (!$this->validation->email($_POST['from'])) {
			$this->error['from'] = $this->_('error_from_email');
		}
		
		if (!$_POST['to']) {
			$this->error['to'] = $this->_('error_to');
		}
		else {
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
		
		if (!$_POST['message']) {
			$this->error['message'] = $this->_('error_message');
		}
		
		return $this->error ? false : true;
	}
}
