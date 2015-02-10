<?php

class App_Model_Customer extends App_Model_Table
{
	protected $table = 'customer', $primary_key = 'customer_id';

	public function save($customer_id, $customer)
	{
		if (isset($customer['name']) && !isset($customer['first_name'])) {
			$name_parts             = explode(' ', $customer['name'], 2);
			$customer['first_name'] = $name_parts[0];

			if (isset($name_parts[1])) {
				$customer['last_name'] = $name_parts[1];
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

		if ((isset($customer['zone_id']) || isset($customer['country_id'])) && !$this->Model_Address->validate($customer)) {
			$this->error += $this->Model_Address->getError();
		}

		if (!empty($data['phone']) && !validate('phone', $data['phone'])) {
			$this->error['phone'] = _l("The phone number you provided is invalid.");
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
		return $this->queryRows("SELECT * FROM {$this->t['customer_group']}");
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

		return $this->insert('customer_meta', $customer_meta);
	}

	public function setMeta($customer_id, $key, $value)
	{
		$this->deleteMeta($customer_id, $key);

		return $this->addMeta($customer_id, $key, $value);
	}

	public function getMeta($customer_id)
	{
		$rows = $this->queryRows("SELECT * FROM {$this->t['customer_meta']} WHERE customer_id = " . (int)$customer_id);

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

		return true;
	}

	/** Addresses **/

	public function customerHasAddress($customer_id, $address_id)
	{
		return $this->queryVar("SELECT COUNT(*) FROM {$this->t['customer_address']} WHERE address_id = " . (int)$address_id . " AND customer_id = " . (int)$customer_id);
	}

	public function saveAddress($customer_id, $address_id, $address)
	{
		if (!$customer_id) {
			$this->error['customer_id'] = _l("Address must be assigned to a customer.");
			return false;
		}

		$address_id = $this->Model_Address->save($address_id, $address);

		if (!$address_id) {
			$this->error = $this->Model_Address->getError();
			return false;
		}

		//Associate address to customer
		if (!$this->customerHasAddress($customer_id, $address_id)) {
			$customer_address = array(
				'customer_id' => $customer_id,
				'address_id'  => $address_id,
			);

			$this->insert('customer_address', $customer_address);
		}

		clear_cache('customer.' . $customer_id);

		return $address_id;
	}

	public function getAddresses($customer_id, $sort = array(), $filter = array(), $select = '*', $total = false, $index = null)
	{
		$addresses = cache('customer.' . $customer_id . '.addresses');

		if ($addresses === null) {
			$addresses = $this->queryColumn("SELECT address_id FROM {$this->t['customer_address']} WHERE customer_id = " . (int)$customer_id);
			cache('customer.' . $customer_id . '.addresses', $addresses);
		}

		if (!$sort && !$filter && $select === '*' && !$total) {
			$sort['cache'] = true;
		}

		$filter['address_id'] = $addresses;

		return $this->Model_Address->getRecords($sort, $filter, $select, $total, $index);
	}

	public function removeAddress($customer_id, $address_id)
	{
		$where = array(
			'customer_id' => $customer_id,
			'address_id'  => $address_id,
		);

		clear_cache('customer.' . $customer_id);

		if ($this->delete('customer_address', $where)) {
			if ($this->Model_Address->remove($address_id)) {
				return true;
			} else {
				$this->error = $this->Model_Address->getError();
			}
		}

		return false;
	}

	public function getTotalAddresses($customer_id, $filter = array())
	{
		return $this->getAddresses($customer_id, null, $filter, 'COUNT(*)');
	}
}
