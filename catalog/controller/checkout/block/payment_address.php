<?php 
class ControllerCheckoutBlockPaymentAddress extends Controller {
	public function index() {
		$this->template->load('checkout/block/payment_address');

		$this->language->load('checkout/checkout');
		
		$this->data['data_addresses'] = $this->model_account_address->getAddresses();
		
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
			foreach($this->data['data_addresses'] as $address){
				if($address['address_id'] == $address_id){
					$this->data['address_id'] = $address_id;
					break;
				}
			}
			
			if(!$this->data['address_id']){
				unset($this->session->data['payment_address_id']);
			}
		}
		
		//Build Address Form
		$this->form->init('address');
		$this->form->set_template('form/address');
		$this->form->set_action($this->url->link('checkout/block/payment_address/validate'));
		$this->form->set_field_options('country_id', $this->model_localisation_country->getCountries(), array('country_id' => 'name'));
		$this->form->set_field_options('default', $this->_('data_yes_no'));
		
		$this->data['form_payment_address'] = $this->form->build();
		
		$this->data['validate_selection'] = $this->url->link('checkout/block/payment_address/validate_selection');
		
		$this->response->setOutput($this->render());
	}
	
	public function validate_selection(){
		$this->language->load('checkout/checkout');
		
		$json = $this->validate();
		
		if(!$json){
			if (empty($_POST['address_id'])) {
				$json['error']['warning'] = $this->_('error_address');
			}
			else {
				//TODO actually validate this!
				$this->session->data['payment_address_id'] = $_POST['address_id'];
			}
		}
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function validate_form(){
		$this->language->load('checkout/checkout');
		
		$json = $this->validate();
		
		if(!$json){
			//Validate the form (Be sure error language files are loaded! eg: checkout/checkout has errors for address.)
			$this->form->init('address');
			
			if(!$this->form->validate($_POST)){
				$json['error'] = $this->form->get_errors();
			}
			
			//Additional Error checking
			$country_info = $this->model_localisation_country->getCountry($_POST['country_id']);
			
			if (!$country_info){
				$json['error']['country_id'] = $this->_('error_country_id');
			}
					
			if (!$json['error']) {
				$this->session->data['payment_address_id'] = $this->model_account_address->addAddress($_POST);
				
				unset($this->session->data['payment_methods']);
			}

			if(!isset($_POST['async'])){
				if($json['error']){
					$this->message->add('warning', $json['error']);
				} else {
					$this->message->add('success', $this->_('text_address_success'));
				}
				
				//We redirect because we are only a block, not a full page!
				$this->url->redirect($this->url->link('checkout/checkout'));
			}
		}
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function validate() {
		$json = array();
		
		// Validate if customer is logged in.
		if (!$this->customer->isLogged()) {
			$json['redirect'] = $this->url->link('checkout/checkout');
		}
		elseif(!$this->cart->validate()){
			$json['redirect'] = $this->url->link('cart/cart');
			$this->message->add($this->cart->get_errors());
		}
		
		return $json;
	}
}