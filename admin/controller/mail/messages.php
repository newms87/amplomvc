<?php
class ControllerMailMessages extends Controller {
 
	public function index() {
		$this->load->language('mail/messages');
		
		$this->template->load('mail/messages');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			
			$this->model_setting_setting->editSetting('mail_messages', $_POST);
			
			$this->message->add('success', $this->_('text_success'));
		}
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('mail/messages'));
		
		$this->data['action'] = $this->url->link('mail/messages');
		
		$this->data['cancel'] = $this->url->link('common/home');

		$defaults = array(
			'mail_registration_subject' => '',
			'mail_registration_message' => '',
		);
		
		foreach($defaults as $key => $default){
			if(isset($_POST[$key])){
				$this->data[$key] = $_POST[$key];
			}
			elseif($this->config->get($key)){
				$this->data[$key] = $this->config->get($key);
			}
			else{
				$this->data[$key] = $default;
			}
		}
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
	
		$this->response->setOutput($this->render());
	}

	public function validate() {
		if (!$this->user->hasPermission('modify', 'mail/messages')) {
			$this->error['permission'] = $this->_('error_permission');
		}

		return $this->error ? false : true;
	}
}
