<?php
class Customer {
	private $registry;
	private $customer_id;
	private $information;
   private $payment_info;
	
  	public function __construct(&$registry) {
		$this->registry = &$registry;
				
		if (isset($this->session->data['customer_id'])) {
			$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$this->session->data['customer_id'] . "' AND status = '1'");
			
			if ($customer_query->num_rows) {
				$this->customer_id = $customer_query->row['customer_id'];
            
            $this->information = $customer_query->row;
            
            $this->payment_info = $this->get_setting('payment_info_' . $this->information['payment_code']);
            
            if(!$this->payment_info){
               $this->payment_info = array(
                  'address_id' => $this->information['address_id'],
                  'payment_code' => $this->information['payment_code'],
               );
            }
            
   			$this->db->query("UPDATE " . DB_PREFIX . "customer SET cart = '" . $this->db->escape(isset($this->session->data['cart']) ? serialize($this->session->data['cart']) : '') . "', wishlist = '" . $this->db->escape(isset($this->session->data['wishlist']) ? serialize($this->session->data['wishlist']) : '') . "', ip = '" . $this->db->escape($_SERVER['REMOTE_ADDR']) . "' WHERE customer_id = '" . (int)$this->customer_id . "'");
			
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_ip WHERE customer_id = '" . (int)$this->session->data['customer_id'] . "' AND ip = '" . $this->db->escape($_SERVER['REMOTE_ADDR']) . "'");
				
				if (!$query->num_rows) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "customer_ip SET customer_id = '" . (int)$this->session->data['customer_id'] . "', ip = '" . $this->db->escape($_SERVER['REMOTE_ADDR']) . "', date_added = NOW()");
				}
			} else {
				$this->logout();
			}
  		}
	}
   
	public function __get($key){
      return $this->registry->get($key);
   }
	
   public function set_default_payment_code($code){
      $this->db->query("UPDATE " . DB_PREFIX . "customer SET payment_code = '" . $this->db->escape($code) . "' WHERE customer_id = '$this->customer_id'");
      
      $this->payment_info = $this->get_setting('payment_info_' . $code);
      
      if(!$this->payment_info){
         $this->payment_info = array(
            'payment_code' => $code,
            'address_id' => $this->information['address_id'],
         );
      }
   }
   
   public function set_default_address_id($address_id){
      $this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '$this->customer_id'");
      
      $this->information['address_id'] = $address_id;
   }
   
   public function edit_setting($key, $value){
      if(!$this->customer_id){
         return false;
      }
      
      if(is_array($value) || is_object($value)){
         $value = serialize($value);
         $serialized = 1;
      }
      else{
         $serialized = 0;
      }
      
      $this->db->query("DELETE FROM " . DB_PREFIX . "customer_setting WHERE customer_id = '$this->customer_id' AND `key` = '" . $this->db->escape($key) . "'");
      
      $this->db->query("INSERT INTO " . DB_PREFIX . "customer_setting SET customer_id = '$this->customer_id', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape($value) . "', serialized = '$serialized'"); 
   }
   
   public function get_setting($key){
      $query = $this->db->query("SELECT value, serialized FROM " . DB_PREFIX . "customer_setting WHERE customer_id = '$this->customer_id' AND `key` = '" . $this->db->escape($key) . "' LIMIT 1");
      
      if($query->num_rows){
         if($query->row['serialized']){
            return unserialize($query->row['value']);
         }
            
         return $query->row['value'];
      }
      
      return null;
   }
   
   public function delete_setting($key){
      $this->db->query("DELETE FROM " . DB_PREFIX . "customer_setting WHERE customer_id = '$this->customer_id' AND `key` = '" . $this->db->escape($key) . "'");
   }
   
  	public function login($email, $password, $override = false) {
		if ($override) {
			$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer where LOWER(email) = '" . $this->db->escape(strtolower($email)) . "' AND status = '1'");
		} elseif (!$this->config->get('config_customer_approval')) {
			$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(strtolower($email)) . "' AND password = '" . $this->db->escape(md5($password)) . "' AND status = '1'");
		} else {
			$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(strtolower($email)) . "' AND password = '" . $this->db->escape(md5($password)) . "' AND status = '1' AND approved = '1'");
		}
		
		if ($customer_query->num_rows) {
			$this->session->data['customer_id'] = $customer_query->row['customer_id'];	
		    
			if ($customer_query->row['cart'] && is_string($customer_query->row['cart'])) {
				$cart = unserialize($customer_query->row['cart']);
				
				foreach ($cart as $key => $value) {
					if (!array_key_exists($key, $this->session->data['cart'])) {
						$this->session->data['cart'][$key] = $value;
					} else {
						$this->session->data['cart'][$key] += $value;
					}
				}
			}

			if ($customer_query->row['wishlist'] && is_string($customer_query->row['wishlist'])) {
				if (!isset($this->session->data['wishlist'])) {
					$this->session->data['wishlist'] = array();
				}
								
				$wishlist = unserialize($customer_query->row['wishlist']);
			
				foreach ($wishlist as $product_id) {
					if (!in_array($product_id, $this->session->data['wishlist'])) {
						$this->session->data['wishlist'][] = $product_id;
					}
				}			
			}
									
			$this->customer_id = $customer_query->row['customer_id'];
         
         $this->information = $customer_query->row;
         
         $this->payment_info = $this->get_setting('payment_info_' . $this->information['payment_code']);
            
         if(!$this->payment_info){
            $this->payment_info = array(
               'address_id' => $this->information['address_id'],
               'payment_code' => $this->information['payment_code'],
            );
         }
          	
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET ip = '" . $this->db->escape($_SERVER['REMOTE_ADDR']) . "' WHERE customer_id = '" . (int)$this->customer_id . "'");
			
	  		return true;
    	} else {
      		return false;
    	}
  	}
  	
	public function logout() {
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET cart = '" . $this->db->escape(isset($this->session->data['cart']) ? serialize($this->session->data['cart']) : '') . "', wishlist = '" . $this->db->escape(isset($this->session->data['wishlist']) ? serialize($this->session->data['wishlist']) : '') . "' WHERE customer_id = '" . (int)$this->customer_id . "'");
		
		unset($this->session->data['customer_id']);

		$this->customer_id = '';
      
		if($this->information){
	      foreach($this->information as &$i){
	         $i = '';
	      }
      }
		
		//TODO: REMOVE THIS
		$this->message->add('notify', "Logged Out");
  	}
  
  	public function isLogged() {
    	return $this->customer_id;
  	}

  	public function getId() {
    	return $this->customer_id;
  	}
   
   public function getInfo($key = null){
      if($key && isset($this->information[$key])){
         return $this->information[$key];
      }
      
      return $this->information;
   }
   
  	public function getFirstName() {
  	   if($this->information){
		   return $this->information['firstname'];
      }
      
      return '';
  	}
  
  	public function getLastName() {
  	   if($this->information){
		   return $this->information['lastname'];
      }
      
      return '';
  	}
	
	public function getFullName(){
		if($this->information){
			return $this->information['firstname'] . ' ' . $this->information['lastname'];
		}
		
		return '';
	}
	
  	public function getEmail() {
  	   if($this->information){
		   return $this->information['email'];
      }
      
      return '';
  	}
  
  	public function getTelephone() {
  	   if($this->information){
		   return $this->information['telephone'];
      }
      
      return '';
  	}
  
  	public function getFax() {
  	   if($this->information){
		   return $this->information['fax'];
      }
      
      return '';
  	}
	
  	public function getNewsletter() {
  	   if($this->information){
		   return $this->information['newsletter'];
      }
      
      return '';
  	}

  	public function getCustomerGroupId() {
  	   if($this->information){
		   return $this->information['customer_group_id'];
      }
      
      return '';
  	}
	
  	public function getAddressId() {
  	   if($this->information){
         return $this->information['address_id'];
      }
      
      return '';
  	}
   
   public function verifyDefaultAddress(){
      if(!$this->information) return false;
      
      $address_id = (int)$this->information['address_id'];
      
      if($address_id){
         $query = $this->db->query("SELECT COUNT(*) as total FROM " . DB_PREFIX . "address WHERE address_id = '$address_id' AND customer_id = '$this->customer_id' LIMIT 1");
      }
      
      if(!$address_id || !$query->row['total']){
         $query = $this->db->query("SELECT address_id FROM " . DB_PREFIX . "address WHERE customer_id = '$this->customer_id' LIMIT 1");
         
         if($query->num_rows){
            $address_id = $query->row['address_id'];
         }
         else{
            $address_id = 0;
         }
         
         $this->set_default_address_id($address_id);
         
         return $address_id;
      }
      
      return true;
   }
   
   public function verifyPaymentInfo(){
      if(!$this->payment_info || empty($this->payment_info['address_id'])) return false;
      
      $query = $this->db->query("SELECT COUNT(*) as total FROM " . DB_PREFIX . "address WHERE address_id = '" . $this->db->escape($this->payment_info['address_id']) . "' AND customer_id = '$this->customer_id'");
      
      if(!$query->row['total']){
         $this->delete_setting('payment_info_' . $this->payment_info['payment_code']);
         
         //if the payment info has not been set, we use our default shipping address for now
         $this->payment_info = $this->information['address_id'];
         
         return $address_id;
      }
      
      return true;
   }
   
   public function getPaymentInfo($key = null){
      if($key && isset($this->payment_info[$key])){
         return $this->payment_info[$key];
      }
      
      return $this->payment_info;
   }
	
  	public function getBalance() {
  	   if(!$this->customer_id) return 0;
      
		$query = $this->db->query("SELECT SUM(amount) AS total FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$this->customer_id . "'");
	
		return $query->row['total'];
  	}	

  	public function getRewardPoints() {
  	   if(!$this->customer_id) return 0;
  	   
		$query = $this->db->query("SELECT SUM(points) AS total FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$this->customer_id . "'");
	
		return $query->row['total'];	
  	}	
}