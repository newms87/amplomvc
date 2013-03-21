<?php
class ControllerCartBlockShipping extends Controller{
   
   public function index($settings = null){
      $this->language->load('cart/block/shipping');
      
      if (isset($_POST['shipping_method']) && $this->validateShipping()) {
         $shipping = explode('.', $_POST['shipping_method']);
         
         $this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
         
         $this->message->add('success', $this->_('text_shipping'));
         
         if($_POST['redirect']){
            $this->redirect(urldecode($_POST['redirect']));
         }
      }
      
      $this->data['action'] = '';
      
      $defaults = array(
         'country_id' => $this->config->get('config_country_id'),
         'zone_id' => '',
         'postcode' => '',
         'shipping_method' => '',
      );
      
      foreach($defaults as $key=>$default){
         if (isset($_POST[$key])) {
            $this->data[$key] = $_POST[$key];
         } elseif (isset($this->session->data[$key])) {
            $this->data[$key] = $this->session->data[$key];
         } elseif(isset($this->session->data['guest']['shipping_address'][$key])) {
            $this->data[$key] = $this->session->data['guest']['shipping_address'][$key];
         }else{
            $this->data[$key] = $default;
         }
      }
      
      if(isset($this->data['shipping_method']['code'])) {
         $this->data['shipping_method'] = $this->data['shipping_method']['code']; 
      }
      
      $this->data['apply_shipping'] = $this->url->link('cart/block/shipping');
      $this->data['redirect'] = $this->url->current_page();
      
      $this->data['countries'] = $this->model_localisation_country->getCountries();
      
      $this->response->setOutput($this->render());
   }
   
   public function quote() {
      $this->language->load('cart/block/shipping');
      
      $json = array();  
      
      if (!$this->cart->hasProducts()) {
         $json['error']['warning'] = $this->_('error_product');            
      }           

      if (!$this->cart->hasShipping()) {
         $json['error']['warning'] = sprintf($this->_('error_no_shipping'), $this->url->link('information/contact'));            
      }           
      
      if ($_POST['country_id'] == '') {
         $json['error']['country_id'] = $this->_('error_country');
      }
      
      if ($_POST['zone_id'] == '') {
         $json['error']['zone_id'] = $this->_('error_zone');
      }
         
      $country_info = $this->model_localisation_country->getCountry($_POST['country_id']);
      
      if ($country_info && $country_info['postcode_required'] && (strlen($_POST['postcode']) < 2) || (strlen($_POST['postcode']) > 10)) {
         $json['error']['postcode'] = $this->_('error_postcode');
      }
                  
      if (!$json) {     
         $this->tax->setShippingAddress($_POST['country_id'], $_POST['zone_id'], $_POST['postcode']);
      
         $this->session->data['guest']['shipping_address']['country_id'] = $_POST['country_id'];
         $this->session->data['guest']['shipping_address']['zone_id'] = $_POST['zone_id'];
         $this->session->data['guest']['shipping_address']['postcode'] = $_POST['postcode'];
      
         if ($country_info) {
            $country = $country_info['name'];
            $iso_code_2 = $country_info['iso_code_2'];
            $iso_code_3 = $country_info['iso_code_3'];
            $address_format = $country_info['address_format'];
         } else {
            $country = '';
            $iso_code_2 = '';
            $iso_code_3 = ''; 
            $address_format = '';
         }
         
         $zone_info = $this->model_localisation_zone->getZone($_POST['zone_id']);
         
         if ($zone_info) {
            $zone = $zone_info['name'];
            $code = $zone_info['code'];
         } else {
            $zone = '';
            $code = '';
         }  
       
         $address_data = array(
            'firstname'      => '',
            'lastname'       => '',
            'company'        => '',
            'address_1'      => '',
            'address_2'      => '',
            'postcode'       => $_POST['postcode'],
            'city'           => '',
            'zone_id'        => $_POST['zone_id'],
            'zone'           => $zone,
            'zone_code'      => $code,
            'country_id'     => $_POST['country_id'],
            'country'        => $country, 
            'iso_code_2'     => $iso_code_2,
            'iso_code_3'     => $iso_code_3,
            'address_format' => $address_format
         );
      
         $quote_data = array();
         
         $results = $this->model_setting_extension->getExtensions('shipping');
         
         foreach ($results as $result) {
            if ($this->config->get($result['code'] . '_status')) {
               $this->load->model('shipping/' . $result['code']);
               
               $quote = $this->{'model_shipping_' . $result['code']}->getQuote($address_data); 
      
               if ($quote) {
                  $quote_data[$result['code']] = array(
                     'title'      => $quote['title'],
                     'quote'      => $quote['quote'], 
                     'sort_order' => $quote['sort_order'],
                     'error'      => $quote['error']
                  );
               }
            }
         }
   
         $sort_order = array();
        
         foreach ($quote_data as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
         }
   
         array_multisort($sort_order, SORT_ASC, $quote_data);
         
         $this->session->data['shipping_methods'] = $quote_data;
         
         if ($this->session->data['shipping_methods']) {
            $json['shipping_method'] = $this->session->data['shipping_methods']; 
         } else {
            $json['error']['warning'] = sprintf($this->_('error_no_shipping'), $this->url->link('information/contact'));
         }           
      }  
      
      $this->response->setOutput(json_encode($json));                
   }

   private function validateShipping() {
      if (!empty($_POST['shipping_method'])) {
         $shipping = explode('.', $_POST['shipping_method']);
               
         if (!isset($shipping[0]) || !isset($shipping[1]) || !isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]])) {       
            $this->error['warning'] = $this->_('error_shipping');
         }
      } else {
         $this->error['warning'] = $this->_('error_shipping');
      }
      
      if (!$this->error) {
         return true;
      } else {
         return false;
      }     
   }
}