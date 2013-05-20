<?php 
class ControllerCheckoutBlockShippingAddress extends Controller {
	public function index() {
		$this->language->load('checkout/checkout');
		
		$this->template->load('checkout/block/shipping_address');
		
		if (isset($this->session->data['shipping_address_id'])) {
			$this->data['address_id'] = $this->session->data['shipping_address_id'];
		} else {
			$this->data['address_id'] = $this->customer->getAddressId();
		}

		$geo_zone_id = $this->config->get('config_allowed_shipping_zone');
		
		//The list of allowed addresses
		$address_ids = array();
		
		//If We have specified a GeoZone of allowed regions, we need to limit the list
		//of allowed addresses of the customer
		if($geo_zone_id > 0){
			$this->data['allowed_geo_zones'] = array();
			
			$zones = $this->model_localisation_zone->getZonesByGeoZone($geo_zone_id);
			
			foreach($zones as $zone){
				$country = $this->model_localisation_country->getCountry($zone['country_id']);
				$this->data['allowed_geo_zones'][] = array(
					'country' => $country,
					'zone'=> $zone
			);
			}
			
			$addresses = $this->model_account_address->getAddresses();
			
			$this->data['data_addresses'] = array();
			
			foreach($addresses as $address){
				foreach($zones as $zone){
					if($address['country_id'] == $zone['country_id'] && ($zone['zone_id'] == 0 || $address['zone_id'] == $zone['zone_id'])){
						$address_ids[] = $address['address_id'];
						$this->data['data_addresses'][] = $address;
						break;
					}
				}
			}
		}
		//otherwise we can use all the customer's addressess
		else{
			$addresses = $this->model_account_address->getAddresses();
			
			foreach($addresses as $address){
				$address_ids[] = $address['address_id'];
			}
			
			$this->data['data_addresses'] = $addresses;
		}
		
		if(!in_array($this->customer->getAddressId(), $address_ids)){
			$default_address_id = !empty($address_ids) ? current($address_ids) : 0;
			
			$this->customer->set_default_address_id($default_address_id);
		}
		
		if(isset($this->session->data['shipping_address_id']) && !in_array($this->session->data['shipping_address_id'], $address_ids)){
			unset($this->session->data['shipping_address_id']);
		}
		
		//Build Address Form
		$this->form->init('address');
		$this->form->set_template('form/address');
		$this->form->set_action($this->url->link('checkout/block/shipping_address/validate_form'));
		$this->form->set_field_options('country_id', $this->model_localisation_country->getCountries(), array('country_id' => 'name'));
		$this->form->set_field_options('default', $this->_('data_yes_no'));
		
		$this->data['form_shipping_address'] = $this->form->build();
		
		$this->data['validate_selection'] = $this->url->link('checkout/block/shipping_address/validate_selection');
		
		$this->response->setOutput($this->render());
  	}	
	
	public function validate_selection(){
		$this->language->load('checkout/checkout');
		
		$json = $this->validate();
		
		if(!$json){
			if (empty($_POST['address_id'])) {
				$json['error']['warning'] = $this->_('error_address');
			}
			
			if (!$json) {			
				$this->session->data['shipping_address_id'] = $_POST['address_id'];
				
				unset($this->session->data['shipping_method']);
			}
		}
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function validate_form(){
		$this->language->load('checkout/checkout');
		
		$json = $this->validate();
		
		if(!$json){
			//Validate Shipping Address Form
			$this->form->init('address');
			
			if(!$this->form->validate($_POST)){
				$json['error'] = $this->form->get_errors();
			}
			
			if(!$json && !$this->cart->validateShippingAddress($_POST)){
				$json['error'] +=  $this->cart->get_errors('shipping_address');
			}
			
			if(!$json){
				$this->session->data['shipping_address_id'] = $this->model_account_address->addAddress($_POST);
				
				unset($this->session->data['shipping_method']);
			}
			
			//If this is not an ajax call
			if(!isset($_POST['async'])){
				if($json){
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
		elseif (!$this->cart->hasShipping()) {
			$json['redirect'] = $this->url->link('checkout/checkout');
			$this->message->add('warning', $this->_('error_no_shipping_required'));
		}
		
		return $json;
	}
}
