<?php
class Catalog_Controller_Information_Contact extends Controller 
{
	
  	public function index()
  	{
		$this->template->load('information/contact');

		$this->language->load('information/contact');

		$this->document->setTitle($this->_('heading_title'));
	
		if ($this->request->isPost() && $this->validate()) {
			$this->mail->init();
			
			$this->mail->setTo($this->config->get('config_email'));
			$this->mail->setFrom($_POST['email']);
			$this->mail->setSender($_POST['name']);
			$this->mail->setSubject(html_entity_decode(sprintf($this->_('email_subject'), $_POST['name']), ENT_QUOTES, 'UTF-8'));
			$this->mail->setText(strip_tags(html_entity_decode($_POST['enquiry'], ENT_QUOTES, 'UTF-8')));
			$this->mail->send();

			$this->url->redirect($this->url->link('information/contact/success'));
		}
		
		$this->_('text_contact_us', $this->config->get('config_name'));
		$this->_('text_contact_info', $this->config->get('config_email'),$this->config->get('config_email'));
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('information/contact'));
		
		$this->data['captcha_url'] = $this->url->link("information/contact/captcha");
		$this->data['continue'] = $this->url->link('common/home');
	
		$this->data['action'] = $this->url->link('information/contact');
		$this->data['store'] = $this->config->get('config_name');
		$this->data['address'] = nl2br($this->config->get('config_address'));
		$this->data['telephone'] = $this->config->get('config_telephone');
		$this->data['fax'] = $this->config->get('config_fax');
		
		$defaults = array('name'=>$this->customer->info('firstname'),
								'email'=>$this->customer->info('email'),
								'enquiry'=>'',
								'captcha'=>''
							);
		foreach ($defaults as $key=>$default) {
			$this->data[$key] = isset($_POST[$key])?$_POST[$key]:$default;
		}

		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);
				
 		$this->response->setOutput($this->render());
  	}

  	public function success()
  	{
		$this->template->load('common/success');

		$this->language->load('information/contact');

		$this->document->setTitle($this->_('heading_title'));
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('information/contact'));
		
		$this->data['continue'] = $this->url->link('common/home');

		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);
				
 		$this->response->setOutput($this->render());
	}
	
  	private function validate()
  	{
		if ((strlen($_POST['name']) < 3) || (strlen($_POST['name']) > 32)) {
				$this->error['name'] = $this->_('error_name');
		}

		if (!preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $_POST['email'])) {
				$this->error['email'] = $this->_('error_email');
		}

		if ((strlen($_POST['enquiry']) < 10) || (strlen($_POST['enquiry']) > 3000)) {
				$this->error['enquiry'] = $this->_('error_enquiry');
		}

		if (empty($this->session->data['captcha']) || ($this->session->data['captcha'] != $_POST['captcha'])) {
				$this->error['captcha'] = $this->_('error_captcha');
		}
		
		return $this->error ? false : true;
  	}

	public function captcha()
	{
		$this->session->data['captcha'] = $this->captcha->getCode();
		
		$this->captcha->showImage();
	}
}
