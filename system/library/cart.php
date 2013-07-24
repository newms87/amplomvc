<?php
class Cart extends Library
{
	private $data = array();
	private $error = array();
	private $error_code = null;
	
	private $no_json = false;
	
  	public function __construct($registry)
  	{
  		parent::__construct($registry);

		$this->language->system('cart');
		
		if (!isset($this->session->data['cart']) || !is_array($this->session->data['cart'])) {
			$this->session->data['cart'] = array();
		}
		
		if (!isset($this->session->data['wishlist']) || !is_array($this->session->data['wishlist'])) {
			$this->session->data['wishlist'] = array();
		}
	}
	
	public function _e($code, $type, $key)
	{
		$this->error[$type] = $this->language->get($key);
		$this->error_code = $code;
	}
	
	public function get_error_code()
	{
		return $this->error_code;
	}
	
	public function get_errors($type = null, $pop = false, $name_format = false)
	{
		//Get Specific Error
		if ($type) {
			if (isset($this->error[$type])) {
				$e = $this->error[$type];
				
				if ($pop) {
					unset($this->error[$type]);
				}
			}
			else {
				return array();
			}
		}
		//Get All Errors
		else {
			$e = $this->error;
			
			if ($pop) {
				$this->error = array();
			}
		}
		
		if ($name_format) {
			return $this->tool->name_format($name_format, $e);
		}
		
		return $e;
	}
	
	public function has_error($type)
	{
		$type_list = explode('>', $type);
		
		$error = $this->error;
		foreach ($type_list as $t) {
			if (!isset($error[$t])) {
				return false;
			}
			$error = $error[$t];
		}
		
		return true;
	}
	
	public function get_cart()
	{
		return !empty($this->session->data['cart']) ? $this->session->data['cart'] : null;
	}
	
	/**
	 * Cart Functions
	 */
	
	public function add($product_id, $quantity = 1, $options = array(), $no_json = true) {
  		$this->no_json = $no_json;
		
  		$product_id = (int)$product_id;
		
		
		if ($this->validateProduct($product_id, $quantity, $options)) {
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
		else {
			return false;
		}
  	}

  	public function update($key, $qty)
  	{
		if ((int)$qty > 0) {
			$this->session->data['cart'][$key] = (int)$qty;
		} else {
			$this->remove($key);
		}
		
		$this->data = array();
  	}
	
	public function merge($cart)
	{
		if (is_string($cart)) {
			$cart = unserialize($cart);
		}
		
		if(empty($cart)) return false;

		foreach ($cart as $key => $qty) {
			if (!empty($this->session->data['cart'][$key])) {
				$this->session->data['cart'][$key] += $qty;
			} else {
				$this->session->data['cart'][$key] = $qty;
			}
		}
		
		return true;
	}
	
  	public function remove($key)
  	{
		if (isset($this->session->data['cart'][$key])) {
			unset($this->session->data['cart'][$key]);
  		}
		
		$this->data = array();
	}
	
  	public function clear()
  	{
		$this->data = array();
		
		$this->session->data['cart'] = array();
		$this->session->data['wishlist'] = array();
		
		unset($this->session->data['shipping_address_id']);
		unset($this->session->data['payment_address_id']);
		unset($this->session->data['shipping_method_id']);
		unset($this->session->data['payment_method_id']);
		unset($this->session->data['comment']);
		unset($this->session->data['coupons']);
		unset($this->session->data['reward']);
		unset($this->session->data['vouchers']);
		
		$this->order->clear();
  	}
	
	/**
	 * Cart Weight
	 */
	
  	public function getWeight()
  	{
		$weight = 0;
	
		foreach ($this->getProducts() as $product) {
			if ($product['shipping']) {
				$weight += $this->weight->convert($product['weight'], $product['weight_class_id'], $this->config->get('config_weight_class_id'));
			}
		}
	
		return $weight;
	}
	
	/**
	 * Cart Totals
	 */
	
  	public function getSubTotal()
  	{
		$total = 0;
		
		foreach ($this->getProducts() as $product) {
			$total += $product['total'];
		}

		return $total;
  	}
	
	public function getTotals()
	{
		$total_data = array();
		$total = 0;
		$taxes = $this->getTaxes();
		
		$sort_order = array();
		
		//TODO: We can do better than this, getExtensions should only return active totals
		$results = $this->Model_Setting_Extension->getExtensions('total');
		
		//TODO: why sort_order was kept in config vs with extension data is beyond me...
		//Remove `key` like '%_status' from oc_setting table
		foreach ($results as $key => $value) {
			$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
		}
		
		array_multisort($sort_order, SORT_ASC, $results);
		
		foreach ($results as $result) {
			if ($this->config->get($result['code'] . '_status')) {
				$classname = "System_Extension_Total_Model_" . $this->tool->formatClassname($result['code']);
				
				$this->$classname->getTotal($total_data, $total, $taxes);
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
	
	public function getTotal()
	{
		$total = 0;
		
		foreach ($this->getProducts() as $product) {
			$total += $this->tax->calculate($product['total'], $product['tax_class_id']);
		}

		return $total;
  	}
	
	public function getTotalPoints()
	{
		$points_total = 0;
		
		$products = $this->getProducts();
		
		foreach ($products as $product) {
			$points_total += (int)$product['points'];
		}
		
		return $points_total;
	}
	
	/**
	 * Taxes
	 **/
	
	public function getTaxes()
	{
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
	
	/**
	 *  Cart Products
	 */
	
	public function getProductId($key)
	{
		if (!isset($this->session->data['cart'][$key])) {
			return false;
		}
		
		$product = explode(':', $key);
		
		return $product[0];
		
	}
	
	public function getProductName($key)
	{
		if (!isset($this->session->data['cart'][$key])) {
			return '';
		}
		
		$product = explode(':', $key);
		
		$product_id = $product[0];
		
		return $this->Model_Catalog_Product->getProductName($product_id);
	}
	
	public function getProducts()
	{
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
				
				$product = $this->db->queryRow("SELECT * FROM " . DB_PREFIX . "product p WHERE p.product_id = '" . (int)$product_id . "' AND p.date_available <= NOW() AND p.status = '1'");
				
				if ($product) {
					$this->translation->translate('product', $product['product_id'], $product);
					
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

					$customer_group_id = $this->customer->getCustomerGroupId();
					
					// Product Specials
					$product_special_price = $this->db->queryVar("SELECT price FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$customer_group_id . "' AND ((date_start = '" . DATETIME_ZERO . "' OR date_start <= NOW()) AND (date_end = '" . DATETIME_ZERO . "' OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1");
				
					if ($product_special_price) {
						$product['price'] = $product_special_price;
					} else {
						// Product Discounts
						$discount_quantity = 0;
						
						foreach ($this->session->data['cart'] as $key_2 => $quantity_2) {
							$product_2 = explode(':', $key_2);
							
							if ((int)$product_2[0] === (int)$product_id) {
								$discount_quantity += $quantity_2;
							}
						}
						
						$product_discount_price = $this->db->queryVar("SELECT price FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$customer_group_id . "' AND quantity <= '" . (int)$discount_quantity . "' AND ((date_start = '" . DATETIME_ZERO . "' OR date_start <= NOW()) AND (date_end = '" . DATETIME_ZERO . "' OR date_end > NOW())) ORDER BY quantity DESC, priority ASC, price ASC LIMIT 1");
						
						if ($product_discount_price) {
							$product['price'] = $product_discount_price;
						}
					}
			
					// Reward Points
					$reward = (int)$this->db->queryVar("SELECT points FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$customer_group_id . "'");
					
					// Downloads
					$downloads = $this->db->queryRows("SELECT * FROM " . DB_PREFIX . "product_to_download p2d LEFT JOIN " . DB_PREFIX . "download d ON (p2d.download_id = d.download_id) WHERE p2d.product_id = '" . (int)$product_id . "'");
					
					$this->translation->translate_all('download', 'download_id', $downloads);
					
					// Stock
					if (!$product['quantity'] || ($product['quantity'] < $quantity)) {
						$stock = false;
					}
					
					$product['key'] 			= $key;
					$product['option']		= $option_data;
					$product['download']		= $downloads;
					$product['quantity']		= $quantity;
					$product['stock']			= $stock;
					$product['cost']			+= $option_cost;
					$product['total_cost']	= $product['cost'] * $quantity;
					$product['price']			+= $option_price;
					$product['total']			= $product['price'] * $quantity;
					$product['reward']		= $reward * $quantity;
					$product['points']		= ((int)$product['points'] + $option_points) * $quantity;
					$product['weight']		= ((int)$product['weight'] + $option_weight) * $quantity;
					
					$this->data[$key] = $product;
				} else {
					$this->remove($key);
				}
			}
		}

		return $this->data;
  	}

  	public function countProducts()
  	{
		$product_total = 0;
			
		$products = $this->getProducts();
			
		foreach ($products as $product) {
			$product_total += $product['quantity'];
		}
					
		return $product_total;
	}
	
  	public function hasProducts()
  	{
		return count($this->session->data['cart']);
  	}
	
	public function productPurchasable($product)
	{
		if (is_integer($product)) {
			$product = $this->Model_Catalog_Product->getProduct($product);
		}
		
		if (!$product['status']) {
			return false;
		}
		
		if ($product['quantity'] < 1 && !$this->config->get('config_stock_checkout')) {
			return false;
		}
		
		if (!$this->date->isInFuture($product['date_expires'], true) || !$this->date->isInPast($product['date_available'], false)) {
			return false;
		}
		
		return true;
	}
	
	public function validateProduct($product_id, $quantity, $options)
	{
		$product_info = $this->Model_Catalog_Product->getProduct($product_id);
		
		if ($product_info) {
			$product_options = $this->Model_Catalog_Product->getProductOptions($product_id);
			
			$restrictions = $this->Model_Catalog_Product->getProductOptionValueRestrictions($product_id);
			
			foreach ($product_options as $product_option) {
				if (!empty($product_option['product_option_value']) && $product_option['required']) {
					if (empty($options[$product_option['product_option_id']])) {
						$this->error['add']['option'][$product_option['product_option_id']] = $this->_('error_required', $product_option['display_name']);
						return false;
					}
					elseif ($product_option['group_type'] == 'single' && count($options[$product_option['product_option_id']]) > 1) {
						$this->error['add']['option'][$product_option['product_option_id']] = $this->_('error_selected_multi', $product_option['display_name']);
						return false;
					}
				}
			}
			
			foreach ($options as $po_id => $selected_po) {
				foreach ($selected_po as $selected_pov) {
					if (isset($selected_pov['option_value_id']) && isset($restrictions[$selected_pov['option_value_id']])) {
						foreach ($options as $selected_po2) {
							foreach ($selected_po2 as $selected_pov2) {
								if (in_array($selected_pov2['option_value_id'], $restrictions[$selected_pov['option_value_id']])) {
									$this->error['add']['option'][$po_id] = $this->language->get('error_pov_restriction');
									return false;
								}
							}
						}
					}
				}
			}
		}
		else {
			$this->error['add']['product_id'] = $this->language->get('error_invalid_product_id');
			return false;
		}

		return true;
	}
	
	/**
	 * Cart Stock
	 */
	
	public function isEmpty()
	{
		return !(count($this->session->data['cart']) || !empty($this->session->data['vouchers']));
	}

  	public function hasStock()
  	{
  		foreach ($this->getProducts() as $product) {
			if (!$product['stock']) {
				$this->_e('C-2', 'cart', $this->_('error_cart_stock', $this->url->link('product/product','product_id=' . $product['product_id']), $product['name']));
				return false;
			}
		}
		
		return true;
  	}
	
	public function validateMinimumQuantity()
	{
		$product_total = 0;
		
		$products = $this->getProducts();
		
		foreach ($products as $product) {
			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}
			}
			
			if ($product_total < $product['minimum']) {
				$this->_e('C-3', 'cart', $this->_('error_product_minimum', $product['name'], $product['minimum']));
				return false;
			}
		}
		
		return true;
	}
	
	public function validate()
	{
		if ($this->isEmpty()) {
			$this->_e('C-1', 'cart', 'error_cart_empty');
			return false;
		}
		
		if (!$this->config->get('config_stock_checkout') && !$this->hasStock()) {
			return false;
		}
		
		if (!$this->validateMinimumQuantity()) {
			return false;
		}
		
		return true;
	}

	public function validateCheckout()
	{
		if (!$this->validate()) {
			$this->_e('CKO-1', 'checkout', 'error_checkout_validate');
			return false;
		}
		
		if (!$this->validatePaymentDetails()) {
			$this->_e('CKO-2', 'checkout', 'error_checkout_payment');
			return false;
		}
		
		if (!$this->validateShippingDetails()) {
			$this->_e('CKO-3', 'checkout', 'error_checkout_shipping');
			return false;
		}
		
		return true;
	}
	
	/**
	 * Wishlist Functions
	 */
	
	public function get_wishlist()
	{
		return !empty($this->session->data['wishlist']) ? $this->session->data['wishlist'] : null;
	}
	
	public function merge_wishlist($wishlist)
	{
		if (is_string($wishlist)) {
			$wishlist = unserialize($wishlist);
		}
		
		if(empty($wishlist)) return false;
		
		if (!isset($this->session->data['wishlist'])) {
			$this->session->data['wishlist'] = array();
		}
	
		foreach ($wishlist as $product_id) {
			if (!in_array($product_id, $this->session->data['wishlist'])) {
				$this->session->data['wishlist'][] = $product_id;
			}
		}
		
		return true;
	}
	
	/**
	 * Product Compare Functions
	 */
	
	public function get_compare_list()
	{
		return !empty($this->session->data['compare']) ? $this->session->data['compare'] : null;
	}
	
	public function get_compare_count()
	{
		return !empty($this->session->data['compare']) ? count($this->session->data['compare']) : null;
	}
	
	
	/**
	 * Shipping & Payment API
	 */
	
  public function hasDownload()
  	{
		foreach ($this->getProducts() as $product) {
			if ($product['download']) {
			return true;
			}
		}
		
		return false;
	}
	
	public function hasPaymentAddress()
	{
		return !empty($this->session->data['payment_address_id']);
	}
	
	public function getPaymentAddressId()
	{
		return isset($this->session->data['payment_address_id']) ? $this->session->data['payment_address_id'] : false;
	}
	
	public function getPaymentAddress()
	{
		if (isset($this->session->data['payment_address_id'])) {
			return $this->customer->getAddress($this->session->data['payment_address_id']);
		}

		return false;
	}
	
	public function setPaymentAddress($address = null)
	{
		if (empty($address)) {
			unset($this->session->data['payment_address_id']);
			$this->setPaymentMethod();
			return true;
		}
		elseif (is_array($address)) {
			$address_id = $this->Model_Account_Address->addAddress($address);
			
			if (!$address_id) {
				$this->_e('PA-10', 'payment_address', 'error_payment_address_details');
				return false;
			}
		}
		else {
			$address_id = (int)$address;
		}
		
		if (!empty($address_id)) {
			$this->session->data['payment_address_id'] = $address_id;
		}
		
		if (!$this->validatePaymentAddress()) {
			$this->_e('SA-11', 'payment_address', 'error_payment_address_invalid');
			unset($this->session->data['payment_address_id']);
			
			$this->setPaymentMethod();
			return false;
		}
		
		return true;
	}
	
	/** Shipping Address Operations **/
	public function hasShipping()
  	{
		foreach ($this->getProducts() as $product) {
			if ($product['shipping']) {
				return true;
			}
		}
		
		return false;
	}
	
	public function hasShippingAddress()
	{
		return !empty($this->session->data['shipping_address_id']);
	}
	
	public function getShippingAddressId()
	{
		return isset($this->session->data['shipping_address_id']) ? $this->session->data['shipping_address_id'] : false;
	}
	
	public function getShippingAddress()
	{
		if (isset($this->session->data['shipping_address_id'])) {
			return $this->customer->getAddress($this->session->data['shipping_address_id']);
		}

		return false;
	}
	
	public function setShippingAddress($address = null)
	{
		if (empty($address)) {
			unset($this->session->data['shipping_address_id']);
			$this->setShippingMethod();
			return true;
		}
		elseif (is_array($address)) {
			$address_id = $this->Model_Account_Address->addAddress($address);
			
			if (!$address_id) {
				$this->_e('SA-10', 'shipping_address', 'error_shipping_address_details');
				return false;
			}
		}
		else {
			$address_id = (int)$address;
		}
		
		if (!empty($address_id)) {
			$this->session->data['shipping_address_id'] = $address_id;
		}
		
		if (!$this->validateShippingAddress()) {
			$this->_e('SA-11', 'shipping_address', 'error_shipping_address_invalid');
			unset($this->session->data['shipping_address_id']);
			return false;
		}
		
		$this->setShippingMethod();
		
		return true;
	}
	
	//TODO: Move this to System_Extension_Payment controller...
	/** Payment Method Operations **/
	
	public function hasPaymentMethod()
	{
		return !empty($this->session->data['payment_method_id']);
	}
	
	public function getPaymentMethodId()
	{
		if (isset($this->session->data['payment_method_id'])) {
			return $this->session->data['payment_method_id'];
		}
		
		return false;
	}
	
	public function getPaymentMethod($payment_method_id = null, $payment_address = null, $totals = null)
	{
		if (!$payment_method_id) {
			$payment_method_id = $this->getPaymentMethodId();
		}

		if ($payment_method_id) {
			if (!empty($payment_address)) {
				if (!is_array($payment_address)) {
					$payment_address = $this->customer->getAddress($payment_address);
				}
			} else {
				$payment_address = $this->getPaymentAddress();
			}
		
			if (!$totals) {
				$totals = $this->getTotals();
			}
			
			$classname = "Model_Payment_" . $this->tool->formatClassname($payment_method_id);
			
			$method = $this->$classname->getMethod($payment_address, $totals['total']);
			
			if ($method) {
				return $method;
			}
		}
		
		return false;
	}
	
	public function getPaymentMethodData($payment_method_id = null)
	{
		if (!$payment_method_id) {
			return false;
		}
		
		$classname = "Model_Payment_" . $this->tool->formatClassname($payment_method_id);
		
		if (method_exists($this->$classname, 'data')) {
			return $this->$classname->data();
		}
		
		return false;
	}

	public function getPaymentMethods($payment_address = null)
	{
		// Payment Methods
		$methods = array();
		
		$results = $this->Model_Setting_Extension->getExtensions('payment');
		
		foreach ($results as $result) {
			$method = $this->getPaymentMethod($result['code'], $payment_address);
			
			if ($method) {
				$methods[$result['code']] = $method;
			}
		}
		
		if (!$methods) {
			$this->error['checkout']['payment_method'] = $this->_('error_payment_methods', $this->config->get('config_email'));
			
			$this->setPaymentMethod();
			
			return false;
		}
		
		uasort($methods, function ($a,$b){ return $a['sort_order'] > $b['sort_order']; });
		
		//Validate the currenlty selected payment method
		if ($this->hasPaymentMethod() && !isset($methods[$this->getPaymentMethodId()])) {
			$this->setPaymentMethod(null);
		}
		
		return $methods;
	}
	
	public function setPaymentMethod($method = null)
	{
		if (!$method) {
			unset($this->session->data['payment_method_id']);
		}
		else {
			$payment_methods = $this->getPaymentMethods();
			
			if (is_string($method)) {
				if (!isset($payment_methods[$method])) {
					$this->_e('PM-1a', 'payment_method', 'error_payment_method');
					return false;
				}
				
				$payment_method_id = $payment_methods[$method]['code'];
			}
			else {
				if (!isset($payment_methods[$method['code']])) {
					$this->_e('PM-1b', 'payment_method', 'error_payment_method');
					return false;
				}
				
				$payment_method_id = $method['code'];
			}
			
			$this->session->data['payment_method_id'] = $payment_method_id;
		}
		
		return true;
	}
	
	//TODO: Move this to System_Extension_Shipping controller...
	/** Shipping Method Operations **/
	
	public function hasShippingMethod()
	{
		return !empty($this->session->data['shipping_method_id']);
	}
	
	public function getShippingMethodId()
	{
		if (isset($this->session->data['shipping_method_id'])) {
			return $this->session->data['shipping_method_id'];
		}
		
		return false;
	}
	
	public function getShippingMethod($shipping_method_id = null, $shipping_address = null)
	{
		if (!$shipping_method_id) {
			$shipping_method_id = $this->getShippingMethodId();
		}

		if ($shipping_method_id) {
			//Invalid Shipping method ID
			if (!strpos($shipping_method_id, '__')) {
				$code = $shipping_method_id;
				$method = false;
			} else {
				list($code, $method) = explode("__", $shipping_method_id, 2);
			}
			
			if (!empty($shipping_address)) {
				if (!is_array($shipping_address)) {
					$shipping_address = $this->customer->getAddress($shipping_address);
				}
			}
			elseif ($this->hasShippingAddress()) {
				$shipping_address = $this->getShippingAddress();
			}
			else {
				$this->_e('SM-2', 'shipping_method', 'error_shipping_address');
				return false;
			}
		
			if (!$this->isAllowedShippingZone($shipping_address)) {
				$this->_e('SM-3', 'shipping_method', 'error_shipping_zone');
				return false;
			}

			$classname = "Model_Shipping_" . $this->tool->formatClassname($code);
			$quotes = $this->$classname->getQuote($shipping_address);
			
			if (!empty($quotes)) {
				if (!$method) {
					return $quotes;
				}
				
				foreach ($quotes as $quote) {
					if ($quote['method'] === $method) {
						return $quote;
					}
				}
			}
		}
		
		return false;
	}
	
	public function getShippingMethods($shipping_address = null)
	{
		//Find Available Shipping Methods
		$results = $this->Model_Setting_Extension->getExtensions('shipping');
		
		$methods = array();
		
		foreach ($results as $result) {
			$quotes = $this->getShippingMethod($result['code'], $shipping_address);
			
			if (!empty($quotes)) {
				foreach ($quotes as $quote) {
					$methods[$quote['code'] . '__' . $quote['method']] = $quote;
				}
			}
		}
		
		if ($methods) {
			//Validate the currently selected shipping method
			if (!$shipping_address && $this->hasShippingMethod() && !isset($methods[$this->getShippingMethodId()])) {
				$this->setShippingMethod();
			}
			
			uasort($methods, function ($a,$b){ return $a['sort_order'] > $b['sort_order']; });
			
			return $methods;
		}
		
		//No Shipping Options Available!
		$msg = $this->_('error_shipping_methods', $this->url->link('information/contact'));
		$this->_e('SM-4', 'shipping_method', $msg);
		
		return false;
	}
	
	public function getShippingMethodData($shipping_method_id)
	{
		if (!$shipping_method_id) {
			return false;
		}
		
		//Invalid Shipping method ID
		if (!strpos($shipping_method_id, '__')) {
			$code = $shipping_method_id;
			$method = false;
		} else {
			list($code, $method) = explode("__", $shipping_method_id, 2);
		}
		
		$classname = "Model_Shipping_" . $this->tool->formatClassname($code);
		
		if (method_exists($this->$classname, 'data')) {
			return $this->$classname->data($method);
		}
		
		return false;
	}
	
	public function setShippingMethod($method = null)
	{
		if (!$method) {
			unset($this->session->data['shipping_method_id']);
		}
		else {
			$shipping_methods = $this->getShippingMethods();
			
			if (is_string($method)) {
				if (!isset($shipping_methods[$method])) {
					$this->_e('SM-1a', 'shipping_method', 'error_shipping_method');
					return false;
				}
				
				$shipping_method_id = $method;
			}
			else {
				$shipping_method_id = $method['code'] . '__' . $method['method'];
				
				if (!isset($shipping_methods[$shipping_method_id])) {
					$this->_e('SM-1b', 'shipping_method', 'error_shipping_method');
					return false;
				}
			}
			
			$this->session->data['shipping_method_id'] = $shipping_method_id;
		}
		
		return true;
	}
	
	public function validateShippingDetails()
	{
		if ($this->hasShipping()) {
			if (!$this->validateShippingAddress()) {
				$this->_e('CO-10', 'checkout', 'error_shipping_address');
				return false;
			}
			
			if (!$this->getShippingMethod()) {
				$this->_e('CO-11', 'checkout', 'error_shipping_method');
				return false;
			}
		}
		
		return true;
	}

	public function validatePaymentDetails()
	{
		if (!$this->validatePaymentAddress()) {
			$this->_e('CO-12', 'checkout', 'error_payment_address');
			return false;
		}
		
		if (!$this->getPaymentMethod()) {
			$this->_e('CO-13', 'checkout', 'error_payment_method');
			return false;
		}
		
		return true;
	}
	
	public function isAllowedShippingZone($shipping_address)
	{
		if (!empty($shipping_address['country_id']) && !empty($shipping_address['zone_id'])) {
			return $this->address->inGeoZone($shipping_address, $this->config->get('config_allowed_shipping_zone'));
		}
		
		return false;
	}
	
	public function getAllowedShippingZones()
	{
		$geo_zone_id = $this->config->get('config_allowed_shipping_zone');
		
		if ($geo_zone_id > 0) {
			$allowed_geo_zones = $this->cache->get('zone.allowed.' . $geo_zone_id);
			
			if (!$allowed_geo_zones) {
				$allowed_geo_zones = array();
				
				$zones = $this->Model_Localisation_Zone->getZonesByGeoZone($geo_zone_id);
				
				foreach ($zones as $zone) {
					$country = $this->Model_Localisation_Country->getCountry($zone['country_id']);
					
					$allowed_geo_zones[] = array(
						'country' => $country,
						'zone'=> $zone
					);
				}
				
				$this->cache->set('zone.allowed.' . $geo_zone_id, $allowed_geo_zones);
			}
			
			return $allowed_geo_zones;
		}
		
		return array();
	}

	public function validatePaymentAddress($address = null)
	{
		unset($this->error['payment_address']);
		
		if (empty($address)) {
			if ($this->hasPaymentAddress()) {
				$address = $this->getPaymentAddress();
			}
			else {
				$this->_e('PA-1', 'payment_address', 'error_payment_address');
				return false;
			}
		}
		
		$country_id = !empty($address['country_id']) ? (int)$address['country_id'] : 0;
		$zone_id = !empty($address['zone_id']) ? (int)$address['zone_id'] : 0;
		
		if ( ! $this->db->queryVar("SELECT COUNT(*) as total FROM " . DB_PREFIX . "country WHERE country_id = '$country_id'")) {
			$this->_e('PA-2', 'payment_address', 'error_country_id');
			return false;
		}
		
		if ( ! $this->db->queryVar("SELECT COUNT(*) as total FROM " . DB_PREFIX . "zone WHERE zone_id = '$zone_id' AND country_id = '$country_id'")) {
			$this->_e('PA-3', 'payment_address', 'error_zone_id');
			return false;
		}
		
		return true;
	}
	
	public function validateShippingAddress($address = null)
	{
		unset($this->error['shipping_address']);
		
		if (empty($address)) {
			if ($this->hasShippingAddress()) {
				$address = $this->getShippingAddress();
			}
			else {
				$this->_e('SA-1', 'shipping_address', 'error_shipping_address');
				return false;
			}
		}
		
		$country_id = !empty($address['country_id']) ? (int)$address['country_id'] : 0;
		$zone_id = !empty($address['zone_id']) ? (int)$address['zone_id'] : 0;
		
		if ( ! $this->db->queryVar("SELECT COUNT(*) as total FROM " . DB_PREFIX . "country WHERE country_id = '$country_id'")) {
			$this->_e('SA-2', 'shipping_address', 'error_country_id');
			return false;
		}
		
		if ( ! $this->db->queryVar("SELECT COUNT(*) as total FROM " . DB_PREFIX . "zone WHERE zone_id = '$zone_id' AND country_id = '$country_id'")) {
			$this->_e('SA-3', 'shipping_address', 'error_zone_id');
			return false;
		}
		
		if (!$this->isAllowedShippingZone($address)) {
			$this->_e('SA-4', 'shipping_address', 'error_shipping_geo_zone');
			return false;
		}
		
		return true;
	}
	
	public function hasVouchers($voucher_id = null)
	{
		if ($voucher_id) {
			return !empty($this->session->data['vouchers'][$voucher_id]);
		}
		
		return !empty($this->session->data['vouchers']);
	}
	
	public function getVoucherIds()
	{
		return isset($this->session->data['vouchers']) ? $this->session->data['vouchers'] : array();
	}
	
	public function getVouchers()
	{
		$vouchers = array();
		
		foreach ($this->getVoucherIds() as $voucher_id) {
			$vouchers[] = $this->System_Model_Voucher->getVoucher($voucher_id);
		}
	}
			
	public function addVoucher($voucher_id)
	{
		if (!isset($this->session->data['vouchers'])) {
			$this->session->data['vouchers'][] = $voucher_id;
		} else {
			$this->session->data['vouchers'] = array($voucher_id);
		}
	}
	
	public function removeVoucher($voucher_id)
	{
		unset($this->session->data['vouchers'][$voucher_id]);
	}
	
	public function removeAllVouchers()
	{
		unset($this->session->data['vouchers']);
	}
	
	/**
	 * Guest API
	 */
	
	public function saveGuestInfo($info)
	{
		$this->session->data['guest_info'] = $info;
	}
	
	public function loadGuestInfo()
	{
		return isset($this->session->data['guest_info']) ? $this->session->data['guest_info'] : null;
	}

	/**
	 * Comments
	 */
	
	public function getComment()
	{
		return !empty($this->session->data['comment']) ? $this->session->data['comment'] : null;
	}
	
	public function setComment($comment)
	{
		$this->session->data['comment'] = strip_tags($comment);
	}
	
	/** Policies **/
	public function getShippingPolicy($shipping_policy_id)
	{
		$shipping_policies = $this->getShippingPolicies();
		
		if (isset($shipping_policies[$shipping_policy_id])) {
			$policy = $shipping_policies[$shipping_policy_id];
			
			$policy['description'] = html_entity_decode($policy['description'], ENT_QUOTES, 'UTF-8');
			
			return $policy;
		}
		
		return null;
	}
	
	public function getProductShippingPolicy($product_id)
	{
		$shipping_policy_id = $this->db->queryVar("SELECT shipping_policy_id FROM " . DB_PREFIX . "product WHERE product_id = " . (int)$product_id);
		
		if (!is_null($shipping_policy_id)) {
			return $this->getShippingPolicy($shipping_policy_id);
		}
		
		return null;
	}
	
	public function getShippingPolicies()
	{
		return $this->config->load('policies', 'shipping_policies', 0);
	}
	
	public function getReturnPolicy($return_policy_id)
	{
		$return_policies = $this->getReturnPolicies();
		
		if (isset($return_policies[$return_policy_id])) {
			$policy = $return_policies[$return_policy_id];
			
			$policy['description'] = html_entity_decode($policy['description'], ENT_QUOTES, 'UTF-8');
			
			return $policy;
		}
		
		return null;
	}
	
	public function getProductReturnPolicy($product_id)
	{
		$return_policy_id = $this->db->queryVar("SELECT return_policy_id FROM " . DB_PREFIX . "product WHERE product_id = " . (int)$product_id);
		
		if (!is_null($return_policy_id)) {
			return $this->getReturnPolicy($return_policy_id);
		}
		
		return null;
	}
	
	public function getReturnPolicies()
	{
		return $this->config->load('policies', 'return_policies', 0);
	}
}