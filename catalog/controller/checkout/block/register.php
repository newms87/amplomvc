<?php 
class ControllerCheckoutBlockRegister extends Controller {
  	public function index() {
  		$this->language->load('checkout/checkout');
  		$this->template->load('checkout/block/register');
		
		$this->language->format('entry_newsletter', $this->config->get('config_name'));
		
		$this->data['action_register'] = $this->url->link('checkout/block/register/validate');
		
		//Registration Details
		$this->form->init('register');
		$this->form->set_template('form/single_column');
		$this->form->show_form_tag(false);
		$this->form->disable_fields('password', 'confirm');
		
		$this->data['form_register'] = $this->form->build();
		
		//Password Fields
		$this->form->set_fields('password', 'confirm');
		
		$this->data['form_password'] = $this->form->build();
		
		//Address Form
		$this->form->init('address');
		$this->form->set_template('form/single_column');
		$this->form->show_form_tag(false);
		$this->form->set_field_options('country_id', $this->model_localisation_country->getCountries(), array('country_id' => 'name'));
		$this->form->disable_fields('firstname', 'lastname', 'submit_address', 'default');
		
		$this->data['form_address'] = $this->form->build();
		
		//Terms and Conditions
		if ($this->config->get('config_account_id')) {
			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));
			
			if ($information_info) {
				$this->language->format('text_agree', $this->url->link('information/information/info', 'information_id=' . $this->config->get('config_account_id')), $information_info['title'], $information_info['title']);
				
				$this->data['agree_to_terms'] = true;
			}
		}
		
		$this->response->setOutput($this->render());		
  	}
	
	public function validate() {
		$this->language->load('checkout/checkout');
		
		$json = array();
		
		// Validate if customer is logged in.
		if ($this->customer->isLogged()) {
			$json['redirect'] = $this->url->link('checkout/checkout');
		}
		elseif(!$this->cart->validate()){
			$json['redirect'] = $this->url->link('cart/cart');
			$this->message->add($this->cart->get_errors());
		}
		
		if($json){
			if(!empty($_POST['async'])){
				$this->url->redirect($this->url->link('checkout/checkout'));
			}
			
			$this->response->setOutput(json_encode($json));
			return;
		}

		//Validate the Address Form
		$this->form->init('address');
		
		if(!$this->form->validate($_POST)){
			$json['error'] = $this->form->get_errors();
		}
		
		//Additional Error checking
		$country_info = $this->model_localisation_country->getCountry($_POST['country_id']);
		
		if (!$country_info){
			$json['error']['country_id'] = $this->_('error_country_id');
		}
		
		//Validate Register Form
		$this->form->init('register');
		
		if(!$this->form->validate($_POST)){
			$json['error'] = $this->form->get_errors();
		}
		
		if ($this->model_account_customer->getTotalCustomersByEmail($_POST['email'])) {
			$json['error']['email'] = $this->_('error_exists');
		}
			
		if ($_POST['confirm'] !== $_POST['password']) {
			$json['error']['confirm'] = $this->_('error_confirm');
		}
		
		if ($this->config->get('config_account_id')) {
			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));
			
			if ($information_info && !isset($_POST['agree'])) {
				$json['error']['agree'] = sprintf($this->_('error_agree'), $information_info['title']);
			}
		}
		
		//If the Form is valid
		if (!$json) {
			$this->model_account_customer->addCustomer($_POST);
			
			$this->session->data['account'] = 'register';
			
			if (!$this->config->get('config_customer_approval')) {
				$this->customer->login($_POST['email'], $_POST['password']);
				
				$this->session->data['payment_address_id'] = $this->customer->getPaymentInfo('address_id');
				
				if (!empty($_POST['shipping_address'])) {
					$this->session->data['shipping_address_id'] = $this->customer->getAddressId();
				}
				$json['redirect'] = $this->url->link('checkout/checkout');
				
			} else {
				$json['redirect'] = $this->url->link('account/success');
			}
			
			unset($this->session->data['shipping_method']);
			unset($this->session->data['payment_method']);	
		}
		
		//If this is not an ajax call, redirect w/ a message
		if(!isset($_POST['async'])){
			if($json['error']){
				$this->message->add('warning', $json['error']);
			} else {
				$this->message->add('success', $this->_('text_address_success'));
			}
			
			//We redirect because we are only a block, not a full page!
			$this->url->redirect($this->url->link('checkout/checkout'));
		}

		$this->response->setOutput(json_encode($json));	
	}
}