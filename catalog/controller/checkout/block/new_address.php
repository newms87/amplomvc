<?php 
class ControllerCheckoutBlockNewAddress extends Controller {
	public function index() {
	   $this->language->load('checkout/checkout');
      
      $this->template->load('checkout/block/new_address');
      
      //New Address Form
      $this->template->load_template_option('checkout/block/address');
      
      $form = $this->template->get_form('address');
      
      $form->set_template('form/table_sets');
      
      $form->set_field_value('country_id', 'values', $this->model_localisation_country->getCountries());
      
      $this->data['form_new_address'] = $form->build();
      
		$this->response->setOutput($this->render());
  	}
	
	public function validate() {
		$this->language->load('checkout/checkout');
		
		$json = array();
		
		// Validate if customer is logged in.
		if (!$this->customer->isLogged()) {
			$json['redirect'] = $this->url->link('checkout/checkout');
		}
      elseif(!$this->cart->validate()){
         $json['redirect'] = $this->url->link('cart/cart');
         $this->message->add($this->cart->get_errors());
      }
      elseif (!$this->cart->hasShipping()) {
			$json['redirect'] = $this->url->link('checkout/checkout');
         $this->message->add('warning', $this->_('error_no_shipping_required'));
		}
		
		if (!$json) {
			$this->template->load_template_option('checkout/block/address');
         
         //Validate the payment address
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
            
			if (!$json) {
				$this->session->data['shipping_address_id'] = $this->model_account_address->addAddress($_POST);
				
            $json['redirect'] = $this->url->link('checkout/checkout');
              
				unset($this->session->data['shipping_method']);
				unset($this->session->data['shipping_methods']);
			}
		}
		
		$this->response->setOutput(json_encode($json));
	}
}
