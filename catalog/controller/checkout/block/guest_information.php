<?php 
class ControllerCheckoutBlockGuestInformation extends Controller {
  	public function index() {
      $this->template->load('checkout/block/guest_information');

    	$this->language->load('checkout/checkout');
      
      $this->language->load('checkout/block/guest_information');
		
      $guest_post = isset($_POST['guest']) ? $_POST['guest'] : array();
      $guest_session = isset($this->session->data['guest']) ? $this->session->data['guest'] : array();
		
      $form = $this->template->get_form('register');
      
      $form->set_template('form/table');
      
      $form->fill_data_from($guest_post, $guest_session);
      
      $this->data['form_general'] = $form->build();
      
      
      //Payment Address Form
      $guest_post_payment = isset($guest_post['payment_address']) ? $guest_post['payment_address'] : null;
      $guest_session_payment = isset($guest_session['payment_address']) ? $guest_session['payment_address'] : null;
      
      $form = $this->template->get_form('payment_address');
      
      $form->set_template('form/table');
      
      $form->fill_data_from($guest_post_payment, $guest_session_payment);
         
      $countries = $this->model_localisation_country->getCountries();
      $form->set_field_value('country_id', 'values', $countries);
      
      $zones = $this->model_localisation_zone->getZonesByCountryId($form->get_field_value('country_id', 'select'));
      $form->set_field_value('zone_id', 'values', $zones);
      
      $this->data['form_payment_address'] = $form->build();
      
      
      //Shipping
      $this->data['shipping_required'] = $this->cart->hasShipping();
      
      if($this->data['shipping_required']){
            
         //Shipping Address Form
         $guest_post_shipping = isset($guest_post['shipping_address']) ? $guest_post['shipping_address'] : null;
         $guest_session_shipping = isset($guest_session['shipping_address']) ? $guest_session['shipping_address'] : null;
         
         $form = $this->template->get_form('shipping_address');
         
         $form->set_template('form/table');
         
         $form->fill_data_from($guest_post_shipping, $guest_session_shipping);
            
         $countries = $this->model_localisation_country->getCountries();
         $form->set_field_value('country_id', 'values', $countries);
         
         $zones = $this->model_localisation_zone->getZonesByCountryId($form->get_field_value('country_id', 'select'));
         $form->set_field_value('zone_id', 'values', $zones);
         
         $this->data['form_shipping_address'] = $form->build();
         
         
         if (isset($this->session->data['guest']['same_shipping_address'])) {
            $this->data['same_shipping_address'] = $this->session->data['guest']['same_shipping_address'];
         } else {
            $this->data['same_shipping_address'] = true;
         }
      }
      
		$this->response->setOutput($this->render());		
  	}
	
	public function validate() {
	   $this->template->load('checkout/block/guest_information');
      
    	$this->language->load('checkout/checkout');
      
      $json = array();
		
		if ($this->customer->isLogged()) {
			$json['redirect'] = $this->url->link('checkout/checkout');
		} 			
      elseif ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$json['redirect'] = $this->url->link('cart/cart');		
		}
      elseif (!$this->config->get('config_guest_checkout') || $this->cart->hasDownload()) {
			$json['redirect'] = $this->url->link('cart/cart');
		} 
	   
      $address_types = array('payment_address');
      
      if(!isset($_POST['same_shipping_address'])){
         $address_types[] = 'shipping_address';
      }
      
      if (!$json) {
         //Validate Registration
         $form = $this->template->get_form('register');
         
         if(!$form->validate($_POST)){
            foreach($form->get_errors() as $field => $error){
               $json['error'][$field] = $this->_('error_' . $field);
            }
         }
         
         //Validate the payment address
         $form = $this->template->get_form('payment_address');
         
         $country_info = $this->model_localisation_country->getCountry($_POST['payment_address']['country_id']);
         
         if (!$country_info || !$country_info['postcode_required']){
            $form->set_field_value('postcode', 'validate', false);
         }

         if(!$form->validate($_POST['payment_address'])){
            foreach($form->get_errors() as $field => $error){
               $json['error']['payment_address[' . $field . ']'] = $this->_('error_' . $field);
            }
         }
         
         
         //Validate the shipping address if different than payment address
         if(empty($_POST['same_shipping_address'])){
            $form = $this->template->get_form('shipping_address');
            
            $country_info = $this->model_localisation_country->getCountry($_POST['shipping_address']['country_id']);
            
            if (!$country_info || !$country_info['postcode_required']){
               $form->set_field_value('postcode', 'validate', false);
            }
            
            if(!$form->validate($_POST['shipping_address'])){
               foreach($form->get_errors() as $field => $error){
                  $json['error']['shipping_address[' . $field . ']'] = $this->_('error_' . $field);
               }
            }
         }
		}
			
		if (!$json) {
		   //Load Registration Info for guest
		   $form = $this->template->get_form('register');
         
         foreach(array_keys($form->get_fields()) as $name){
            $this->session->data['guest'][$name] = $_POST[$name];
         }
         
         
         //Load Payment Address info for guest
         $form = $this->template->get_form('payment_address');
         
         foreach(array_keys($form->get_fields()) as $name){
            $this->session->data['guest']['payment_address'][$name] = $_POST['payment_address'][$name];
         }
         
         //If we are not using separate fields for the names for this address form, use the registration form fields
         if(!isset($this->session->data['guest']['payment_address']['firstname'])){
            $this->session->data['guest']['payment_address']['firstname'] = $_POST['firstname'];
         }
         
         if(!isset($this->session->data['guest']['payment_address']['lastname'])){
            $this->session->data['guest']['payment_address']['lastname'] = $_POST['lastname'];
         }
         
         $country_info = $this->model_localisation_country->getCountry($_POST['payment_address']['country_id']);
            
         $this->session->data['guest']['payment_address']['country']        = $country_info ? $country_info['name'] : '';
         $this->session->data['guest']['payment_address']['iso_code_2']     = $country_info ? $country_info['iso_code_2'] : '';
         $this->session->data['guest']['payment_address']['iso_code_3']     = $country_info ? $country_info['iso_code_3'] : '';
         $this->session->data['guest']['payment_address']['address_format'] = $country_info ? $country_info['address_format'] : '';
         
         $zone_info = $this->model_localisation_zone->getZone($_POST['payment_address']['zone_id']);
         
         $this->session->data['guest']['payment_address']['zone']      = $zone_info ? $zone_info['name'] : '';
         $this->session->data['guest']['payment_address']['zone_code'] = $zone_info ? $zone_info['code'] : '';
         
			
         //Determine if Same Shipping Address or Different than Payment
			if (!empty($_POST['same_shipping_address'])) {
				$this->session->data['guest']['same_shipping_address'] = true;
            
            $this->session->data['guest']['shipping_address'] = $this->session->data['guest']['payment_address'];
			} else {
				$this->session->data['guest']['same_shipping_address'] = false;
            
            //Load Shipping Address info for guest
            $form = $this->template->get_form('shipping_address');
            
            foreach(array_keys($form->get_fields()) as $name){
               $this->session->data['guest']['shipping_address'][$name] = $_POST['shipping_address'][$name];
            }
            
            //If we are not using separate fields for the names for this address form, use the registration form fields
            if(!isset($this->session->data['guest']['shipping_address']['firstname'])){
               $this->session->data['guest']['shipping_address']['firstname'] = $_POST['firstname'];
            }
            
            if(!isset($this->session->data['guest']['shipping_address']['lastname'])){
               $this->session->data['guest']['shipping_address']['lastname'] = $_POST['lastname'];
            }
            
            $country_info = $this->model_localisation_country->getCountry($_POST['shipping_address']['country_id']);
               
            $this->session->data['guest']['shipping_address']['country']        = $country_info ? $country_info['name'] : '';
            $this->session->data['guest']['shipping_address']['iso_code_2']     = $country_info ? $country_info['iso_code_2'] : '';
            $this->session->data['guest']['shipping_address']['iso_code_3']     = $country_info ? $country_info['iso_code_3'] : '';
            $this->session->data['guest']['shipping_address']['address_format'] = $country_info ? $country_info['address_format'] : '';
            
            $zone_info = $this->model_localisation_zone->getZone($_POST['shipping_address']['zone_id']);
            
            $this->session->data['guest']['shipping_address']['zone']      = $zone_info ? $zone_info['name'] : '';
            $this->session->data['guest']['shipping_address']['zone_code'] = $zone_info ? $zone_info['code'] : '';
			}
			
			$this->session->data['account'] = 'guest';
			
         $json['reload'] = array(
            'shipping_method',
            'payment_method'
          );
          
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
		}
					
		$this->response->setOutput(json_encode($json));	
	}
}