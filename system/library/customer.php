<?php
class Customer extends Library
{
	private $customer_id;
	private $information;
	private $payment_info;
	private $settings;
	
  	public function __construct($registry)
  	{
		parent::__construct($registry);
		
		if (isset($this->session->data['customer_id'])) {
			$customer = $this->db->queryRow("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$this->session->data['customer_id'] . "' AND status = '1'");
			
			if (!empty($customer)) {
				$this->set_customer($customer);
			} else {
				$this->logout();
			}
  		}
	}
	
	public function isLogged()
	{
		return $this->customer_id ? true : false;
  	}

  	public function getId()
  	{
		return $this->customer_id;
  	}
	
	public function add($data)
	{
		$this->System_Model_Customer->addCustomer($data);
	}
	
	public function edit($data, $customer_id = null)
	{
		if (!$customer_id) {
			$customer_id = $this->customer_id;
		}
		
		if (!$customer_id || empty($data)) {
			return false;
		}
		
		$this->System_Model_Customer->editCustomer($customer_id, $data);
	}
	
	public function editPassword($customer_id, $password)
	{
		$this->System_Model_Customer->editPassword($customer_id, $password);
	}
	
	public function info($key = null)
	{
		if ($key && isset($this->information[$key])) {
			return $this->information[$key];
		}
		
		return $this->information;
	}
	
  	public function getCustomerGroupId()
  	{
  		if (!empty($this->information['customer_group_id'])) {
			return $this->information['customer_group_id'];
		}
		
		return (int)$this->config->get('config_customer_group_id');
  	}
	
	public function login($email, $password, $override = false)
	{
  		$where = "LOWER(email) = '" . $this->db->escape(strtolower($email)) . "' AND status = '1'";
		
		if (!$override) {
			$where .= " AND password = '" . $this->customer->encrypt($password) . "'";
			
			if ($this->config->get('config_customer_approval')) {
				$where .= " AND approved = '1'";
			}
		}
		
		$customer = $this->db->queryRow("SELECT * FROM " . DB_PREFIX . "customer WHERE $where LIMIT 1");
		
		if ($customer) {
			$this->set_customer($customer);
			
			if (!empty($customer['cart'])) {
				$this->cart->merge($customer['cart']);
			}
			
			if (!empty($customer['wishlist'])) {
				$this->cart->merge_wishlist($customer['wishlist']);
			}
			
			$this->order->synchronizeOrders($customer);
			
			return true;
		}
	
		return false;
  	}
  	
	public function logout()
	{
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET cart = '" . $this->db->escape(isset($this->session->data['cart']) ? serialize($this->session->data['cart']) : '') . "', wishlist = '" . $this->db->escape(isset($this->session->data['wishlist']) ? serialize($this->session->data['wishlist']) : '') . "' WHERE customer_id = '" . (int)$this->customer_id . "'");
		
		$this->session->end();
		
		$this->customer_id = null;
		
		$this->information = array();
		
		//TODO: Add as option in admin panel $this->config->get('config_logout_message');
		$this->message->add('notify', "Logged Out");
  	}
	
	public function get_setting($key)
	{
		return isset($this->settings[$key]) ? $this->settings[$key] : null;
	}
	
	public function set_setting($key, $value)
	{
		if(!$this->customer_id) return;
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_setting WHERE customer_id = '" . $this->customer_id . "' AND `key` = '" . $this->db->escape($key) . "'");
		
		if (is_object($value) || is_array($value) || is_resource($value)) {
			$value = serialize($value);
			$serialized = 1;
		} else {
			$serialized = 0;
		}
		
		$this->db->query("INSERT INTO " . DB_PREFIX . "customer_setting SET customer_id = '" . $this->customer_id . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape($value) . "', serialized = '$serialized'");
		
		$this->settings[$key] = $value;
	}
	
	public function delete_setting($key)
	{
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_setting WHERE customer_id = '$this->customer_id' AND `key` = '" . $this->db->escape($key) . "'");
	}
	
	public function getAddress($address_id)
	{
		$address = $this->Model_Account_Address->getAddress($address_id);
		
		if (!$address) {
			return null;
		}
		
		if ($this->isLogged()) {
			//Associate this address to this customer
			if (!(int)$address['customer_id']) {
				$address['customer_id'] = $this->customer_id;
				$this->Model_Account_Address->editAddress($address['address_id'], $address);
			}
			elseif ((int)$address['customer_id'] !== $this->customer_id) {
				trigger_error("Customer (id: $this->customer_id) attempted to access an unassociated address!");
				
				return null;
			}
		}
		
		return $address;
	}
	
	public function getAddresses()
	{
		$filter = array(
			'customer_ids' => array($this->customer_id),
		);
		
		return $this->Model_Account_Address->getAddresses($filter);
	}
	
	public function getShippingAddresses()
	{
		$allowed_zones = $this->cart->getAllowedShippingZones();
		
		$filter = array(
			'customer_ids' => array($this->customer_id),
			'country_ids' => array_column($allowed_zones, 'country_id'),
			'zone_ids' => array_column($allowed_zones, 'zone_id'),
		);
		
		$addresses = $this->Model_Account_Address->getAddresses($filter);
		
		return $addresses;
	}
	
	public function getPaymentAddresses()
	{
		$filter = array(
			'customer_ids' => array($this->customer_id),
		);
		
		return $this->Model_Account_Address->getAddresses($filter);
	}
	
	public function getOrders()
	{
		if ($this->isLogged()) {
			return $this->db->queryRows("SELECT * FROM " . DB_PREFIX . "order WHERE customer_id = '" . $this->customer_id . "' AND order_status_id > 0");
		}
		
		return false;
	}
	
  	public function getBalance()
  	{
  		if(!$this->customer_id) return 0;
		
		return $this->db->queryVar("SELECT SUM(amount) AS total FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$this->customer_id . "'");
  	}

  	public function getRewardPoints()
  	{
  		if(!$this->customer_id) return 0;
  		
		return $this->db->queryVar("SELECT SUM(points) AS total FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$this->customer_id . "'");
  	}
	
	public function encrypt($password)
	{
		return md5($password);
	}
	
	private function set_customer($customer)
	{
		if(empty($customer)) return;
		
		$this->customer_id = (int)$customer['customer_id'];
		$this->session->data['customer_id'] = $this->customer_id;
		$this->information = $customer;
		
		//Load Customer Settings
		$settings = $this->db->queryRows("SELECT * FROM " . DB_PREFIX . "customer_setting WHERE customer_id = '" . $this->customer_id . "'");
		
		foreach ($settings as $setting) {
			if ($setting['serialized']) {
				$this->settings[$setting['key']] = unserialize($setting['value']);
			} else {
				$this->settings[$setting['key']] = $setting['value'];
			}
		}
		
		$this->payment_info = $this->get_setting('payment_info_' . $this->information['payment_code']);
		
		if (!$this->payment_info) {
			$this->payment_info = array(
				'address_id' => $this->information['address_id'],
				'payment_code' => $this->information['payment_code'],
			);
		}
			
		$cart = $this->cart->getCart();
		$wishlist = $this->cart->get_wishlist();
		
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET cart = '" . $this->db->escape(serialize($cart)) . "', wishlist = '" . $this->db->escape(serialize($wishlist)) . "', ip = '" . $this->db->escape($_SERVER['REMOTE_ADDR']) . "' WHERE customer_id = '" . (int)$this->customer_id . "'");
	
		$ip_set = $this->db->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "customer_ip WHERE customer_id = '" . (int)$this->session->data['customer_id'] . "' AND ip = '" . $this->db->escape($_SERVER['REMOTE_ADDR']) . "'");
		
		if (!$ip_set) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "customer_ip SET customer_id = '" . $this->customer_id . "', ip = '" . $this->db->escape($_SERVER['REMOTE_ADDR']) . "', date_added = NOW()");
		}
		
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET ip = '" . $this->db->escape($_SERVER['REMOTE_ADDR']) . "' WHERE customer_id = '" . (int)$this->customer_id . "'");
	}

	public function emailRegistered($email)
	{
		return (int) $this->db->queryVar("SELECT customer_id FROM " . DB_PREFIX . "customer WHERE email = '" . $this->db->escape($email) . "'");
	}

	public function isBlacklisted($customer_id = null, $ips = array())
	{
		if (!$customer_id) {
			$customer_id = $this->customer_id;
		}
		
		if ($customer_id) {
			$customer_ips = $this->db->queryColumn("SELECT ip FROM " . DB_PREFIX . "customer_ip WHERE customer_id = " . (int)$customer_id);
			
			if ($customer_ips) {
				$ips += $customer_ips;
			}
		}
		
		if (empty($ips)) {
			return false;
		}
		
		return $this->db->queryVar("SELECT COUNT(*) FROM `" . DB_PREFIX . "customer_ip_blacklist` WHERE ip IN ('" . implode("','", $ips) . "')");
	}
}