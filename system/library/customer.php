<?php

class Customer extends Library
{
	protected $customer_id;
	protected $info = array();
	protected $meta = array();

	public function __construct()
	{
		parent::__construct();

		if (!empty($_SESSION['customer_id'])) {
			if ($this->setCustomer($_SESSION['customer_id'])) {
				$this->track();
			} else {
				$this->logout();
			}
		} elseif (defined("AMPLO_SECRET_KEY")) {
			$cookie = _cookie('customer');

			if ($cookie) {
				$cookie = json_decode($cookie, true);

				if (isset($cookie['customer_id'], $cookie['signature'], $cookie['username'])) {
					$signature = hash_hmac('sha256', $cookie['customer_id'] . '-' . $cookie['username'], AMPLO_SECRET_KEY);

					if ($cookie['signature'] === $signature) {
						$this->setCustomer($cookie['customer_id']);
					}
				}
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

		$customer = $this->queryRow("SELECT * FROM {$this->t['customer']} WHERE $where LIMIT 1");

		if ($customer) {
			//AC_CUSTOMER_OVERRIDE allows for alternative login methods to function
			if ($password !== AC_CUSTOMER_OVERRIDE) {
				if (!password_verify($password, $customer['password'])) {
					$this->error['password'] = _l("Login failed. Invalid username and / or password.");

					return false;
				}
			}

			//Save Customer cookie for remembering customer login
			if (defined("AMPLO_SECRET_KEY")) {
				$cookie = array(
					'customer_id' => $customer['customer_id'],
					'username'    => $customer['username'],
					'signature'   => hash_hmac('sha256', $customer['customer_id'] . '-' . $customer['username'], AMPLO_SECRET_KEY),
				);

				set_cookie('customer', json_encode($cookie), option('customer_cookie_expire', AMPLO_USER_EXPIRE));
			}

			return $this->setCustomer($customer);
		}

		$this->error['username'] = _l("Login failed. Invalid username and / or password.");

		return false;
	}

	public function logout()
	{
		$this->session->endTokenSession();

		set_cookie('customer', null);

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
			$this->setCustomer($customer);
		}
	}

	public function setCustomer($customer)
	{
		if (!is_array($customer)) {
			$customer = $this->Model_Customer->getRecord($customer);
		}

		if (empty($customer)) {
			$this->error['customer'] = _l("Customer was not found.");

			return false;
		}

		$customer['name'] = trim($customer['first_name'] . ' ' . $customer['last_name']);

		$this->customer_id       = (int)$customer['customer_id'];
		$_SESSION['customer_id'] = $this->customer_id;
		$this->info              = $customer;

		$this->displayMessages();

		//Load Customer Settings
		$this->meta = $this->Model_Customer->getMeta($this->customer_id);

		return true;
	}

	/**
	 * The same as Customer::add() except for an additional validation checks and auto sign in.
	 *
	 * @param $customer - Customer Account Data
	 */

	public function register($customer, $login = true)
	{
		if ($terms_page_id = option('terms_agreement_page_id')) {
			$page = $this->Model_Page->getRecord($terms_page_id);

			if ($page && !isset($customer['agree'])) {
				$this->error['agree'] = _l("You must agree to the <a href=\"%s\">%s</a>", site_url('page', 'page_id=' . $terms_page_id), $page['title']);

				return false;
			}
		}

		$customer_id = $this->Model_Customer->save(null, $customer);

		if ($customer_id) {
			if ($login) {
				$this->setCustomer($customer_id);
			}

			if ($terms_page_id) {
				$this->agreedToTerms();
			}
		} else {
			$this->error = $this->Model_Customer->fetchError();
		}

		return $customer_id;
	}

	public function agreedToTerms()
	{
		$date = $this->date->now();

		set_cookie('terms_agreed_date', $date);

		if (is_logged()) {
			set_customer_meta('terms_agreed_date', $date);
		}
	}

	public function meta($key = null, $default = null)
	{
		if ($key) {
			return isset($this->meta[$key]) ? $this->meta[$key] : $default;
		}

		return $this->meta;
	}

	public function setMeta($key, $value)
	{
		$this->removeMeta($key);

		$this->meta[$key] = $value;

		$meta_id = $this->Model_Customer->addMeta($this->customer_id, $key, $value);

		if (!$meta_id) {
			$this->error = $this->Model_Customer->fetchError();
		}

		return $meta_id;
	}

	public function removeMeta($key)
	{
		unset($this->meta[$key]);

		return $this->Model_Customer->deleteMeta($this->customer_id, $key);
	}

	/** Customer Info **/

	public function info($key = null, $default = null)
	{
		if ($key) {
			return isset($this->info[$key]) ? $this->info[$key] : $default;
		}

		return $this->info;
	}

	public function setInfo($key, $value)
	{
		if ($this->customer_id) {
			if ($this->Model_Customer->save($this->customer_id, array($key => $value))) {
				$this->info[$key] = $value;
			}
		}

		return false;
	}

	public function getIps($customer_id)
	{
		return $this->queryRows("SELECT * FROM `{$this->t['customer_ip']}` WHERE customer_id = " . (int)$customer_id);
	}

	/** Tools **/

	/**
	 * Displays the messages sent to the customer using sendMessage()
	 */

	public function displayMessages()
	{
		$messages = $this->queryColumn("SELECT value FROM {$this->t['customer_meta']} WHERE customer_id = " . (int)$this->customer_id . " AND `key` = 'message'");

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
	 * Sends a message to the customer. If the customer is not logged in and not currently browsing, the message will be
	 * delivered the next time the customer logs in.
	 *
	 * @param $customer_id - the customer to send the message to
	 * @param $msg         - the message. This will be translated into the customer's language, so DO NOT translate -
	 *                     aka do not use _l() - when using sendMessage()
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

		$ip = $_SERVER['REMOTE_ADDR'];

		$ip_set = $this->queryVar("SELECT COUNT(*) FROM {$this->t['customer_ip']} WHERE customer_id = '" . (int)$this->customer_id . "' AND ip = '" . $this->escape($ip) . "'");

		if (!$ip_set) {
			$customer_ip = array(
				'customer_id' => $this->customer_id,
				'ip'          => $ip,
				'date_added'  => $this->date->now(),
			);

			$this->insert('customer_ip', $customer_ip);
		}
	}

	public function emailRegistered($email)
	{
		return (int)$this->queryVar("SELECT customer_id FROM {$this->t['customer']} WHERE email = '" . $this->escape($email) . "'");
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
		return $this->queryVar("SELECT customer_id FROM {$this->t['customer_meta']} WHERE `key` = 'pass_reset_code' AND `value` = '" . $this->escape($code) . "' LIMIT 1");
	}

	public function clearResetCode($customer_id)
	{
		return $this->Model_Customer->deleteMeta($customer_id, 'pass_reset_code');
	}

	public function generateCode()
	{
		return str_shuffle(md5(microtime(true) * rand()));
	}
}
