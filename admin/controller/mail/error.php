<?php
class ControllerMailError extends Controller {
 
	public function index() {
		$this->load->language('mail/error');
		$this->template->load('mail/error');

		$this->document->setTitle($this->_('heading_title'));
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('mail/error'));
		
		$this->data['cancel'] = $this->url->link('common/home');
		
		$messages = $this->model_mail_error->getFailedMessages();
		
		$this->data['messages'] = $messages;
		
		$this->data['send_message'] = $this->url->link('mail/error/resend');
		$this->data['resend_message'] = $this->url->link('mail/error/resend');
		$this->data['delete_message'] = $this->url->link('mail/error/delete');
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
	
		$this->response->setOutput($this->render());
	}

	public function resend(){
		if($this->validate()){
			$mail = $_POST;
		
			if(!empty($_FILES['attachment']) && empty($_FILES['attachment']['error'])){
				$mail['attachment'] = $_FILES['attachment'];
			}
			
			if(!empty($mail['allow_html'])){
				$mail['html'] = html_entity_decode($mail['message'], ENT_QUOTES, 'UTF-8');
			}else{
				$mail['text'] = htmlentities($mail['message']);
			}
			
			$this->mail->init();
			
			$this->model_mail_error->deleteFailedMessage($mail['mail_fail_id']);
			
			if($this->mail->send($mail)){
				$this->message->add('success', 'text_message_sent');
			}
		}
		
		$this->index();
	}
	
	public function delete(){
		if(!isset($_POST['mail_fail_id'])) return;
		
		$this->model_mail_error->deleteFailedMessage($_POST['mail_fail_id']);
	}
	
	public function validate() {
		if (!$this->user->hasPermission('modify', 'mail/error')) {
			$this->error['permission'] = $this->_('error_permission');
		}
		
		if(!$_POST['from']){
			$this->error['from'] = $this->_('error_from');
		}
		elseif(!$this->validation->email($_POST['from'])){
			$this->error['from'] = $this->_('error_from_email');
		}
		
		if(!$_POST['to']){
			$this->error['to'] = $this->_('error_to');
		}
		else{
			$emails = explode(',', $_POST['to']);
			
			foreach($emails as $e){
				if(!$this->validation->email(trim($e))){
					$this->error['to'] = $this->_('error_to_email');
				}
			}
		}
		
		if($_POST['cc']){
			$emails = explode(',', $_POST['cc']);
			
			foreach($emails as $e){
				if(!$this->validation->email(trim($e))){
					$this->error['cc'] = $this->_('error_cc');
				}
			}
		}
		
		if($_POST['bcc']){
			$emails = explode(',', $_POST['bcc']);
			
			foreach($emails as $e){
				if(!$this->validation->email(trim($e))){
					$this->error['bcc'] = $this->_('error_bcc');
				}
			}
		}
		
		if(!$_POST['subject']){
			$this->error['subject'] = $this->_('error_subject');
		}
		
		if(!$_POST['message']){
			$this->error['message'] = $this->_('error_message');
		}
		
		return $this->error ? false : true;
	}
}
