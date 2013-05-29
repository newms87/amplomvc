<?php
class Customer 
{
	private $registry;
	private $customer_id;
	private $information;
	private $payment_info;
	private $settings;
	
  	public function __construct($registry)
  	{
		$this->registry = $registry;
		
		if (isset($this->session->data['customer_id'])) {
			$customer = $this->db->query_row("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$this->session->data['customer_id'] . "' AND status = '1'");
			
			if (!empty($customer)) {
				$this->set_customer($customer);
			} else {
				$this->logout();
			}
  		}
	}
	
	public function __get($key)
	{
		return $this->registry->get($key);
	}
	
	public function isLogged()
	{
		return $this->customer_id ? true : false;
  	}

  	public function getId()
  	{
		return $this->customer_id;
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
		
		$customer = $this->db->query_row("SELECT * FROM " . DB_PREFIX . "customer WHERE $where LIMIT 1");
		
		if ($customer) {
			$this->set_customer($customer);
			
			if (!empty($customer['cart'])) {
				$this->cart->merge($customer['cart']);
			}
			
			if (!empty($customer['wishlist'])) {
				$this->cart->merge_wishlist($customer['wishlist']);
			}
			
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
		
		//TODO: REMOVE THIS?
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
	
	public function get_shipping_addresses()
	{
		$address_list = $this->model_account_address->getAddresses();
		
		$allowed_zones = $this->cart->getAllowedShippingZones();
		
		if (empty($allowed_zones)) {
			$addresses = $address_list;
		}
		else {
			$addresses = array();
			
			foreach ($address_list as $key => $address) {
				foreach ($allowed_zones as $zone) {
					if ((int)$address['country_id'] === (int)$zone['country_id'] && ((int)$zone['zone_id'] === 0 || (int)$address['zone_id'] === (int)$zone['zone_id'])) {
						$addresses[$address['address_id']] = $address;
						break;
					}
				}
			}
		}
		
		return $addresses;
	}
	
	public function get_payment_addresses()
	{
		return $this->model_account_address->getAddresses();
	}
	
  	public function getBalance()
  	{
  		if(!$this->customer_id) return 0;
		
		return $this->db->query_var("SELECT SUM(amount) AS total FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$this->customer_id . "'");
  	}

  	public function getRewardPoints()
  	{
  		if(!$this->customer_id) return 0;
  		
		return $this->db->query_var("SELECT SUM(points) AS total FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$this->customer_id . "'");
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
		$settings = $this->db->query_rows("SELECT * FROM " . DB_PREFIX . "customer_setting WHERE customer_id = '" . $this->customer_id . "'");
		
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
			
		$cart = $this->cart->get_cart();
		$wishlist = $this->cart->get_wishlist();
		
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET cart = '" . $this->db->escape(serialize($cart)) . "', wishlist = '" . $this->db->escape(serialize($wishlist)) . "', ip = '" . $this->db->escape($_SERVER['REMOTE_ADDR']) . "' WHERE customer_id = '" . (int)$this->customer_id . "'");
	
		$ip_set = $this->db->query_var("SELECT COUNT(*) FROM " . DB_PREFIX . "customer_ip WHERE customer_id = '" . (int)$this->session->data['customer_id'] . "' AND ip = '" . $this->db->escape($_SERVER['REMOTE_ADDR']) . "'");
		
		if (!$ip_set) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "customer_ip SET customer_id = '" . $this->customer_id . "', ip = '" . $this->db->escape($_SERVER['REMOTE_ADDR']) . "', date_added = NOW()");
		}
		
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET ip = '" . $this->db->escape($_SERVER['REMOTE_ADDR']) . "' WHERE customer_id = '" . (int)$this->customer_id . "'");
	}
}