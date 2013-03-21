<?php 
class ControllerCheckoutBlockPaymentAddress extends Controller {
	public function index() {
      $this->template->load('checkout/block/payment_address');

		$this->language->load('checkout/checkout');
		
		$this->data['addresses'] = $this->model_account_address->getAddresses();
      
      if(isset($this->session->data['payment_address_id'])){
         $address_id = $this->session->data['payment_address_id'];
      }
      elseif($this->customer->verifyPaymentInfo()){
         $address_id = $this->customer->getPaymentInfo('address_id');
      }
      else{
         $address_id = false;
      }
      
      $this->data['address_id'] = false;
      
      //verify payment address actually exists
      if($address_id){
         foreach($this->data['addresses'] as $address){
            if($address['address_id'] == $address_id){
               $this->data['address_id'] = $address_id;
               break;
            }
         }
         
         if(!$this->data['address_id']){
            unset($this->session->data['payment_address_id']);
         }
      }
      
      $this->template->load_template_option('checkout/block/address');
      
      $form = $this->template->get_form('address');
      
      $form->set_template('form/table_sets');
      
      $form->set_field_value('country_id', 'values', $this->model_localisation_country->getCountries());
      
      $this->data['form_payment_address'] = $form->build();
      
      $this->data['set_default'] = 1;
      
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
		
      if(!isset($_POST['payment_address'])){
         $address_type = 'existing';
      }
      else{
         $address_type = $_POST['payment_address'];
      }

		if (!$json) {
			if ($address_type == 'existing') {
				if (empty($_POST['address_id'])) {
					$json['error']['warning'] = $this->_('error_address');
				}
				
				if (!$json) {			
					$this->session->data['payment_address_id'] = $_POST['address_id'];
               
               $json['reload'] = array(
                  'payment_method'
                );
				}
			} 
         else if ($address_type == 'new') {
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
					$this->session->data['payment_address_id'] = $this->model_account_address->addAddress($_POST);
					
               $json['reload'] = array(
                  'shipping_address',
                  'payment_address'
                 );
               
					unset($this->session->data['payment_methods']);
				}		
			}		
		}
		
		$this->response->setOutput(json_encode($json));
	}
}