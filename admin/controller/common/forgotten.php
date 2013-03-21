<?php
class ControllerCommonForgotten extends Controller {
	

	public function index() {
$this->template->load('common/forgotten');

		if ($this->user->isLogged()) {
			$this->redirect($this->url->link('common/home'));
		}

		$this->language->load('common/forgotten');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->language->load('mail/forgotten');
			
			$code = md5(rand());
			
			$this->model_user_user->editCode($_POST['email'], $code);
			
			$subject = sprintf($this->_('text_subject'), $this->config->get('config_name'));
			
			$message  = sprintf($this->_('text_greeting'), $this->config->get('config_name')) . "\n\n";
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
			
			$this->message->add('success', $this->_('text_success'));

			$this->redirect($this->url->link('common/login'));
		}

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_forgotten'), $this->url->link('common/forgotten'));

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

	private function validate() {
		if (empty($_POST['email'])) {
			$this->error['email'] = $this->_('error_email');
		} elseif (!$this->model_user_user->getTotalUsersByEmail($_POST['email'])) {
			$this->error['email'] = $this->_('error_email');
		}

		return $this->error ? false : true;
	}
}