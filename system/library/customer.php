<?php

class Customer extends Library
{
	protected $customer_id;
	protected $info;
	protected $metadata;

	public function __construct()
	{
		parent::__construct();

		if (!empty($_SESSION['customer']['customer_id'])) {
			if ($this->setCustomer($_SESSION['customer']['customer_id'])) {
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

		if (option('config_customer_approval')) {
			$where .= " AND approved = '1'";
		}

		$customer = $this->queryRow("SELECT * FROM " . self::$tables['customer'] . " WHERE $where LIMIT 1");

		if ($customer) {
			//AC_CUSTOMER_OVERRIDE allows for alternative login methods to function
			if ($password !== AC_CUSTOMER_OVERRIDE) {
				if (!password_verify($password, $customer['password'])) {
					$this->error['password'] = _l("Login failed. Invalid username and / or password.");
					return false;
				}
			}

			$this->setCustomer($customer);

			return true;
		}

		$this->error['username'] = _l("Login failed. Invalid username and / or password.");

		return false;
	}

	public function logout()
	{
		unset($_SESSION['customer']);

		$this->customer_id = null;

		$this->info = array();
	}

	public function getId()
	{
		return $this->customer_id;
	}

	public function setCustomerOverride($customer)
	{
		if (user_can('w', 'admin/customer')) {
			$this->setCustomer($customer, true);
		}
	}

	protected function setCustomer($customer, $ignore_status = false)
	{
		if (!is_array($customer)) {
			$customer = $this->queryRow("SELECT * FROM " . self::$tables['customer'] . " WHERE customer_id = '" . (int)$customer . "'" . ($ignore_status ? '' : " AND status = '1'"));
		}

		if (empty($customer)) {
			return false;
		}

		$this->customer_id                   = (int)$customer['customer_id'];
		$_SESSION['customer']['customer_id'] = $this->customer_id;
		$this->info                          = $customer;

		$this->displayMessages();

		//Load Customer Settings
		$this->metadata = $this->Model_Customer->getMeta($this->customer_id);

		return true;
	}

	//TODO: Need to move customer database calls to library. This will resolve the customer_id issue.
	public function setId($customer_id)
	{
		$this->customer_id = $customer_id;
	}

	/**
	 * The same as Customer::add() except for an additional validation checks and auto sign in.
	 *
	 * @param $customer - Customer Account Data
	 */

	public function register($customer)
	{
		if (option('config_account_terms_page_id')) {
			$page_info = $this->Model_Page->getPage(option('config_account_terms_page_id'));

			if ($page_info && !isset($customer['agree'])) {
				$this->error['agree'] = _l("You must agree to the %s!", $page_info['title']);
			}
		}

		$customer_id = $this->Model_Customer->save(null, $customer);

		$this->setCustomer($customer_id);

		return $customer_id;
	}

	public function meta($key = null, $default = null)
	{
		if ($key) {
			return isset($this->metadata[$key]) ? $this->metadata[$key] : $default;
		}

		return $this->metadata;
	}

	public function setMeta($key, $value)
	{
		$this->Model_Customer->deleteMeta($key);

		$this->metadata[$key] = $value;

		return $this->Model_Customer->addMeta($key, $value);
	}

	public function removeMeta($key)
	{
		unset($this->metadata[$key]);

		return $this->Model_Customer->deleteMeta($key);
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
			$customer_id = $this->queryVar("SELECT customer_id FROM " . self::$tables['customer_address'] . " WHERE address_id = " . (int)$address_id . " LIMIT 1");

			if ($customer_id && $customer_id != $this->customer_id) {
				trigger_error("Customer (id: $this->customer_id) attempted to access an unassociated address!");

				return null;
			}
		}

		return $address;
	}

	public function getAddresses($filter = array())
	{
		if (!isset($filter['customer_ids']) && $this->customer_id) {
			$filter['customer_ids'] = array($this->customer_id);
		}

		return $this->address->getAddresses($filter);
	}

	public function removeAddress($address_id)
	{
		if ($this->getTotalAddresses() === 1) {
			$this->error['warning'] = _l("Must have at least 1 address associated to your account!");
		}

		if ((int)$this->meta('default_shipping_address_id') === (int)$address_id) {
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
		return (int)$this->queryVar("SELECT COUNT(*) FROM " . self::$tables['customer_address'] . " WHERE customer_id = " . (int)$this->customer_id);
	}

	/** Customer Info **/

	public function info($key = null)
	{
		if ($key) {
			return isset($this->info[$key]) ? $this->info[$key] : null;
		}

		return $this->info;
	}

	public function getIps($customer_id)
	{
		return $this->queryRows("SELECT * FROM `" . self::$tables['customer_ip'] . "` WHERE customer_id = " . (int)$customer_id);
	}

	/** Tools **/

	/**
	 * Displays the messages sent to the customer using sendMessage()
	 */

	public function displayMessages()
	{
		$messages = $this->queryColumn("SELECT value FROM " . self::$tables['customer_meta'] . " WHERE customer_id = " . (int)$this->customer_id . " AND `key` = 'message'");

		foreach ($messages as $message) {
			message('notify', _l($message));
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
			message('notify', _l($msg));
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

	public function canDoAction($action)
	{
		$private = false;

		$class  = $action->getClass();
		$method = $action->getMethod();

		if (property_exists($class, 'allow')) {
			$allow = $class::$allow;

			if (!empty($allow['access'])) {
				if (is_string($allow['access'])) {
					$private = preg_match("/$allow[access]/", $method);
				} else {
					$private = in_array($method, $allow['access']);
				}
			}
		}

		if ($private) {
			return $this->isLogged();
		}

		return true;
	}

	public function encrypt($password)
	{
		return password_hash($password, PASSWORD_DEFAULT, array('cost' => PASSWORD_COST));
	}

	public function generatePassword()
	{
		return substr(str_shuffle(MD5(microtime())), 0, (int)rand(10, 13));
	}

	protected function track()
	{
		if (!$this->customer_id) {
			return;
		}

		$ip_set = $this->queryVar("SELECT COUNT(*) FROM " . self::$tables['customer_ip'] . " WHERE customer_id = '" . (int)$this->customer_id . "' AND ip = '" . $this->escape($_SERVER['REMOTE_ADDR']) . "'");

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
		return (int)$this->queryVar("SELECT customer_id FROM " . self::$tables['customer'] . " WHERE email = '" . $this->escape($email) . "'");
	}

	public function setResetCode($email, $code)
	{
		$customer_id = $this->emailRegistered($email);

		if ($customer_id) {
			$this->customer_id = $customer_id;

			return $this->setMeta('pass_reset_code', $code);
		}

		if (!validate('email', $email)) {
			$this->error['email_invalid'] = _l("The email %s is not a valid email address", $email);
		} else {
			$this->error['email'] = _l("The email %s is not a registered email address.", $email);
		}

		return false;
	}

	public function lookupResetCode($code)
	{
		return $this->queryVar("SELECT customer_id FROM " . self::$tables['customer_meta'] . " WHERE `key` = 'pass_reset_code' AND `value` = '" . $this->escape($code) . "' LIMIT 1");
	}

	public function clearResetCode()
	{
		return $this->deleteMeta('pass_reset_code');
	}

	public function generateCode()
	{
		return str_shuffle(md5(microtime(true) * rand()));
	}

	public function isBlacklisted($customer_id = null, $ips = array())
	{
		if (!$customer_id) {
			$customer_id = $this->customer_id;
		}

		if ($customer_id) {
			$customer_ips = $this->queryColumn("SELECT ip FROM " . self::$tables['customer_ip'] . " WHERE customer_id = " . (int)$customer_id);

			if ($customer_ips) {
				$ips += $customer_ips;
			}
		}

		if (empty($ips)) {
			return false;
		}

		return $this->queryVar("SELECT COUNT(*) FROM `" . self::$tables['customer_ip_blacklist'] . "` WHERE ip IN ('" . implode("','", $ips) . "')");
	}
}
