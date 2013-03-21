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
               'zone'    => $zone
             );
         }
         
         $addresses = $this->model_account_address->getAddresses();
         
         $this->data['addresses'] = array();
         
         foreach($addresses as $address){
            foreach($zones as $zone){
               if($address['country_id'] == $zone['country_id'] && ($zone['zone_id'] == 0 || $address['zone_id'] == $zone['zone_id'])){
                  $address_ids[] = $address['address_id'];
                  $this->data['addresses'][] = $address;
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
			
			$this->data['addresses'] = $addresses;
      }
      
      if(!in_array($this->customer->getAddressId(), $address_ids)){
         $default_address_id = !empty($address_ids) ? current($address_ids) : 0;
         
         $this->customer->set_default_address_id($default_address_id);
      }
      
      if(isset($this->session->data['shipping_address_id']) && !in_array($this->session->data['shipping_address_id'], $address_ids)){
         unset($this->session->data['shipping_address_id']);
      }
      
      $this->template->load_template_option('checkout/block/address');
      
      $form = $this->template->get_form('address');
      
      $form->set_template('form/table_sets');
      
      $form->set_field_value('country_id', 'values', $this->model_localisation_country->getCountries());
      
      $this->data['form_shipping_address'] = $form->build();
      
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
      elseif (!$this->cart->hasShipping()) {
			$json['redirect'] = $this->url->link('checkout/checkout');
         $this->message->add('warning', $this->_('error_no_shipping_required'));
		}
		
		if (!$json) {
			if ($_POST['shipping_address'] == 'existing') {
				if (empty($_POST['address_id'])) {
					$json['error']['warning'] = $this->_('error_address');
				}
				
				if (!$json) {			
					$this->session->data['shipping_address_id'] = $_POST['address_id'];
					
               $json['reload'] = array(
                  'shipping_method',
                );
                
					unset($this->session->data['shipping_method']);							
					unset($this->session->data['shipping_methods']);
				}
			}
         else if ($_POST['shipping_address'] == 'new') {
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
				
            
            $geo_zone = $this->config->get('config_allowed_shipping_zone');
            
            if($geo_zone > 0){
               $valid_country_id = $valid_zone_id = false;
               
               $zones = $this->model_localisation_zone->getZonesByGeoZone($geo_zone);
               
               foreach($zones as $z){
                  if($_POST['country_id'] == $z['country_id']){
                     $valid_country_id = true;
                     if($_POST['zone_id'] == $z['zone_id'] || $z['zone_id'] == 0){
                        $valid_zone_id = true;
                     }
                  }
               }
               
               if(!$valid_country_id){
                  $json['error']['country_id'] = $this->_('error_country_shipping');
               }
               
               if(!$valid_zone_id){
                  $json['error']['zone_id'] = $this->_('error_zone_shipping');
               }
            }
            
				if (!$json) {
					$this->session->data['shipping_address_id'] = $this->model_account_address->addAddress($_POST);
					
               $json['reload'] = array(
                  'shipping_address',
                  'payment_address'
                 );
                 
					unset($this->session->data['shipping_method']);
					unset($this->session->data['shipping_methods']);
				}
			}
		}
		
		$this->response->setOutput(json_encode($json));
	}
}
