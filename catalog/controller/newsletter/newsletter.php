<?php
class ControllerNewsletterNewsletter extends Controller 
{

	public function index()
	{
	
		$newsletter_id = isset($_GET['newsletter_id'])?$_GET['newsletter_id']:0;
		
		if (!$newsletter_id) {
			$this->message->add('warning', $this->_('error_newsletter_preview'));
			$this->url->redirect($this->url->link('error/not_found'));
			return;
		}
		
		if (!$this->user->validate_token()) {
			$this->user->login('guest','guest');
		}
		
		if (empty($this->session->data['token'])) {
			trigger_error("There was an error while generating the Admin link. The token was not set!");
			return '';
		}
		
		$html = $this->url->load($this->url->admin('mail/newsletter/preview', 'newsletter_id=' . $newsletter_id), true);
		
		$this->response->setOutput($html);
	}
}
