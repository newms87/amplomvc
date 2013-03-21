<?php 
class ControllerCheckoutBlockRegister extends Controller {
  	public function index() {
  	   $this->language->load('checkout/checkout');
      
  	   $this->template->load('checkout/block/register');
		
      $this->language->format('entry_newsletter', $this->config->get('config_name'));
		
      $this->data['register_url'] = $this->url->link('checkout/block/register/validate', 'no_ajax=1');
      
		//Register Form
      $form = $this->template->get_form('register');
      
      $form->set_template('form/table');
      
      $form->fill_data_from('POST', 'SESSION');
      
      $this->data['form_register'] = $form->build();
      
      
      //Password Form
      $form = $this->template->get_form('password');
      
      $form->set_template('form/table');
      
      $this->data['form_password'] = $form->build();
      
      
      //Address Form
      $form = $this->template->get_form('address');
      
      $form->set_template('form/table');
      
      $form->fill_data_from('POST', 'SESSION', 'DEFAULT');
      
      //fill country value list
      $countries = $this->model_localisation_country->getCountries();
      
      $form->set_field_value('country_id', 'values', $countries);
      
      //fill zone value list
      $zones = $this->model_localisation_zone->getZonesByCountryId($form->get_field_value('country_id', 'select'));
      
      $form->set_field_value('zone_id', 'values', $zones);
      
      $this->data['form_address'] = $form->build();
      
      
      if ($this->config->get('config_account_id')) {
			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));
			
			if ($information_info) {
				$this->language->format('text_agree', $this->url->link('information/information/info', 'information_id=' . $this->config->get('config_account_id')), $information_info['title'], $information_info['title']);
			} else {
				$this->language->set('text_agree', '');
			}
		} else {
			$this->language->set('text_agree', '');
		}
		
		$this->data['shipping_required'] = $this->cart->hasShipping();
      
		$this->response->setOutput($this->render());		
  	}
	
	public function validate() {
	   $this->template->load('checkout/block/register');
      
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
      
      if (!$json) {
         //Validate Registration
         $form = $this->template->get_form('register');
         
         if(!$form->validate($_POST)){
            foreach($form->get_errors() as $field => $error){
               $json['error'][$field] = $this->_('error_' . $field);
            }
         }
         
         if ($this->model_account_customer->getTotalCustomersByEmail($_POST['email'])) {
            $json['error']['email'] = $this->_('error_exists');
         }
         
         //Validate Password
         $form = $this->template->get_form('password');
         
         if(!$form->validate($_POST)){
            foreach($form->get_errors() as $field => $error){
               $json['error'][$field] = $this->_('error_' . $field);
            }
         }
   
         if ($_POST['confirm'] !== $_POST['password']) {
            $json['error']['confirm'] = $this->_('error_confirm');
         }
         
         
         //Validate Address
         $form = $this->template->get_form('address');
         
         $country_info = $this->model_localisation_country->getCountry($_POST['country_id']);
         
         if (!$country_info || !$country_info['postcode_required']){
            $form->set_field_value('postcode', 'validate', false);
         }
         
         if(!$form->validate($_POST)){
            foreach($form->get_errors() as $field => $error){
               $json['error'][$field] = $this->_('error_' . $field);
            }
         }
         
			if ($this->config->get('config_account_id')) {
				   
				$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));
				
				if ($information_info && !isset($_POST['agree'])) {
					$json['error']['warning'] = sprintf($this->_('error_agree'), $information_info['title']);
				}
			}
		}
		
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
			
			unset($this->session->data['guest']);
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);	
			unset($this->session->data['payment_methods']);
		}
		
      if(isset($_GET['no_ajax'])){
         if(isset($json['redirect'])){
            $this->redirect($json['redirect']);
         }
         elseif(isset($json['error'])){
            $this->message->add('warning', $json['error']);
         }
         else{
            $this->redirect($this->url->link('checkout/checkout'));
         }
      }

		$this->response->setOutput(json_encode($json));	
	}
}