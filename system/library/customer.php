<?php
class Customer extends Library
{
	private $customer_id;
	private $information;
	private $payment_info;
	private $metadata;

	public function __construct($registry)
	{
		parent::__construct($registry);

		if (isset($this->session->data['customer_id'])) {
			$customer = $this->db->queryRow("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$this->session->data['customer_id'] . "' AND status = '1'");

			if (!empty($customer)) {
				$this->setCustomer($customer);
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

		if (!empty($data['password'])) {
			$this->editPassword($customer_id, $data['password']);
		}
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
			$this->setCustomer($customer);

			if (!empty($customer['cart'])) {
				$this->cart->merge($customer['cart']);
			}

			if (!empty($customer['wishlist'])) {
				$this->cart->mergeWishlist($customer['wishlist']);
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

	public function getMeta($key)
	{
		return isset($this->metadata[$key]) ? $this->metadata[$key] : null;
	}

	public function getMetaData()
	{
		return $this->metadata;
	}

	public function setMeta($key, $value)
	{
		if (!$this->customer_id) {
			return;
		}

		$this->System_Model_Customer->setMetaData($this->customer_id, $key, $value);

		$this->metadata[$key] = $value;
	}

	public function deleteMeta($key)
	{
		$this->System_Model_Customer->deleteMetaData($this->customer_id, $key);
	}

	public function getAddress($address_id)
	{
		$address = $this->System_Model_Address->getAddress($address_id);

		if (!$address) {
			return null;
		}

		if ($this->isLogged()) {
			//Associate this address to this customer
			if (!(int)$address['customer_id']) {
				$address['customer_id'] = $this->customer_id;
				$this->address->update($address_id, $address);
			} elseif ((int)$address['customer_id'] !== $this->customer_id) {
				trigger_error("Customer (id: $this->customer_id) attempted to access an unassociated address!");

				return null;
			}
		}

		return $address;
	}

	public function getAddresses($filter = array())
	{
		$filter['customer_ids'] = array($this->customer_id);

		$addresses = $this->System_Model_Address->getAddresses($filter);

		$payment_address_id = (int)$this->getMeta('default_payment_address_id');
		$shipping_address_id = (int)$this->getMeta('default_shipping_address_id');

		foreach ($addresses as &$address) {
			$address['default_payment'] = (int)$address['address_id'] === $payment_address_id;
			$address['default_shipping'] = (int)$address['address_id'] === $shipping_address_id;
		}
		unset($address);

		return $addresses;
	}

	public function getDefaultShippingAddress()
	{
		if (!empty($this->metadata['default_shipping_address_id'])) {
			return $this->getAddress($this->metadata['default_shipping_address_id']);
		}

		return null;
	}

	public function getDefaultPaymentAddress()
	{
		if (!empty($this->metadata['default_payment_address_id'])) {
			return $this->getAddress($this->metadata['default_payment_address_id']);
		}

		return null;
	}

	public function getShippingAddresses($filter = array())
	{
		$allowed_zones = $this->cart->getAllowedShippingZones();

		$defaults = array(
			'country_ids'  => array_column($allowed_zones, 'country_id'),
			'zone_ids'     => array_column($allowed_zones, 'zone_id'),
		);

		$addresses = $this->getAddresses($filter + $defaults);

		if (empty($filter) && !array_search_key('default_shipping', true, $addresses)) {
			//Reference first Address in array, then break loop
			foreach ($addresses as &$address) {
				$address['default_shipping'] = true;
				//$this->setMeta('default_shipping_address_id', $address['address_id']);
				break;
			}
			unset($address);
		}

		return $addresses;
	}

	public function getPaymentAddresses($filter = array())
	{
		$addresses = $this->getAddresses($filter);

		if (empty($filter) && !array_search_key('default_payment', true, $addresses)) {
			//Reference first Address in array, then break loop
			foreach ($addresses as &$address) {
				$address['default_payment'] = true;
				$this->setMeta('default_payment_address_id', $address['address_id']);
				break;
			}
			unset($address);
		}

		return $addresses;
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
		if (!$this->customer_id) {
			return 0;
		}

		return $this->db->queryVar("SELECT SUM(amount) AS total FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$this->customer_id . "'");
	}

	public function getRewardPoints()
	{
		if (!$this->customer_id) {
			return 0;
		}

		return $this->db->queryVar("SELECT SUM(points) AS total FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$this->customer_id . "'");
	}

	public function encrypt($password)
	{
		return md5($password);
	}

	private function setCustomer($customer)
	{
		if (empty($customer)) {
			return;
		}

		$this->customer_id                  = (int)$customer['customer_id'];
		$this->session->data['customer_id'] = $this->customer_id;
		$this->information                  = $customer;

		//Load Customer Settings
		$this->metadata = $this->System_Model_Customer->getMetaData($this->customer_id);

		$this->payment_info = $this->getMeta('payment_info_' . $this->information['payment_code']);

		if (!$this->payment_info) {
			$this->payment_info = array(
				'address_id'   => $this->information['address_id'],
				'payment_code' => $this->information['payment_code'],
			);
		}

		$cart     = $this->cart->getCart();
		$wishlist = $this->cart->getWishlist();

		$this->db->query("UPDATE " . DB_PREFIX . "customer SET cart = '" . $this->db->escape(serialize($cart)) . "', wishlist = '" . $this->db->escape(serialize($wishlist)) . "', ip = '" . $this->db->escape($_SERVER['REMOTE_ADDR']) . "' WHERE customer_id = '" . (int)$this->customer_id . "'");

		$ip_set = $this->db->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "customer_ip WHERE customer_id = '" . (int)$this->session->data['customer_id'] . "' AND ip = '" . $this->db->escape($_SERVER['REMOTE_ADDR']) . "'");

		if (!$ip_set) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "customer_ip SET customer_id = '" . $this->customer_id . "', ip = '" . $this->db->escape($_SERVER['REMOTE_ADDR']) . "', date_added = NOW()");
		}
	}

	public function emailRegistered($email)
	{
		return (int)$this->db->queryVar("SELECT customer_id FROM " . DB_PREFIX . "customer WHERE email = '" . $this->db->escape($email) . "'");
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
