<?php
class Cart {
	protected $registry;
	
	private $data = array();
	private $error = array();
	private $error_code = null;
	
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
	
	public function _e($code, $type, $key){
		$this->error[$type] = $this->language->get($key);
		$this->error_code = $code;
	}
	
	public function get_error_code(){
		return $this->error_code;
	}
	
	public function get_errors($type = null, $pop = false, $name_format = false){
		//Get Specific Error
		if($type){
			if(isset($this->error[$type])){
				$e = $this->error[$type];
				
				if($pop){
					unset($this->error[$type]);
				}
			}
			else{
				return array();
			}
		}
		//Get All Errors
		else{
			$e = $this->error;
			
			if($pop){
				$this->error = array();
			}
		}
		
		if($name_format){
			return $this->tool->name_format($name_format, $e);
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
									
									$option_cost	+= $option_value_query->row['cost'];
									$option_price  += $option_value_query->row['price'];
									$option_points += $option_value_query->row['points'];
									$option_weight += $option_value_query->row['weight'];
									
									if ($option_value_query->row['subtract'] && (!$option_value_query->row['quantity'] || ($option_value_query->row['quantity'] < $quantity))) {
										$stock = false;
									}
									
									$option_data[] = array(
										'product_option_id'	=> $product_option_id,
										'product_option_value_id' => $product_option_value['product_option_value_id'],
										'option_id'					=> $option_query->row['option_id'],
										'option_value_id'			=> $option_value_query->row['option_value_id'],
										'name'						=> $option_query->row['name'],
										'display_name'				=> $option_query->row['display_name'],
										'option_value'				=> $option_value_query->row['name'],
										'type'						=> $option_query->row['type'],
										'quantity'				=> $option_value_query->row['quantity'],
										'subtract'				=> $option_value_query->row['subtract'],
										'cost'						=> $option_value_query->row['cost'],
										'price'					=> $option_value_query->row['price'],
										'points'						=> $option_value_query->row['points'],
										'weight'						=> $option_value_query->row['weight'],
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
							'name'		=> $download['name'],
							'filename'=> $download['filename'],
							'mask'		=> $download['mask'],
							'remaining'	=> $download['remaining']
						);
					}
					
					// Stock
					if (!$product_query->row['quantity'] || ($product_query->row['quantity'] < $quantity)) {
						$stock = false;
					}
					
					$this->data[$key] = array(
						'key'			=> $key,
						'product_id'		=> $product_query->row['product_id'],
						'name'				=> $product_query->row['name'],
						'model'			=> $product_query->row['model'],
						'shipping'		=> $product_query->row['shipping'],
						'image'			=> $product_query->row['image'],
						'option'		=> $option_data,
						'download'		=> $download_data,
						'quantity'		=> $quantity,
						'minimum'			=> $product_query->row['minimum'],
						'subtract'		=> $product_query->row['subtract'],
						'stock'			=> $stock,
						'is_final'		=> $product_query->row['is_final'],
						'cost'				=> ($cost + $option_cost),
						'total_cost'		=> ($cost + $option_cost) * $quantity,
						'price'			=> ($price + $option_price),
						'total'			=> ($price + $option_price) * $quantity,
						'reward'		=> $reward * $quantity,
						'points'		=> ($product_query->row['points'] ? ($product_query->row['points'] + $option_points) * $quantity : 0),
						'tax_class_id'=> $product_query->row['tax_class_id'],
						'weight'		=> ($product_query->row['weight'] + $option_weight) * $quantity,
						'weight_class_id' => $product_query->row['weight_class_id'],
						'length'		=> $product_query->row['length'],
						'width'			=> $product_query->row['width'],
						'height'		=> $product_query->row['height'],
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
	
				$this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
			}
			
			$sort_order = array();
		
			foreach ($total_data as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $total_data);		
		}

		$values = array(
			'data'	=> $total_data,
			'total'		=> $total,
			'taxes'		=> $taxes
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
  		foreach ($this->getProducts() as $product) {
			if (!$product['stock']) {
				$this->_e('C-2', 'cart', $this->language->format('error_cart_stock', $this->url->link('product/product','product_id=' . $product['product_id']), $product['name']));
				return false;
			}
		}
		
		return true;
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
	
	public function hasPaymentAddress(){
		return !empty($this->session->data['payment_address_id']);
	}
	
	public function hasShippingAddress(){
		return !empty($this->session->data['shipping_address_id']);
	}

	public function hasPaymentMethod(){
		return !empty($this->session->data['payment_method']);
	}
	
	public function hasShippingMethod(){
		return !empty($this->session->data['shipping_method']);
	}
	
	public function getPaymentAddressId(){
		return isset($this->session->data['payment_address_id']) ? $this->session->data['payment_address_id'] : false;
	}
	
	public function getShippingAddressId(){
		return isset($this->session->data['shipping_address_id']) ? $this->session->data['shipping_address_id'] : false;
	}
	
	public function getPaymentAddress(){
		if(isset($this->session->data['payment_address_id'])){
			return $this->model_account_address->getAddress($this->session->data['payment_address_id']);
		}

		return false;
	}
	
	public function getShippingAddress(){
		if(isset($this->session->data['shipping_address_id'])){
			return $this->model_account_address->getAddress($this->session->data['shipping_address_id']);
		}

		return false;
	}
	
	public function getPaymentMethod(){
		return isset($this->session->data['payment_method']) ? $this->session->data['payment_method'] : false;
	}
	
	public function getPaymentMethodId(){
		return isset($this->session->data['payment_method']) ? $this->session->data['payment_method']['code'] : false;
	}
	
	public function getShippingMethod(){
		if(isset($this->session->data['shipping_method'])){
			$method = $this->session->data['shipping_method'];
			
			$quotes = $this->{'model_shipping_' . $method['code']}->getQuote($this->getShippingAddress());
			
			if(!empty($quotes)){
				foreach($quotes as $quote){
					if($quote['method'] == $method['method']){
						return $method;
					}
				}
			}
		}
		
		unset($this->session->data['shipping_method']);

		return false;
	}
	
	public function getShippingMethodId(){
		if(isset($this->session->data['shipping_method'])){
			return $this->session->data['shipping_method']['code'] . '_' . $this->session->data['shipping_method']['method'];
		}
		
		return false;
	}
	
	public function setPaymentAddress($address = null){
		if(empty($address)){
			unset($this->session->data['payment_address_id']);
			$this->setPaymentMethod();
			return true;
		}
		elseif(is_array($address)) {
			$address_id = $this->model_account_address->addAddress($address);
			
			if(!$address_id){
				$this->_e('PA-10', 'payment_address', 'error_payment_address_details');
				return false;
			}
		}
		else{
			$address_id = (int)$address;
		}
		
		if(!empty($address_id)){
			$this->session->data['payment_address_id'] = $address_id;
		}
		
		if(!$this->validatePaymentAddress()){
			$this->_e('SA-11', 'payment_address', 'error_payment_address_invalid');
			unset($this->session->data['payment_address_id']);
			return false;
		}
		
		$this->setPaymentMethod();
		
		return true;
	}
	
	public function setShippingAddress($address = null){
		if(empty($address)){
			unset($this->session->data['shipping_address_id']);
			$this->setShippingMethod();
			return true;
		}
		elseif(is_array($address)) {
			$address_id = $this->model_account_address->addAddress($address);
			
			if(!$address_id){
				$this->_e('SA-10', 'shipping_address', 'error_shipping_address_details');
				return false;
			}
		}
		else{
			$address_id = (int)$address;
		}
		
		if(!empty($address_id)){
			$this->session->data['shipping_address_id'] = $address_id;
		}
		
		if(!$this->validateShippingAddress()){
			$this->_e('SA-11', 'shipping_address', 'error_shipping_address_invalid');
			unset($this->session->data['shipping_address_id']);
			return false;
		}
		
		$this->setShippingMethod();
		
		return true;
	}
	
	public function setPaymentMethod($method = null){
		if(!$method){
			unset($this->session->data['payment_method']);
		}
		else{
			$payment_methods = $this->getPaymentMethods();
			
			if(is_string($method)){
				if(!isset($payment_methods[$method])){
					$this->_e('PM-1a', 'payment_method', 'error_payment_method');
					return false;
				}
				
				$method = $payment_methods[$method];
			}
			else{
				$key = $method['code'];
				
				if(!isset($payment_methods[$key])){
					$this->_e('PM-1b', 'payment_method', 'error_payment_method');
					return false;
				}
			}
			
			$this->session->data['payment_method'] = $method;
		}
		
		return true;
	}
	
	public function setShippingMethod($method = null){
		if(!$method){
			unset($this->session->data['shipping_method']);
		}
		else{
			$shipping_methods = $this->getShippingMethods();
			
			if(is_string($method)){
				if(!isset($shipping_methods[$method])){
					$this->_e('SM-1a', 'shipping_method', 'error_shipping_method');
					return false;
				}
				
				$method =  $shipping_methods[$method];
			}
			else{
				$key = $method['code'] . '_' . $method['method'];
				
				if(!isset($shipping_methods[$key])){
					$this->_e('SM-1b', 'shipping_method', 'error_shipping_method');
					return false;
				}
			}
			
			$this->session->data['shipping_method'] = $method;
		}
		
		return true;
	}
	
	public function getPaymentMethods($address = null){
		if(!empty($address)){
			if(is_array($address)){
				$payment_address = $address;
			}
			else{
				$payment_address = $this->model_account_address->getAddress($address);
			}
		}elseif(isset($this->session->data['payment_address_id'])) {
			$payment_address = $this->model_account_address->getAddress($this->session->data['payment_address_id']);
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
		
		uasort($method_data, function($a,$b){ return $a['sort_order'] > $b['sort_order']; });
		
		return $method_data;
	}
	
	public function getShippingMethods($address = null){
		if(!empty($address)){
			if(is_array($address)){
				$shipping_address = $address;
			}
			else{
				$shipping_address = $this->model_account_address->getAddress($address);
			}
		}
		elseif ($this->hasShippingAddress()) {				
			$shipping_address = $this->getShippingAddress();
		}
		else{
			$this->_e('SM-2', 'shipping_method', 'error_shipping_address');
			return false;
		}
		
		if(!$this->isAllowedShippingZone($shipping_address)){
			$this->_e('SM-3', 'shipping_method', 'error_shipping_zone');
			return false;
		}
		
		//Find Available Shipping Methods
		$results = $this->model_setting_extension->getExtensions('shipping');
		
		$methods = array();
		
		foreach ($results as $result) {
			
			if ($this->config->get($result['code'] . '_status')) {
				$quotes = $this->{'model_shipping_' . $result['code']}->getQuote($shipping_address); 
				
				if(empty($quotes)) continue;
				
				foreach($quotes as $quote){
					$methods[$quote['code'] . '_' . $quote['method']] = $quote;
				}
			}
		}
		
		if($methods){
			uasort($methods, function($a,$b){ return $a['sort_order'] > $b['sort_order']; });
			
			return $methods;
		}
		
		
		//No Shipping Options Available!
		$msg = $this->language->format('error_shipping_methods', $this->url->link('information/contact'));
		$this->_e('SM-4', 'shipping_method', $msg);
		
		if($this->hasShippingAddress()){
			$this->message->add('error', $msg);
		}
		
		return false;
	}

	public function validateShippingDetails(){
		if($this->hasShipping()){
			if(!$this->validateShippingAddress()){
				$this->_e('CO-10', 'checkout', 'error_shipping_address');
				return false;
			}
			
			if($this->hasShippingMethod()){
				$shipping_method = $this->getShippingMethod();
			}
			
			if(empty($shipping_method)){
				$this->_e('CO-11', 'checkout', 'error_shipping_method');
				return false;
			}
		}
		
		return true;
	}

	public function validatePaymentDetails(){
		if(!$this->validatePaymentAddress()){
			$this->_e('CO-12', 'checkout', 'error_payment_address');
			return false;
		}
		
		if($this->hasPaymentMethod()){
			$payment_method = $this->getPaymentMethod();
		}
		
		if(empty($payment_method)){
			$this->_e('CO-13', 'checkout', 'error_payment_method');
			return false;
		}
		
		return true;
	}
	
	public function isAllowedShippingZone($shipping_address){
		if(!empty($shipping_address['country_id']) && !empty($shipping_address['zone_id'])){
			return $this->model_localisation_zone->inGeoZone($this->config->get('config_allowed_shipping_zone'), $shipping_address['country_id'], $shipping_address['zone_id']);
		}
		
		return false;
	}
	
	public function getAllowedShippingZones(){
		$geo_zone_id = $this->config->get('config_allowed_shipping_zone');
		
		if($geo_zone_id > 0){
			$allowed_geo_zones = $this->cache->get('zone.allowed.' . $geo_zone_id);
			
			if(!$allowed_geo_zones){
				$allowed_geo_zones = array();
				
				$zones = $this->model_localisation_zone->getZonesByGeoZone($geo_zone_id);
				
				foreach($zones as $zone){
					$country = $this->model_localisation_country->getCountry($zone['country_id']);
					
					$allowed_geo_zones[] = array(
						'country' => $country,
						'zone'=> $zone
					);
				}
				
				$this->cache->set('zone.allowed.' . $geo_zone_id, $allowed_geo_zones);
			}
		}
		else{
			return array();
		}
		
		return $allowed_geo_zones;
	}

	public function validatePaymentAddress($address = null){
		unset($this->error['payment_address']);
		
		if(empty($address)){
			if($this->hasPaymentAddress()){
				$address = $this->getPaymentAddress();
			}
			else{
				$this->_e('PA-1', 'payment_address', 'error_payment_address');
				return false;
			}
		}
		
		$country_id = !empty($address['country_id']) ? (int)$address['country_id'] : 0;
		$zone_id = !empty($address['zone_id']) ? (int)$address['zone_id'] : 0;
		
		if( ! $this->db->query_var("SELECT COUNT(*) as total FROM " . DB_PREFIX . "country WHERE country_id = '$country_id'")){
			$this->_e('PA-2', 'payment_address', 'error_country_id');
			return false;
		}
		
		if( ! $this->db->query_var("SELECT COUNT(*) as total FROM " . DB_PREFIX . "zone WHERE zone_id = '$zone_id' AND country_id = '$country_id'")){
			$this->_e('PA-3', 'payment_address', 'error_zone_id');
			return false;
		}
		
		return true;
	}
	
	public function validateShippingAddress($address = null){
		unset($this->error['shipping_address']);
		
		if(empty($address)){
			if($this->hasShippingAddress()){
				$address = $this->getShippingAddress();
			}
			else{
				$this->_e('SA-1', 'shipping_address', 'error_shipping_address');
				return false;
			}
		}
		
		$country_id = !empty($address['country_id']) ? (int)$address['country_id'] : 0;
		$zone_id = !empty($address['zone_id']) ? (int)$address['zone_id'] : 0;
		
		if( ! $this->db->query_var("SELECT COUNT(*) as total FROM " . DB_PREFIX . "country WHERE country_id = '$country_id'")){
			$this->_e('SA-2', 'shipping_address', 'error_country_id');
			return false;
		}
		
		if( ! $this->db->query_var("SELECT COUNT(*) as total FROM " . DB_PREFIX . "zone WHERE zone_id = '$zone_id' AND country_id = '$country_id'")){
			$this->_e('SA-3', 'shipping_address', 'error_zone_id');
			return false;
		}
		
		if(!$this->isAllowedShippingZone($address)){
			$this->_e('SA-4', 'shipping_address', 'error_shipping_geo_zone');
			return false;
		}
		
		return true;
	}
	
	public function saveGuestInfo($info){
		$this->session->data['guest_info'] = $info;
	}
	
	public function loadGuestInfo(){
		return isset($this->session->data['guest_info']) ? $this->session->data['guest_info'] : null;
	}
		
	public function addOrder(){
		if(!$this->validate()){
			return false;
		}
		
		$data = array();
		
		//Validate Shipping Address & Method
		if($this->cart->hasShipping()){
			if(!$this->cart->hasShippingAddress()){
				$this->_e('CO-1', 'checkout', 'error_shipping_address');
				return false;
			}
			
			if(!$this->cart->hasShippingMethod()){
				$this->_e('CO-2', 'checkout', 'error_shipping_method');
			}
		}
		
		//Validate Payment Address & Method
		if(!$this->cart->hasPaymentAddress()){
			$this->_e('CO-3', 'checkout', 'error_payment_address');
			return false;
		}
		
		if(!$this->cart->hasPaymentMethod()){
			$this->_e('CO-3', 'checkout', 'error_payment_method');
			return false;
		}
		
		//Customer Checkout
		if($this->customer->isLogged()){
			$data = $this->customer->getInfo();
		}
		elseif($this->config->get('config_guest_checkout')){
			$data['customer_id'] = 0;
			$data['customer_group_id'] = $this->config->get('config_customer_group_id');
		}
		//Guest checkout no allowed and customer not logged in
		else{
			$this->error['checkout']['guest'] = $this->language->get('error_checkout_guest');
			return false;
		}
		
		//Payment info
		$data['payment_address']  = $this->getPaymentAddress();
		
		$payment_method = $this->cart->getPaymentMethod();
		$data['payment_method'] = $payment_method['code'];
		
		//Shipping info
		if($this->cart->hasShipping()){
			$data['shipping_address'] = $this->getShippingAddress();
			
			$shipping_method = $this->getShippingMethod();
			$data['shipping_code'] = $shipping_method['code'];
			$data['shipping_method'] = $shipping_method['method'];
		}
		
		$data['invoice_prefix'] = $this->tool->format_invoice($this->config->get('config_invoice_prefix'));
		$data['store_id'] = $this->config->get('config_store_id');
		$data['store_name'] = $this->config->get('config_name');
		
		if ($data['store_id']) {
			$data['store_url'] = $this->config->get('config_url');
		} else {
			$data['store_url'] = SITE_URL;
		}
		
		$totals = $this->getTotals();
		
		$data['total'] = $totals['total'];
		$data['totals'] = $totals['data'];
		
		$product_data = $this->getProducts();
		
		foreach ($product_data as &$product) {
			$product['tax'] = $this->tax->getTax($product['total'], $product['tax_class_id']);
		}unset($product);
		
		// Gift Voucher
		$voucher_data = array();
		
		if (!empty($this->session->data['vouchers'])) {
			$voucher_data = $this->session->data['vouchers']; 
			
			//TODO: This is not a good way to generate unique IDs!
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
		
		if(!empty($data['shipping_address'])){
			foreach($data['shipping_address'] as $key => $sa){
				$data['shipping_' . $key] = $sa;
			}
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
				$this->_e('C-3', 'cart', $this->language->format('error_product_minimum', $product['name'], $product['minimum']));
				return false;
			}
		}
		
		return true;
	}
	
	public function validate(){
		if($this->isEmpty()){
			$this->_e('C-1', 'cart', 'error_cart_empty');
			return false;
		}
		
		if(!$this->config->get('config_stock_checkout') && !$this->hasStock()){
			return false;
		}
		
		if(!$this->validateMinimumQuantity()){
			return false;
		}
		
		return true;
	}
}