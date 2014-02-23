<?php

class Customer extends Library
{
	private $customer_id;
	private $information;
	private $metadata;

	public function __construct($registry)
	{
		$registry->set('customer', $this);
		parent::__construct($registry);

		if (isset($this->session->data['customer_id'])) {
			if ($this->setCustomer($this->session->data['customer_id'])) {
				$this->track();
			} else {
				$this->logout();
			}
		}
	}

	public function isLogged()
	{
		return $this->customer_id ? true : false;
	}

	public function login($email, $password)
	{
		$where = "LOWER(email) = '" . $this->escape(strtolower($email)) . "' AND status = '1'";

		if ($this->config->get('config_customer_approval')) {
			$where .= " AND approved = '1'";
		}

		$customer = $this->queryRow("SELECT * FROM " . DB_PREFIX . "customer WHERE $where LIMIT 1");

		if ($customer) {
			//AC_CUSTOMER_OVERRIDE allows for alternative login methods to function
			if ($password !== AC_CUSTOMER_OVERRIDE) {
				if (!password_verify($password, $customer['password'])) {
					return false;
				}
			}

			$this->setCustomer($customer);

			$this->loadCart($customer);

			return true;
		}

		return false;
	}

	public function logout()
	{
		$this->query("UPDATE " . DB_PREFIX . "customer SET cart = '" . $this->escape(isset($this->session->data['cart']) ? serialize($this->session->data['cart']) : '') . "', wishlist = '" . $this->escape(isset($this->session->data['wishlist']) ? serialize($this->session->data['wishlist']) : '') . "' WHERE customer_id = '" . (int)$this->customer_id . "'");

		$this->session->end();

		$this->customer_id = null;

		$this->information = array();

		//TODO: Add as option in admin panel $this->config->get('config_logout_message');
		$this->message->add('notify', "Logged Out");
	}

	public function getId()
	{
		return $this->customer_id;
	}

	/** Customer Data **/

	public function add($customer)
	{
		$customer['store_id']          = $this->config->get('config_store_id');
		$customer['customer_group_id'] = $this->config->get('config_customer_group_id');
		$customer['date_added']        = $this->date->now();
		$customer['status']            = 1;

		if (!isset($customer['newsletter'])) {
			$customer['newsletter'] = 0;
		}

		if (empty($customer['password'])) {
			$customer['no_password_set'] = true;
			$customer['password']        = $this->generatePassword();
		}

		$customer['password'] = $this->encrypt($customer['password']);

		$customer['approved'] = $this->config->get('config_customer_approval') ? 1 : 0;

		$customer_id = $this->insert('customer', $customer);

		//Address will be extracted from customer information, if it exists
		$this->addAddress($customer, $customer_id);

		//Customer MetaData
		if (!empty($customer['metadata'])) {
			foreach ($customer['metadata'] as $key => $value) {
				$this->setMeta($key, $value);
			}
		}

		$customer['customer_id'] = $customer_id;

		$this->mail->sendTemplate('new_customer', $customer);

		return $customer_id;
	}

	public function edit($data)
	{
		//Editing password here is not allowed. Must use customer->editPassword()
		unset($data['password']);

		$customer_id = !empty($data['customer_id']) ? $data['customer_id'] : $this->customer_id;

		$this->update('customer', $data, $customer_id);

		if (!empty($data['metadata'])) {
			foreach ($data['metadata'] as $key => $value) {
				$this->setMeta($key, $value);
			}
		}
	}

	public function editPassword($customer_id, $password)
	{
		$this->update('customer', array('password' => $this->customer->encrypt($password)), $customer_id);
	}

	public function getCustomerGroupId()
	{
		if (!empty($this->information['customer_group_id'])) {
			return $this->information['customer_group_id'];
		}

		return (int)$this->config->get('config_customer_group_id');
	}

	public function getCustomer($customer_id)
	{
		return $this->queryRow("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = " . (int)$customer_id);
	}

	public function getCustomerByToken($token)
	{
		$customer = $this->queryRow("SELECT * FROM " . DB_PREFIX . "customer WHERE token = '" . $this->escape($token) . "' AND token != ''");

		//Unset the 1 time access token
		$this->update('customer', array('token' => ''), $customer['customer_id']);

		return $customer;
	}

	public function getCustomers($data = array(), $select = '', $total = false)
	{
		if ($total) {
			$select = "COUNT(*) as total";
		} elseif (!$select) {
			$select = "*, CONCAT(c.firstname, ' ', c.lastname) AS name, cg.name AS customer_group";
		}

		$from = DB_PREFIX . "customer c" .
			" LEFT JOIN" . DB_PREFIX . "customer_group cg ON (c.customer_group_id = cg.customer_group_id)";

		$where = "1";

		if (!empty($data['name'])) {
			$where .= " AND LCASE(CONCAT(c.firstname, ' ', c.lastname)) LIKE '%" . $this->escape(strtolower($data['name'])) . "%'";
		}

		if (!empty($data['email'])) {
			$where .= " AND c.email = '" . $this->escape($data['email']) . "'";
		}

		if (!empty($data['customer_group_ids'])) {
			$where .= " AND cg.customer_group_id IN (" . implode(',', $this->escape($data['customer_group_ids'])) . ")";
		}

		if (isset($data['status'])) {
			$where .= " AND c.status = " . $data['status'] ? 1 : 0;
		}

		if (isset($data['approved'])) {
			$where .= " AND c.approved = " . $data['approved'] ? 1 : 0;
		}

		if (!empty($data['ip'])) {
			$where .= " AND c.customer_id IN (SELECT customer_id FROM " . DB_PREFIX . "customer_ip WHERE ip = '" . $this->escape($data['ip']) . "')";
		}

		if (!empty($data['date_added'])) {
			$where .= " AND DATE(c.date_added) = DATE('" . $this->escape($data['date_added']) . "')";
		}

		//Order By and Limit
		if (!$total) {
			$order = $this->extractOrder($data);
			$limit = $this->extractLimit($data);
		} else {
			$order = '';
			$limit = '';
		}

		$query = "SELECT $select FROM $from WHERE $where $order $limit";

		$result = $this->query($query);

		if ($total) {
			return $result['total'];
		}

		return $result->rows;
	}

	/** Customer Meta Data **/

	public function getMeta($key = null)
	{
		if ($key === null) {
			return $this->metadata;
		}

		return isset($this->metadata[$key]) ? $this->metadata[$key] : null;
	}

	public function setMeta($key, $value)
	{
		if (!$this->customer_id) {
			return;
		}

		$where = array(
			'customer_id' => $this->customer_id,
			'key'         => $key,
		);

		$this->delete('customer_meta', $where);

		if (is_object($value) || is_array($value) || is_resource($value)) {
			$value      = serialize($value);
			$serialized = 1;
		} else {
			$serialized = 0;
		}

		$customer_meta = array(
			'customer_id' => $this->customer_id,
			'key'         => $key,
			'value'       => $value,
			'serialized'  => $serialized,
		);

		$this->insert('customer_meta', $customer_meta);

		$this->metadata[$key] = $value;
	}

	public function deleteMeta($key)
	{
		$where = array(
			'customer_id' => $this->customer_id,
			'key'         => $key,
		);

		$this->delete('customer_meta', $where);
	}

	/** Addresses **/

	public function addAddress($address)
	{
		$address_id = $this->address->add($address);

		if ($address_id) {
			if (!$this->customer_id) {
				return $address_id;
			}

			//Associate address to customer
			$customer_address = array(
				'customer_id' => $this->customer_id,
				'address_id'  => $address_id,
			);

			$this->insert('customer_address', $customer_address);

			return $address_id;
		}

		$this->error = $this->address->getError();

		return false;
	}

	public function editAddress($address_id, $address)
	{
		if (!$this->customer_id) {
			$this->error['login'] = _l("Must be logged in to add an address to your account");
			return false;
		}

		if (!$this->address->edit($address_id, $address)) {
			$this->error = $this->address->getError();

			return false;
		}

		return true;
	}

	public function getAddress($address_id)
	{
		$address = $this->address->getAddress($address_id);

		if (!$address) {
			return null;
		}

		if ($this->isLogged()) {
			$customer_id = $this->queryVar("SELECT customer_id FROM " . DB_PREFIX . "customer_address WHERE address_id = " . (int)$address_id . " LIMIT 1");

			if ($customer_id && $customer_id != $this->customer_id) {
				trigger_error("Customer (id: $this->customer_id) attempted to access an unassociated address!");

				return null;
			}
		}

		return $address;
	}

	public function getAddresses($filter = array())
	{
		if (!isset($filter['customer_ids'])) {
			$filter['customer_ids'] = array($this->customer_id);
		}

		$addresses = $this->address->getAddresses($filter);

		$payment_address_id  = (int)$this->getMeta('default_payment_address_id');
		$shipping_address_id = (int)$this->getMeta('default_shipping_address_id');

		if (!$payment_address_id) {
			$address            = current($addresses);
			$payment_address_id = $address['address_id'];
			$this->setDefaultPaymentAddress($payment_address_id);
		}

		if (!$shipping_address_id) {
			$address             = current($addresses);
			$shipping_address_id = $address['address_id'];
			$this->setDefaultPaymentAddress($shipping_address_id);
		}

		foreach ($addresses as &$address) {
			$address['default_payment']  = (int)$address['address_id'] === $payment_address_id;
			$address['default_shipping'] = (int)$address['address_id'] === $shipping_address_id;
		}
		unset($address);

		return $addresses;
	}

	public function setDefaultShippingAddress($address_id)
	{
		$this->setMeta('default_shipping_address_id', $address_id);
	}

	public function setDefaultPaymentAddress($address_id)
	{
		$this->setMeta('default_payment_address_id', $address_id);
	}

	public function getDefaultShippingAddressId()
	{
		return $this->getMeta('default_shipping_address_id');
	}

	public function getDefaultPaymentAddressId()
	{
		return $this->getMeta('default_shipping_address_id');
	}

	public function getDefaultShippingAddress()
	{
		if (!$this->customer_id) {
			return null;
		}

		$address_id = $this->getMeta('default_shipping_address_id');

		if ($address_id) {
			$address = $this->getAddress($address_id);

			if ($address) {
				return $address;
			}
		}

		$first_address_id = $this->queryVar("SELECT address_id FROM " . DB_PREFIX . "customer_address WHERE customer_id = " . (int)$this->customer_id . " LIMIT 1");

		if ($first_address_id) {
			$this->setDefaultShippingAddress($first_address_id);

			return $this->getAddress($first_address_id);
		}

		return null;
	}

	public function getDefaultPaymentAddress()
	{
		if (!$this->customer_id) {
			return null;
		}

		$address_id = $this->getMeta('default_payment_address_id');

		if ($address_id) {
			$address = $this->getAddress($address_id);

			if ($address) {
				return $address;
			}
		}

		$first_address_id = $this->queryVar("SELECT address_id FROM " . DB_PREFIX . "customer_address WHERE customer_id = " . (int)$this->customer_id . " LIMIT 1");

		if ($first_address_id) {
			$this->setDefaultPaymentAddress($first_address_id);

			return $this->getAddress($first_address_id);
		}

		return null;
	}

	public function getShippingAddresses($filter = array())
	{
		$allowed_zones = $this->cart->getAllowedShippingZones();

		$defaults = array(
			'country_ids' => array_column_recursive($allowed_zones, 'country_id'),
			'zone_ids'    => array_column_recursive($allowed_zones, 'zone_id'),
		);

		$addresses = $this->getAddresses($filter + $defaults);

		if (empty($filter) && !array_search_key('default_shipping', true, $addresses)) {
			//Reference first Address in array, then break loop
			foreach ($addresses as &$address) {
				$address['default_shipping'] = true;
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

	public function removeAddress($address_id)
	{
		if ($this->getTotalAddresses() === 1) {
			$this->error['warning'] = _l("Must have at least 1 address associated to your account!");
		}

		if ((int)$this->customer->getMeta('default_shipping_address_id') === (int)$address_id) {
			$this->error['warning'] = _l("Cannot remove the default shipping address! Please change your default shipping address first.");
		}

		if (!$this->error) {
			$where = array(
				'customer_id' => $this->customer_id,
				'address_id'  => $address_id,
			);

			$this->delete('customer_address', $where);
			return true;
		}

		return false;
	}

	public function getTotalAddresses()
	{
		return (int)$this->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "customer_address WHERE customer_id = " . (int)$this->customer_id);
	}

	/** Customer Info **/

	public function info($key = null)
	{
		if ($key && isset($this->information[$key])) {
			return $this->information[$key];
		}

		return $this->information;
	}

	public function getOrders()
	{
		if ($this->customer_id) {
			return $this->queryRows("SELECT * FROM " . DB_PREFIX . "order WHERE customer_id = " . (int)$this->customer_id . " AND order_status_id > 0");
		}

		return false;
	}

	public function getBalance()
	{
		if ($this->customer_id) {
			return $this->queryVar("SELECT SUM(amount) AS total FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = " . (int)$this->customer_id);
		}

		return 0;
	}

	public function getRewardPoints()
	{
		if ($this->customer_id) {
			return $this->queryVar("SELECT SUM(points) AS total FROM " . DB_PREFIX . "customer_reward WHERE customer_id = " . (int)$this->customer_id);
		}

		return 0;
	}

	public function getIps($customer_id)
	{
		return $this->queryRows("SELECT * FROM `" . DB_PREFIX . "customer_ip` WHERE customer_id = " . (int)$customer_id);
	}

	/** Tools **/

	/**
	 * Displays the messages sent to the customer using sendMessage()
	 */

	public function displayMessages()
	{
		$messages = $this->queryColumn("SELECT value FROM " . DB_PREFIX . "customer_meta WHERE customer_id = " . (int)$this->customer_id . " AND `key` = 'message'");

		foreach ($messages as $message) {
			$this->message->add('notify', _l($message));
		}

		$where = array(
			'customer_id' => $this->customer_id,
			'key'         => 'message',
		);

		$this->delete('customer_meta', $where);
	}

	/**
	 * Sends a message to the customer. If the customer is not logged in and not currently browsing, the message will be delivered the next
	 * time the customer logs in.
	 *
	 * @param $customer_id - the customer to send the message to
	 * @param $msg - the message. This will be translated into the customer's language, so DO NOT translate - aka do not use _l() - when using sendMessage()
	 */

	public function sendMessage($customer_id, $msg)
	{
		if (!$customer_id) {
			return;
		}

		if ($this->customer_id && (int)$customer_id === (int)$this->customer_id) {
			$this->message->add('notify', _l($msg));
			return;
		}

		$meta = array(
			'customer_id' => $customer_id,
			'key'         => 'message',
			'value'       => $msg,
			'serialized'  => 0,
		);

		$this->insert('customer_meta', $meta);
	}

	public function encrypt($password)
	{
		return password_hash($password, PASSWORD_DEFAULT, array('cost' => PASSWORD_COST));
	}

	public function generatePassword()
	{
		return substr(str_shuffle(MD5(microtime())), 0, (int)rand(10, 13));
	}

	public function setCustomerOverride($customer, $ignore_status = true)
	{
		if ($this->user->can('modify', 'customer')) {
			$this->setCustomer($customer, true);
		}
	}

	private function setCustomer($customer, $ignore_status = false)
	{
		if (!is_array($customer)) {
			$customer = $this->queryRow("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer . "'" . ($ignore_status ? '' : " AND status = '1'"));
		}

		if (empty($customer)) {
			return false;
		}

		$this->customer_id = (int)$customer['customer_id'];
		$this->session->set('customer_id', $this->customer_id);
		$this->information = $customer;

		$this->displayMessages();

		//Load Customer Settings
		$metadata = $this->queryRows("SELECT * FROM " . DB_PREFIX . "customer_meta WHERE customer_id = " . $this->customer_id);

		$this->metadata = array();

		foreach ($metadata as $meta) {
			$this->metadata[$meta['key']] = $meta['serialized'] ? unserialize($meta['value']) : $meta['value'];
		}

		return true;
	}

	public function loadCart($customer)
	{
		if (!empty($customer['cart'])) {
			$this->cart->merge($customer['cart']);
		}

		if (!empty($customer['wishlist'])) {
			$this->cart->mergeWishlist($customer['wishlist']);
		}

		$this->order->synchronizeOrders($customer);
	}

	private function track()
	{
		if (!$this->customer_id) {
			return;
		}

		$ip_set = $this->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "customer_ip WHERE customer_id = '" . (int)$this->customer_id . "' AND ip = '" . $this->escape($_SERVER['REMOTE_ADDR']) . "'");

		if (!$ip_set) {
			$customer_ip = array(
				'customer_id' => $this->customer_id,
				'ip'          => $_SERVER['REMOTE_ADDR'],
				'date_added'  => $this->date->now(),
			);

			$this->insert('customer_ip', $customer_ip);
		}
	}

	public function emailRegistered($email)
	{
		return (int)$this->queryVar("SELECT customer_id FROM " . DB_PREFIX . "customer WHERE email = '" . $this->escape($email) . "'");
	}

	public function setCode($email, $code)
	{
		if (!$this->customer_id) {
			$customer_id = $this->emailRegistered($email);

			if ($customer_id) {
				$this->customer_id = $customer_id;

				$this->setMeta('password_reset_code', $code);

				$this->customer_id = null;
			}
		}
	}

	public function lookupCode($code)
	{
		if ($code) {
			$query = "SELECT c.* FROM " . DB_PREFIX . "customer c" .
				" JOIN " . DB_PREFIX . "customer_meta cm ON (cm.customer_id=c.customer_id)" .
				" WHERE `key` = 'password_reset_code' AND 'value' = '" . $this->escape($code) . "' LIMIT 1";

			return $this->queryRow($query);
		}
	}

	public function generateCode()
	{
		return str_shuffle(md5(microtime(true) * rand()));
	}

	public function clearCode($customer_id)
	{
		$where = array(
			'customer_id' => $customer_id,
			'key'         => "password_reset_code",
		);

		$this->delete('customer_meta', $where);
	}

	public function isBlacklisted($customer_id = null, $ips = array())
	{
		if (!$customer_id) {
			$customer_id = $this->customer_id;
		}

		if ($customer_id) {
			$customer_ips = $this->queryColumn("SELECT ip FROM " . DB_PREFIX . "customer_ip WHERE customer_id = " . (int)$customer_id);

			if ($customer_ips) {
				$ips += $customer_ips;
			}
		}

		if (empty($ips)) {
			return false;
		}

		return $this->queryVar("SELECT COUNT(*) FROM `" . DB_PREFIX . "customer_ip_blacklist` WHERE ip IN ('" . implode("','", $ips) . "')");
	}
}
