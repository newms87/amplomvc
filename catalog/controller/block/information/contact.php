<?php
class Catalog_Controller_Block_Information_Contact extends Controller
{
	public function index($settings)
	{
		$this->language->load('block/information/contact');
		
		if ($this->request->isPost() && $this->validate()) {
			$this->mail->callController('contact', $_POST);
			
			$this->success();
			
			return;
		}
		
		//The Contact Form
		$contact_form = $this->getForm();
		
		//The Block template
		$this->template->load('block/information/contact');
		
		$contact_info = html_entity_decode($settings['contact_info'], ENT_QUOTES, 'UTF-8');
		
		$insertables = array(
			'contact_form' => $contact_form,
		);
		
		$this->data['contact_info'] = $this->tool->insertables($insertables, $contact_info);
		
		$this->render();
	}
	
	private function getForm()
	{
		//Template and Language
		$this->template->load('block/information/contact_form');
		
		//Captcha Image
		$this->data['captcha_url'] = $this->url->link("block/information/contact/captcha");
		
		//Action
		$this->data['action'] = $this->url->here();
		
		//Load Value or Defaults
		$defaults = array(
			'name' => $this->customer->info('firstname'),
			'email' => $this->customer->info('email'),
			'enquiry' => '',
			'captcha' => '',
		);
		
		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$this->data[$key] = $_POST[$key];
			} else {
				$this->data[$key] = $default;
			}
		}
		
		//Render
		return $this->render();
	}
	
	public function success()
  	{
		$this->template->load('block/information/contact_success');
		$this->language->load('block/information/contact');

		$this->data['continue'] = $this->url->link('common/home');
		
 		$this->render();
	}
	
  	private function validate()
  	{
		if (!$this->validation->text($_POST['name'], 3, 64)) {
			$this->error['name'] = $this->_('error_name');
		}

		if (!$this->validation->email($_POST['email'])) {
			$this->error['email'] = $this->_('error_email');
		}

		if (!$this->validation->text($_POST['enquiry'], 10, 3000)) {
			$this->error['enquiry'] = $this->_('error_enquiry');
		}

		if (!$this->captcha->validate($_POST['captcha'])) {
			$this->error['captcha'] = $this->_('error_captcha');
		}
		
		return $this->error ? false : true;
  	}

	public function captcha()
	{
		$this->captcha->generate();
	}
}
