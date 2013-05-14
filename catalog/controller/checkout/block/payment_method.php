<?php  
class ControllerCheckoutBlockPaymentMethod extends Controller {
  	public function index() {
      $this->template->load('checkout/block/payment_method');

		$this->language->load('checkout/checkout');
		
		$payment_methods = $this->cart->getPaymentMethods();
      
      if ($payment_methods) {
         $this->session->data['payment_methods'] = $payment_methods;
		   $this->data['payment_methods'] = $payment_methods;
		} else {
		   $this->data['payment_methods'] = array();
		   $this->error['warning'] = $this->language->format('error_no_payment', $this->url->link('information/contact'));
		}	
	   
      $this->data['guest_checkout'] = !$this->customer->isLogged();
      
      if (isset($this->session->data['payment_method']['code'])) {
			$this->data['code'] = $this->session->data['payment_method']['code'];
		} else {
			if(count($this->data['payment_methods']) == 1){
            $key = key($this->data['payment_methods']);
            $this->data['code'] = $this->data['payment_methods'][$key]['code'];
         }
         else{
            $this->data['code'] = '';
         }
		}
		
		if (isset($this->session->data['comment'])) {
			$this->data['comment'] = $this->session->data['comment'];
		} else {
			$this->data['comment'] = '';
		}
		
		if ($this->config->get('config_checkout_id')) {
			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_checkout_id'));
			
			if ($information_info) {
				$this->language->format('text_agree', $this->url->link('information/information/info', 'information_id=' . $this->config->get('config_checkout_id')), $information_info['title'], $information_info['title']);
			} else {
				$this->language->set('text_agree', '');
			}
		} else {
			$this->language->set('text_agree', '');
		}
		
		if (isset($this->session->data['agree'])) {
			$this->data['agree'] = $this->session->data['agree'];
		} else {
			$this->data['agree'] = '';
		}
      
		$this->data['validate_payment_method'] = $this->url->link('checkout/block/payment_method/validate');
		
		$this->response->setOutput($this->render());
  	}
	
	public function validate() {
		$this->language->load('checkout/checkout');
		
		$json = array();
		
		// Validate if payment address has been set.
		if ($this->customer->isLogged() && isset($this->session->data['payment_address_id'])) {
			$payment_address = $this->model_account_address->getAddress($this->session->data['payment_address_id']);		
		} elseif (isset($this->session->data['guest']['payment_address'])) {
			$payment_address = $this->session->data['guest']['payment_address'];
		}	
      else{
         $json['error']['payment_address'] = $this->_('error_payment_address');
      }
      
      if(!$this->cart->validate()){
         $json['redirect'] = $this->url->link('cart/cart');
         $this->message->add($this->cart->get_errors());
      }
		
		if (!$json) {
			if (!isset($_POST['payment_method'])) {
				$json['error']['warning'] = $this->_('error_payment');
			} else {
			   if(!isset($this->session->data['payment_methods'])){
			      $this->session->data['payment_methods'] = $this->cart->getPaymentMethods();
            }
            
				if (!isset($this->session->data['payment_methods'][$_POST['payment_method']])) {
					$json['error']['warning'] = $this->_('error_payment');
				}
			}
							
			if ($this->config->get('config_checkout_id')) {
				$information_info = $this->model_catalog_information->getInformation($this->config->get('config_checkout_id'));
				
				if ($information_info && !isset($_POST['agree'])) {
					$json['error']['warning'] = sprintf($this->_('error_agree'), $information_info['title']);
				}
			}
			
			if (!$json) {
				$this->session->data['payment_method'] = $this->session->data['payment_methods'][$_POST['payment_method']];
			  
            if($this->customer->isLogged()){
               $payment_info = array(
                  'address_id' => $this->session->data['payment_address_id'],
               );
               
               $code = $_POST['payment_method'];
               
               $this->customer->edit_setting('payment_info_' . $code, $payment_info);
               
               $this->customer->set_default_payment_code($code);
            }
            
				$this->session->data['comment'] = strip_tags($_POST['comment']);
			}							
		}
		
		$this->response->setOutput(json_encode($json));
	}
}