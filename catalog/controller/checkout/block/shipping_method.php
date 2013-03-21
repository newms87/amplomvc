<?php 
class ControllerCheckoutBlockShippingMethod extends Controller {
  	public function index() {
      $this->template->load('checkout/block/shipping_method');

		$this->language->load('checkout/checkout');
		
      $shipping_methods = $this->cart->getShippingMethods();
      
      $this->data['guest_checkout'] = !$this->customer->isLogged();
      
      if($shipping_methods){
         $this->session->data['shipping_methods'] = $shipping_methods;
         $this->data['shipping_methods'] = $shipping_methods;
      }
      else {
         if($this->cart->has_error('checkout>shipping_address')){
            $this->data['no_shipping_address'] = $this->_('text_no_shipping_address');
         }
         else{
            $this->data['shipping_methods'] = array();
            
            $this->data['allowed_geo_zones'] = array();
               
            $geo_zone_id = $this->config->get('config_allowed_shipping_zone');
            
            if($geo_zone_id > 0){
               $zones = $this->model_localisation_zone->getZonesByGeoZone($geo_zone_id);
               
               foreach($zones as $zone){
                  $country = $this->model_localisation_country->getCountry($zone['country_id']);
                  $this->data['allowed_geo_zones'][] = array(
                     'country' => $country,
                     'zone'    => $zone
                   );
               }
            }
            
            $this->error['warning'] = $this->language->format('error_no_shipping', $this->url->link('information/contact'));
         }
      }
         
      if (isset($this->session->data['shipping_method']['code'])) {
         $this->data['code'] = $this->session->data['shipping_method']['code'];
      } elseif(!empty($this->data['shipping_methods']) && count($this->data['shipping_methods']) == 1){
         $key = key($this->data['shipping_methods']);
         $this->data['code'] = $this->data['shipping_methods'][$key]['quote'][$key]['code'];
      }else{
         $this->data['code'] = '';
      }
      
		$this->response->setOutput($this->render());
  	}
	
	public function validate() {
		$this->language->load('checkout/checkout');
		
		$json = array();		
		
		// Validate cart contents
		if (!$this->cart->validate()) {
		   $this->message->add('warning', $this->cart->get_errors());
			$json['redirect'] = $this->url->link('cart/cart');				
		}
      elseif (!$this->cart->hasShipping()) {
         $this->message->add('warning', $this->_('error_no_shipping_required'));
         $json['redirect'] = $this->url->link('checkout/checkout');
      }
		
		if (!$json) {
		   // Validate if shipping address has been set.    
         if ($this->customer->isLogged() && isset($this->session->data['shipping_address_id'])) {              
            $shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);     
         } elseif (isset($this->session->data['guest']['shipping_address'])) {
            $shipping_address = $this->session->data['guest']['shipping_address'];
         }
         else{
            $json['error']['shipping_address'] = $this->_('error_shipping_address');
         }
         
			if (!isset($_POST['shipping_method'])) {
				$json['error']['warning'] = $this->_('error_shipping');
			} else {
				$shipping = explode('.', $_POST['shipping_method']);
			   
            if(!isset($this->session->data['shipping_methods'])){
               $this->session->data['shipping_methods'] = $this->cart->getShippingMethods();
            }
            
				if (!isset($shipping[0]) || !isset($shipping[1]) || !isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]])) {			
					$json['error']['warning'] = $this->_('error_shipping');
				}
			}
			
			if (!$json) {
				$shipping = explode('.', $_POST['shipping_method']);
					
				$this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
			}							
		}
		
		$this->response->setOutput(json_encode($json));	
	}
}