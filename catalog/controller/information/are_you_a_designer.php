<?php
class ControllerInformationAreYouADesigner extends Controller {
	
  	public function index() {
		$this->template->load('information/are_you_a_designer');

		$this->language->load('information/are_you_a_designer');

		$this->document->setTitle($this->_('heading_title'));

		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_catalog_designer->addDesigner($_POST);
			
			$name = $_POST['firstname'] . ' ' . $_POST['lastname'];
			
			$this->mail->init();
			
			//send message to the store admin
			$this->mail->setTo($this->config->get('config_email'));
			$this->mail->setFrom($_POST['email']);
			$this->mail->setSender($name);
			
			$insertables = $_POST;
			$insertables['store_name'] = $this->config->get('config_name');
			$insertables['store_url']  = $this->url->link('common/home');
			
			$subject = $this->tool->insertables($insertables, $this->config->get('mail_are_you_a_designer_subject'));
			$message = $this->tool->insertables($insertables, $this->config->get('mail_are_you_a_designer_message'));
			
			$this->mail->setSubject(html_entity_decode(sprintf($this->_('email_subject'), $name, $this->config->get('config_name')), ENT_QUOTES, 'UTF-8'));
			$this->mail->setHtml(html_entity_decode($_POST['description'], ENT_QUOTES, 'UTF-8'));
			$this->mail->send();
			
			//send message to the designer
			$this->mail->setTo($_POST['email']);
			$this->mail->setFrom($this->config->get('config_email'));
			$this->mail->setSender($this->config->get('config_name'));
			$this->mail->setSubject(sprintf($this->_('email_return_subject')));
			$this->mail->setHtml(sprintf($this->_('email_return_message'),$this->config->get('config_name')));
			$this->mail->send();
			
			$this->message->add('success', html_entity_decode($this->_('text_message_sent'), ENT_QUOTES, 'UTF-8'));
			
			$this->url->redirect($this->url->link('information/are_you_a_designer/success'));
		}
		
		$this->language->format('text_are_you_a_designer', $this->config->get('config_name'));
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('information/are_you_a_designer'));
		
		$this->data['captcha_url'] = $this->url->link("information/are_you_a_designer/captcha");
		$this->data['continue'] = $this->url->link('common/home');
	
		$this->data['action'] = $this->url->link('information/are_you_a_designer');
		
		$this->data['address'] = nl2br($this->config->get('config_address'));
		$this->data['telephone'] = $this->config->get('config_telephone');
		
		$defaults = array(
				'firstname'=>$this->customer->info('firstname'),
				'lastname'=>$this->customer->info('lastname'),
				'email'=>$this->customer->info('email'),
				'phone'=>$this->customer->info('telephone'),
				'brand'=>'',
				'website'=>'',
				'lookbook'=>'',
				'description'=>'',
				'category'=>array(''),
				'captcha'=>''
			);
		foreach($defaults as $key=>$default){
			$this->data[$key] = isset($_POST[$key])?$_POST[$key]:$default;
		}
		
		$this->data['categories'] = array_merge(array(''=>"( Select Category )"),$this->model_catalog_category->getCategories(-1));
		$this->data['categories'][0] = 'Other';

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

  	public function success() {
		$this->template->load('common/success');

		$this->language->load('information/are_you_a_designer');

		$this->document->setTitle($this->_('heading_title'));
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('information/are_you_a_designer'));
		
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
	
  	private function validate() {
		$name_length = array('firstname','lastname','brand');
		foreach($name_length as $name){
			if ((strlen($_POST[$name]) < 3) || (strlen($_POST[$name]) > 32)) {
					$this->error[$name] = $this->_('error_' . $name);
			}
		}

		if (!preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $_POST['email'])) {
				$this->error['email'] = $this->_('error_email');
		}

		if ((strlen($_POST['description']) < 10) || (strlen($_POST['description']) > 100000)) {
				$this->error['description'] = $this->_('error_description');
		}
		
		if(isset($_POST['category'])){
			foreach($_POST['category'] as $key=>$cat){
				if($cat === '' || $cat === '0' || $cat === 0){
					unset($_POST['category'][$key]);
					continue;
				}
				
				if($key === 'other'){
					foreach($cat as $other_key=>$other_cat){
						if(!$other_cat){
							$this->error['no_other'] = $this->_('error_blank_other');
						}
						$_POST['category']['other'.$other_key] = $other_cat;
					}
					unset($_POST['category']['other']);
				}
			}
			
			if(!count($_POST['category']))
				$this->error['category_list'] = $this->_('error_no_category');
		}
		else{
			$this->error['category_list'] = $this->_('error_no_category');
		}
		
		if (empty($this->session->data['captcha']) || ($this->session->data['captcha'] != $_POST['captcha'])) {
				$this->error['captcha'] = $this->_('error_captcha');
		}
		
		return $this->error ? false : true;
  	}

	public function captcha() {
		$this->session->data['captcha'] = $this->captcha->getCode();
		
		$this->captcha->showImage();
	}
}
