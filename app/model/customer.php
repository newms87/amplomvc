<?php
class App_Model_Customer extends App_Model_Table
{
	protected $table = 'customer', $primary_key = 'customer_id';

	public function save($customer_id, $customer)
	{
		if (isset($customer['name']) && !isset($customer['firstname'])) {
			$name_parts            = explode(' ', $customer['name'], 2);
			$customer['firstname'] = $name_parts[0];

			if (isset($name_parts[1])) {
				$customer['lastname'] = $name_parts[1];
			}
		}

		if (isset($customer['email'])) {
			if (!validate('email', $customer['email'])) {
				$this->error['email'] = $this->validation->getError();
			} elseif ($this->customer->emailRegistered($customer['email'])) {
				$this->error['email'] = _l("Warning: E-Mail Address is already registered!");
			}
		} elseif (!$customer_id) {
			$this->error['email'] = _l("Your email address is required.");
		}

		if ((isset($customer['zone_id']) || isset($customer['country_id'])) && !$this->address->validate($customer)) {
			$this->error += $this->address->getError();
		}

		if (!empty($data['telephone']) && !validate('phone', $data['telephone'])) {
			$this->error['telephone'] = _l("The phone number you provided is invalid.");
		}

		if (isset($customer['password'])) {
			if (!validate('password', $customer['password'])) {
				$this->error['password'] = $this->validation->getError();
			} elseif (isset($customer['confirm']) && $customer['confirm'] !== $customer['password']) {
				$this->error['confirm'] = _l("Your password and confirmation do not match.");
			}
		} elseif (!$customer_id) {
			$customer['no_password_set'] = true;
			$customer['password']        = $this->generatePassword();
		}

		if ($this->error) {
			return false;
		}

		$customer['customer_group_id'] = option('config_customer_group_id');
		$customer['date_added']        = $this->date->now();
		$customer['status']            = 1;

		if (!isset($customer['newsletter'])) {
			$customer['newsletter'] = option('config_customer_newsletter', 0);
		}

		$customer['password'] = $this->customer->encrypt($customer['password']);

		$customer['approved'] = option('config_customer_approval') ? 1 : 0;

		if ($customer_id) {
			$this->update('customer', $customer, $customer_id);
		} else {
			$customer_id = $this->insert('customer', $customer);
		}

		//Address will be extracted from customer information, if it exists
		$this->saveAddress($customer_id, null, $customer);

		//Customer MetaData
		if (!empty($customer['metadata'])) {
			foreach ($customer['metadata'] as $key => $value) {
				$this->setMeta($customer_id, $key, $value);
			}
		}

		$customer['customer_id'] = $customer_id;

		call('mail/new_customer', $customer);

		return $customer_id;
	}

	public function getCustomerGroups()
	{
		return $this->queryRows("SELECT * FROM " . self::$tables['customer_group']);
	}

	/** Customer Meta Data **/
	public function addMeta($customer_id, $key, $value)
	{
		if (!$customer_id) {
			$this->error['customer_id'] = _l("The customer does not exist.");
			return false;
		}

		if (_is_object($value)) {
			$value      = serialize($value);
			$serialized = 1;
		} else {
			$serialized = 0;
		}

		$customer_meta = array(
			'customer_id' => $customer_id,
			'key'         => $key,
			'value'       => $value,
			'serialized'  => $serialized,
		);

		$this->metadata[$key] = $value;

		return $this->insert('customer_meta', $customer_meta);
	}

	public function setMeta($customer_id, $key, $value)
	{
		$this->deleteMeta($customer_id, $key);

		return $this->addMeta($customer_id, $key, $value);
	}

	public function getMeta($customer_id)
	{
		$rows = $this->queryRows("SELECT * FROM " . self::$tables['customer_meta'] . " WHERE customer_id = " . (int)$customer_id);

		$meta = array();

		foreach ($rows as $row) {
			$meta[$row['key']] = $row['serialized'] ? unserialize($row['value']) : $row['value'];
		}

		return $meta;
	}

	public function deleteMeta($customer_id, $key)
	{
		$where = array(
			'customer_id' => $customer_id,
			'key'         => $key,
		);

		$this->delete('customer_meta', $where);

		unset($this->metadata[$key]);

		return true;
	}

	/** Addresses **/

	public function customerHasAddress($customer_id, $address_id)
	{
		return $this->queryVar("SELECT COUNT(*) FROM " . self::$tables['customer_address'] . " WHERE address_id = " . (int)$address_id . " AND customer_id = " . (int)$customer_id);
	}

	public function saveAddress($customer_id, $address_id, $address)
	{
		if (!$address_id) {
			$address_id = $this->address->add($address);

			if (!$address_id) {
				$this->error = $this->address->getError();
				return false;
			}
		} else {
			if (!$this->address->edit($address_id, $address)) {
				$this->error = $this->address->getError();
				return false;
			}
		}

		if (!$customer_id) {
			return $address_id;
		}

		//Associate address to customer
		if (!$this->customerHasAddress($customer_id, $address_id)) {
			$customer_address = array(
				'customer_id' => $customer_id,
				'address_id'  => $address_id,
			);

			$this->insert('customer_address', $customer_address);
		}

		return $address_id;
	}

	public function getAddress($customer_id, $address_id)
	{
		$address = $this->address->getAddress($address_id);

		if (!$address) {
			return null;
		}

		if ($this->isLogged()) {
			$address_customer_id = $this->queryVar("SELECT customer_id FROM " . self::$tables['customer_address'] . " WHERE address_id = " . (int)$address_id . " LIMIT 1");

			if ($customer_id && $customer_id != $address_customer_id) {
				write_log('security', _l("Customer %s attempted to access unowned address with ID %s", $customer_id, $address_id));
				return null;
			}
		}

		return $address;
	}

	public function getAddresses($customer_id, $filter = array())
	{
		if (!isset($filter['customer_ids']) && $customer_id) {
			$filter['customer_ids'] = array($customer_id);
		}

		return $this->address->getAddresses($filter);
	}

	public function removeAddress($customer_id, $address_id)
	{
		if ($this->getTotalAddresses($customer_id) <= 1) {
			$this->error['warning'] = _l("Must have at least 1 address associated to your account!");
		}

		if ((int)$this->meta('default_shipping_address_id') === (int)$address_id) {
			$this->error['warning'] = _l("Cannot remove the default shipping address! Please change your default shipping address first.");
		}

		if (!$this->error) {
			$where = array(
				'customer_id' => $customer_id,
				'address_id'  => $address_id,
			);

			$this->delete('customer_address', $where);
			return true;
		}

		return false;
	}

	public function getTotalAddresses($customer_id)
	{
		return (int)$this->queryVar("SELECT COUNT(*) FROM " . self::$tables['customer_address'] . " WHERE customer_id = " . (int)$customer_id);
	}
}
