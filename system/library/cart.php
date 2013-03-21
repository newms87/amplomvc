<?php
class Cart {
   protected $registry;
	
	private $data = array();
	private $error = array();
   private $no_json = false;
   
  	public function __construct(&$registry) {
  	   $this->registry = &$registry;

      $this->language->load('system/cart');
      
		if (!isset($this->session->data['cart']) || !is_array($this->session->data['cart'])) {
      		$this->session->data['cart'] = array();
    	}
	}
   
   public function __get($key) {
      return $this->registry->get($key);
   }
	
   public function get_errors($type = null, $pop = false){
      if($type){
         if(isset($this->error[$type])){
            $e = $this->error[$type];
            if($pop){
               unset($this->error[$type]);
            }
            return $e;
         }
         else{
            return array();
         }
      }
      
      $e = $this->error;
      if($pop){
         $this->error = array();
      }
      return $e;
   }
   
   public function has_error($type){
      $type_list = explode('>', $type);
      
      $error = $this->error;
      foreach($type_list as $t){
         if(!isset($error[$t])){
            return false;
         }
         $error = $error[$t];
      }
      
      return true;
   }
   
   public function getProductId($key){
      if(!isset($this->session->data['cart'][$key])){
         return false;
      }
      
      $product = explode(':', $key);
      
      return $product[0];
      
   }
   
   public function getProductName($key){
      if(!isset($this->session->data['cart'][$key])){
         return '';
      }
      
      $product = explode(':', $key);
      
      $product_id = $product[0];
      
      return $this->model_catalog_product->getProductName($product_id);
   }
   
   public function getProducts() {
		if (!$this->data) {
			foreach ($this->session->data['cart'] as $key => $quantity) {
				$product = explode(':', $key);
            $product_id = $product[0];
				$stock = true;
	
				// Options
				if (isset($product[1])) {
					$options = unserialize(base64_decode($product[1]));
				} else {
					$options = array();
				}
				
				$product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.date_available <= NOW() AND p.status = '1'");
				
				if ($product_query->num_rows) {
				   $option_cost = 0;
					$option_price = 0;
					$option_points = 0;
					$option_weight = 0;
	
					$option_data = array();
	
					foreach ($options as $product_option_id => $option_value) {
						$option_query = $this->db->query("SELECT po.product_option_id, po.option_id, od.name, od.display_name, o.type FROM " . DB_PREFIX . "product_option po" .
						   " LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id)" .
						   " LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id)" . 
						   " WHERE po.product_option_id = '" . (int)$product_option_id . "' AND po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");
						
						if ($option_query->num_rows) {
							foreach ($option_value as $product_option_value) {
								$option_value_query = $this->db->query(
								   "SELECT pov.option_value_id, ovd.name, pov.* FROM " . DB_PREFIX . "product_option_value pov" . 
								   " LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id)" . 
								   " LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id)" .
								   " WHERE pov.product_option_value_id = '" . (int)$product_option_value['product_option_value_id'] . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
								
								if ($option_value_query->num_rows) {
									
                           $option_cost   += $option_value_query->row['cost'];
									$option_price  += $option_value_query->row['price'];
									$option_points += $option_value_query->row['points'];
									$option_weight += $option_value_query->row['weight'];
									
									if ($option_value_query->row['subtract'] && (!$option_value_query->row['quantity'] || ($option_value_query->row['quantity'] < $quantity))) {
										$stock = false;
									}
									
									$option_data[] = array(
										'product_option_id'       => $product_option_id,
										'product_option_value_id' => $product_option_value['product_option_value_id'],
										'option_id'               => $option_query->row['option_id'],
										'option_value_id'         => $option_value_query->row['option_value_id'],
										'name'                    => $option_query->row['name'],
										'display_name'            => $option_query->row['display_name'],
										'option_value'            => $option_value_query->row['name'],
										'type'                    => $option_query->row['type'],
										'quantity'                => $option_value_query->row['quantity'],
										'subtract'                => $option_value_query->row['subtract'],
										'cost'                    => $option_value_query->row['cost'],
										'price'                   => $option_value_query->row['price'],
										'points'                  => $option_value_query->row['points'],
										'weight'                  => $option_value_query->row['weight'],
									);								
								}
							}
						}
					}

					if ($this->customer->isLogged()) {
						$customer_group_id = $this->customer->getCustomerGroupId();
					} else {
						$customer_group_id = $this->config->get('config_customer_group_id');
					}
					
					$cost = $product_query->row['cost'];
					$price = $product_query->row['price'];
               
					
					// Product Discounts
					$discount_quantity = 0;
					
					foreach ($this->session->data['cart'] as $key_2 => $quantity_2) {
						$product_2 = explode(':', $key_2);
						
						if ($product_2[0] == $product_id) {
							$discount_quantity += $quantity_2;
						}
					}
					
					$product_discount_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$customer_group_id . "' AND quantity <= '" . (int)$discount_quantity . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY quantity DESC, priority ASC, price ASC LIMIT 1");
					
					if ($product_discount_query->num_rows) {
						$price = $product_discount_query->row['price'];
					}
					
					// Product Specials
					$product_special_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$customer_group_id . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1");
				
					if ($product_special_query->num_rows) {
						$price = $product_special_query->row['price'];
					}						
			
					// Reward Points
					$product_reward_query = $this->db->query("SELECT points FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$customer_group_id . "'");
					
					if ($product_reward_query->num_rows) {	
						$reward = $product_reward_query->row['points'];
					} else {
						$reward = 0;
					}
					
					// Downloads		
					$download_data = array();     		
					
					$download_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_download p2d LEFT JOIN " . DB_PREFIX . "download d ON (p2d.download_id = d.download_id) LEFT JOIN " . DB_PREFIX . "download_description dd ON (d.download_id = dd.download_id) WHERE p2d.product_id = '" . (int)$product_id . "' AND dd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
				
					foreach ($download_query->rows as $download) {
						$download_data[] = array(
							'download_id' => $download['download_id'],
							'name'        => $download['name'],
							'filename'    => $download['filename'],
							'mask'        => $download['mask'],
							'remaining'   => $download['remaining']
						);
					}
					
					// Stock
					if (!$product_query->row['quantity'] || ($product_query->row['quantity'] < $quantity)) {
						$stock = false;
					}
					
					$this->data[$key] = array(
						'key'             => $key,
						'product_id'      => $product_query->row['product_id'],
						'name'            => $product_query->row['name'],
						'model'           => $product_query->row['model'],
						'shipping'        => $product_query->row['shipping'],
						'image'           => $product_query->row['image'],
						'option'          => $option_data,
						'download'        => $download_data,
						'quantity'        => $quantity,
						'minimum'         => $product_query->row['minimum'],
						'subtract'        => $product_query->row['subtract'],
						'stock'           => $stock,
						'is_final'        => $product_query->row['is_final'],
						'cost'            => ($cost + $option_cost),
						'total_cost'      => ($cost + $option_cost) * $quantity,
						'price'           => ($price + $option_price),
						'total'           => ($price + $option_price) * $quantity,
						'reward'          => $reward * $quantity,
						'points'          => ($product_query->row['points'] ? ($product_query->row['points'] + $option_points) * $quantity : 0),
						'tax_class_id'    => $product_query->row['tax_class_id'],
						'weight'          => ($product_query->row['weight'] + $option_weight) * $quantity,
						'weight_class_id' => $product_query->row['weight_class_id'],
						'length'          => $product_query->row['length'],
						'width'           => $product_query->row['width'],
						'height'          => $product_query->row['height'],
						'length_class_id' => $product_query->row['length_class_id']					
					);
				} else {
					$this->remove($key);
				}
			}
		}

		return $this->data;
  	}
		  
  	public function add($product_id, $quantity = 1, $options = array(), $no_json = true) {
  	   $this->no_json = $no_json;
      
  	   $product_id = (int)$product_id;
      
      
      if($this->validateProduct($product_id, $quantity, $options)){
       	if (!$options) {
         		$key = $product_id;
       	} else {
         		$key = $product_id . ':' . base64_encode(serialize($options));
       	}
       	
         $quantity = (int)$quantity;
         
   		if ($quantity && $quantity > 0) {
       		if (!isset($this->session->data['cart'][$key])) {
         			$this->session->data['cart'][$key] = $quantity;
       		} else {
         			$this->session->data['cart'][$key] += $quantity;
       		}
   		}
   		
   		$this->data = array();
         return true;
      }
      else{
         return false;
      }
  	}

  	public function update($key, $qty) {
    	if ((int)$qty && ((int)$qty > 0)) {
      		$this->session->data['cart'][$key] = (int)$qty;
    	} else {
	  		$this->remove($key);
		}
		
		$this->data = array();
  	}

  	public function remove($key) {
		if (isset($this->session->data['cart'][$key])) {
     		unset($this->session->data['cart'][$key]);
  		}
		
		$this->data = array();
	}
	
  	public function clear() {
		$this->session->data['cart'] = array();
		$this->data = array();
  	}
	
  	public function getWeight() {
		$weight = 0;
	
    	foreach ($this->getProducts() as $product) {
			if ($product['shipping']) {
      			$weight += $this->weight->convert($product['weight'], $product['weight_class_id'], $this->config->get('config_weight_class_id'));
			}
		}
	
		return $weight;
	}
	
  	public function getSubTotal() {
		$total = 0;
		
		foreach ($this->getProducts() as $product) {
			$total += $product['total'];
		}

		return $total;
  	}
	
   public function getTotals(){
      $total_data = array();
      $total = 0;
      $taxes = $this->getTaxes();
      
      $sort_order = array(); 
      
      $results = $this->model_setting_extension->getExtensions('total');
      
      foreach ($results as $key => $value) {
         $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
      }
      
      array_multisort($sort_order, SORT_ASC, $results);
      
      foreach ($results as $result) {
         if ($this->config->get($result['code'] . '_status')) {
            $this->load->model('total/' . $result['code']);
   
            $this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
         }
         
         $sort_order = array();
        
         foreach ($total_data as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
         }

         array_multisort($sort_order, SORT_ASC, $total_data);        
      }

      $values = array(
         'data'       => $total_data,
         'total'      => $total,
         'taxes'      => $taxes
        );
        
      return $values;
   }
   
	public function getTaxes() {
		$tax_data = array();
		
		foreach ($this->getProducts() as $product) {
			if ($product['tax_class_id']) {
				$tax_rates = $this->tax->getRates($product['total'], $product['tax_class_id']);
				
				foreach ($tax_rates as $tax_rate) {
					$amount = 0;
					
					if ($tax_rate['type'] == 'F') {
						$amount = ($tax_rate['amount'] * $product['quantity']);
					} elseif ($tax_rate['type'] == 'P') {
						$amount = $tax_rate['amount'];
					}
					
					if (!isset($tax_data[$tax_rate['tax_rate_id']])) {
						$tax_data[$tax_rate['tax_rate_id']] = $amount;
					} else {
						$tax_data[$tax_rate['tax_rate_id']] += $amount;
					}
				}
			}
		}
		
		return $tax_data;
  	}

  	public function getTotal() {
		$total = 0;
		
		foreach ($this->getProducts() as $product) {
			$total += $this->tax->calculate($product['total'], $product['tax_class_id'], $this->config->get('config_show_price_with_tax'));
		}

		return $total;
  	}
	
   public function getTotalPoints(){
      $points_total = 0;
      
      $products = $this->getProducts();
      
      foreach ($products as $product) {
         $points_total += (int)$product['points'];
      }
      
      return $points_total;
   }
   
  	public function countProducts() {
		$product_total = 0;
			
		$products = $this->getProducts();
			
		foreach ($products as $product) {
			$product_total += $product['quantity'];
		}		
					
		return $product_total;
	}
	  
  	public function hasProducts() {
    	return count($this->session->data['cart']);
  	}
   
   public function isEmpty(){
      return !(count($this->session->data['cart']) || !empty($this->session->data['vouchers']));
   }
  
  	public function hasStock() {
  	   $this->error['cart']['stock'] = array();
      
		foreach ($this->getProducts() as $product) {
			if (!$product['stock']) {
			   $this->error['cart']['stock'][] = $this->language->format('error_cart_stock', $this->url->link('product/product','product_id=' . $product['product_id']), $product['name']);
			}
		}
		
    	return $this->error['cart']['stock'] ? false : true;
  	}
  
  	public function hasShipping() {
		foreach ($this->getProducts() as $product) {
	  		if ($product['shipping']) {
	    		return true;
	  		}		
		}
		
		return false;
	}
	
  	public function hasDownload() {
		foreach ($this->getProducts() as $product) {
	  		if ($product['download']) {
	    		return true;
	  		}		
		}
		
		return false;
	}
   
   public function getPaymentMethods(){
      if ($this->customer->isLogged() && isset($this->session->data['payment_address_id'])) {
         $payment_address = $this->model_account_address->getAddress($this->session->data['payment_address_id']);    
      } elseif (isset($this->session->data['guest']['payment_address'])) {
         $payment_address = $this->session->data['guest']['payment_address'];
      }
      else{
         $payment_address = 0;
      }
      
      $totals = $this->cart->getTotals();
      
      // Payment Methods
      $method_data = array();
      
      $results = $this->model_setting_extension->getExtensions('payment');

      foreach ($results as $result) {
         if ($this->config->get($result['code'] . '_status')) {
            
            $this->load->model('payment/' . $result['code']);
            $method = $this->{'model_payment_' . $result['code']}->getMethod($payment_address, $totals['total']); 
            
            if ($method) {
               $method_data[$result['code']] = $method;
            }
         }
      }
      
      if(!$method_data){
         $this->error['checkout']['payment_method'] = $this->language->format('error_payment_methods', $this->config->get('config_email'));
         return false;
      }
      
      $sort_order = array(); 
     
      foreach ($method_data as $key => $value) {
         $sort_order[$key] = $value['sort_order'];
      }

      array_multisort($sort_order, SORT_ASC, $method_data);       
      
      return $method_data;
   }
   
   public function verifyPaymentMethod(){
      if ($this->customer->isLogged() && isset($this->session->data['payment_address_id'])) {
         $payment_address = $this->model_account_address->getAddress($this->session->data['payment_address_id']);    
      } elseif (isset($this->session->data['guest']['payment_address'])) {
         $payment_address = $this->session->data['guest']['payment_address'];
      }
      else{
         $this->error['checkout']['payment_address'] = $this->language->get('error_payment_address');
         return false;
      }
      
      if(!isset($this->session->data['payment_method'])){
         $this->error['checkout']['payment_method'] = $this->language->get('error_payment_method');
         return false;
      }
      
      $code = $this->session->data['payment_method']['code'];
      
      if ($this->config->get($code . '_status')) {
         $totals = $this->cart->getTotals();
      
         $this->load->model('payment/' . $code);
         $method = $this->{'model_payment_' . $code}->getMethod($payment_address, $totals['total']); 
         
         if (!$method) {
            $this->error['checkout']['payment_method'] = $this->language->get('error_payment_method_not_available');
            return false;
         }
         else{
            return $method;
         }
      }
      else{
         $this->error['checkout']['payment_method'] = $this->language->get('error_payment_method_status');
         return false;
      }
   }
   
   public function getShippingMethods(){
      if ($this->customer->isLogged() && isset($this->session->data['shipping_address_id'])) {              
         $shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);     
      } elseif (isset($this->session->data['guest']['shipping_address'])) {
         $shipping_address = $this->session->data['guest']['shipping_address'];
      }
      else{
         $this->error['checkout']['shipping_address'] = $this->language->get('error_shipping_address');
         return false;
      }
      
      $methods = array();
      
      $results = $this->model_setting_extension->getExtensions('shipping');
      
      foreach ($results as $result) {
         if ($this->config->get($result['code'] . '_status')) {
            $this->load->model('shipping/' . $result['code']);
            
            $method = $this->{'model_shipping_' . $result['code']}->getQuote($shipping_address); 
   
            if ($method) {
               $methods[$result['code']] = $method;
            }
         }
      }
      
      if($methods){
         $sort_order = array();
        
         foreach ($methods as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
         }
   
         array_multisort($sort_order, SORT_ASC, $methods);
         
         return $methods;
      }
      else{
         $this->error['checkout']['shipping_methods'] = $this->language->get('error_shipping_methods');
         return false;
      }
   }
   
   public function updateShippingQuote(){
      if ($this->customer->isLogged() && isset($this->session->data['shipping_address_id'])) {              
         $shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);     
      } elseif (isset($this->session->data['guest']['shipping_address'])) {
         $shipping_address = $this->session->data['guest']['shipping_address'];
      }
      else{
         $this->error['checkout']['shipping_address'] = $this->language->get('error_shipping_address');
         return false;
      }
      
      if(!isset($this->session->data['shipping_method'])){
         $this->error['checkout']['shipping_method'] = $this->language->get('error_shipping_method');
         return false;
      }
      
      $code = $this->session->data['shipping_method']['code'];
      
      $method_code = preg_replace("/\..*$/",'', $code);
      
      $quote_code = preg_replace("/^.*\./",'', $code); 
      
      if ($this->config->get($method_code . '_status')) {
         $this->load->model('shipping/' . $method_code);
         
         $quotes = $this->{'model_shipping_' . $method_code}->getQuote($shipping_address);
         
         if(!$quotes || !isset($quotes['quote'][$quote_code])){
            unset($this->session->data['shipping_method']);
            
            $this->error['checkout']['shipping_method'] = $this->language->get('error_shipping_method_not_available');
            return false;
         }
         else{
            $this->session->data['shipping_method'] = $quotes['quote'][$quote_code];
            
            return $quotes['quote'][$quote_code];
         }
      }
      else{
         $this->error['checkout']['shipping_method'] = $this->language->get('error_shipping_method_status');
         return false;
      }
   }
   
   public function addOrder(){
      if(!$this->validate()){
         return false;
      }
      
      $data = array();
      
      //Customer Checkout
      if($this->customer->isLogged()){
         //validate customer shipping address if necessary
         if($this->cart->hasShipping()){
            if(!isset($this->session->data['shipping_address_id'])){
               $this->error['checkout']['shipping_address'] = $this->language->get('error_shipping_address');
               return false;
            }
         }
         
         //validate customer payment address
         if(!isset($this->session->data['payment_address_id'])){
            $this->error['checkout']['payment_address'] = $this->language->get('error_payment_address');
            return false;
         }
         
         
         $data                     = $this->customer->getInfo();
         $data['payment_address']  = $this->model_account_address->getAddress($this->session->data['payment_address_id']);
         $data['shipping_address'] = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);
      }
      elseif($this->config->get('config_guest_checkout') && isset($this->session->data['guest'])){
         //validate Guest shipping address if necessary
         if($this->cart->hasShipping()){
            if(!isset($this->session->data['guest']['shipping_address'])){
               $this->error['checkout']['shipping_address'] = $this->language->get('error_shipping_address');
               return false;
            }
         }
         
         //validate Guest Payment address
         if(!isset($this->session->data['guest']['payment_address'])){
            $this->error['checkout']['payment_address'] = $this->language->get('error_payment_address');
            return false;
         }
         
         //build rest of Guest Checkout Profile
         $data['customer_id'] = 0;
         $data['customer_group_id'] = $this->config->get('config_customer_group_id');
         $data += $this->session->data['guest'];
      }
      //Guest checkout no allowed and customer not logged in
      else{
         $this->error['checkout']['guest'] = $this->language->get('error_checkout_guest');
         return false;
      }
      
      if(isset($this->session->data['payment_method'])){
         $data['payment_method'] = $this->session->data['payment_method']['title'];
         $data['payment_code'] = $this->session->data['payment_method']['code'];
      }
      else{
         $this->error['checkout']['payment_method'] = $this->language->get('error_payment_method');
         return false;
      }
      
      if ($this->hasShipping()) {
         if(isset($this->session->data['shipping_method'])){
            $data['shipping_method'] = $this->session->data['shipping_method']['title'];
            $data['shipping_code'] = $this->session->data['shipping_method']['code'];
         }
         else{
            $this->error['checkout']['shipping_method'] = $this->language->get('error_shipping_method');
            return false;
         }
      }
      
      $data['invoice_prefix'] = $this->tool->format_invoice($this->config->get('config_invoice_prefix'));
      $data['store_id'] = $this->config->get('config_store_id');
      $data['store_name'] = $this->config->get('config_name');
      
      if ($data['store_id']) {
         $data['store_url'] = $this->config->get('config_url');      
      } else {
         $data['store_url'] = HTTP_SERVER;
      }
      
      $totals = $this->getTotals();
      
      $data['total'] = $totals['total'];
      $data['totals'] = $totals['data'];
      
      $product_data = $this->getProducts();
      
      foreach ($product_data as &$product) {
         //TODO - remove this if we never use files?!?
         /*
         foreach ($product['option'] as &$option) {
            if ($option['type'] == 'file') {
               $option['value'] = $this->encryption->decrypt($option['option_value']);
            }              
         }
         unset($option);
         */
         
         $product['tax'] = $this->tax->getTax($product['total'], $product['tax_class_id']);
      }
      unset($product);
      
      // Gift Voucher
      $voucher_data = array();
      
      if (!empty($this->session->data['vouchers'])) {
         $voucher_data = $this->session->data['vouchers']; 
      
         foreach ($voucher_data as &$voucher) {
            $voucher['code'] = substr(md5(rand()), 0, 7);
         }
      }
               
      $data['products'] = $product_data;
      $data['vouchers'] = $voucher_data;
      $data['comment'] = $this->session->data['comment'];
      
      
      $data['affiliate_id'] = 0;
      $data['commission'] = 0;
      
      if (isset($_COOKIE['tracking'])) {
         $affiliate_info = $this->model_affiliate_affiliate->getAffiliateByCode($_COOKIE['tracking']);
         
         if ($affiliate_info) {
            $data['affiliate_id'] = $affiliate_info['affiliate_id']; 
            $data['commission'] = ($total / 100) * $affiliate_info['commission']; 
         }
      }
      
      $data['language_id'] = $this->config->get('config_language_id');
      $data['currency_id'] = $this->currency->getId();
      $data['currency_code'] = $this->currency->getCode();
      $data['currency_value'] = $this->currency->getValue($this->currency->getCode());
      $data['ip'] = $_SERVER['REMOTE_ADDR'];
      
      if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
         $data['forwarded_ip'] = $_SERVER['HTTP_X_FORWARDED_FOR']; 
      } elseif(!empty($_SERVER['HTTP_CLIENT_IP'])) {
         $data['forwarded_ip'] = $_SERVER['HTTP_CLIENT_IP']; 
      }
      
      if (isset($_SERVER['HTTP_USER_AGENT'])) {
         $data['user_agent'] = $_SERVER['HTTP_USER_AGENT'];  
      }
      
      if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
         $data['accept_language'] = $_SERVER['HTTP_ACCEPT_LANGUAGE']; 
      }
      
      foreach($data['payment_address'] as $key => $pa){
         $data['payment_' . $key] = $pa;
      }
      
      foreach($data['shipping_address'] as $key => $pa){
         $data['shipping_' . $key] = $pa;
      }
      
      $order_id = $this->model_checkout_order->addOrder($data);
      
      $this->session->data['order_id'] = $order_id;
      
      return $order_id;
   }
   
   public function validateProduct($product_id, $quantity, $options){
      $product_info = $this->model_catalog_product->getProduct($product_id);
      
      if ($product_info) {
         $product_options = $this->model_catalog_product->getProductOptions($product_id);
         
         $restrictions = $this->model_catalog_product->getProductOptionValueRestrictions($product_id);
         
         foreach ($product_options as $product_option) {
            if (!empty($product_option['product_option_value']) && $product_option['required']) {
               if(empty($options[$product_option['product_option_id']])){
                  $this->error['add']['option'][$product_option['product_option_id']] = $this->language->format('error_required', $product_option['display_name']);
                  return false;
               }
               elseif($product_option['group_type'] == 'single' && count($options[$product_option['product_option_id']]) > 1){
                  $this->error['add']['option'][$product_option['product_option_id']] = $this->language->format('error_selected_multi', $product_option['display_name']);
                  return false;
               }
            }
         }
         
         foreach($options as $po_id => $selected_po){
            foreach($selected_po as $selected_pov){
               if (isset($selected_pov['option_value_id']) && isset($restrictions[$selected_pov['option_value_id']])){
                  foreach($options as $selected_po2){
                     foreach($selected_po2 as $selected_pov2){
                        if(in_array($selected_pov2['option_value_id'], $restrictions[$selected_pov['option_value_id']])){
                           $this->error['add']['option'][$po_id] = $this->language->get('error_pov_restriction');
                           return false;
                        }
                     }
                  }
               }
            }
         }
      }
      else{
         $this->error['add']['product_id'] = $this->language->get('error_invalid_product_id');
         return false;
      }

      return true;
   }

   public function validateMinimumQuantity(){
      $product_total = 0;
      
      $products = $this->getProducts();
      
      foreach($products as $product){
         foreach ($products as $product_2) {
            if ($product_2['product_id'] == $product['product_id']) {
               $product_total += $product_2['quantity'];
            }
         }
         
         if ($product_total < $product['minimum']) {
            $this->error['cart']['minimum'][$product['product_id']] = $this->language->format('error_product_minimum', $product['name'], $product['minimum']);
            return false;
         }
      }
      
      return true;
   }
   
   public function validate(){
      if($this->isEmpty()){
         $this->error['cart']['empty'] = $this->language->get('error_cart_empty');
         return false;
      }
      
      if(!$this->hasStock() && !$this->config->get('config_stock_checkout')){
         return false;
      }
      
      if(!$this->validateMinimumQuantity()){
         return false;
      }
      
      return true;
   }
}