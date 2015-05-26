<?php

class App_Model_Customer extends App_Model_Table
{
	protected $table = 'customer', $primary_key = 'customer_id';

	protected $meta = array();

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
				$this->error['email'] = $this->validation->fetchError();
			} elseif (!$customer_id && $this->customer->emailRegistered($customer['email'])) {
				$this->error['email'] = _l("E-Mail Address is already registered!");
			}

			if (isset($customer['username'])) {
				if (empty($customer['username'])) {
					$customer['username'] = $customer['email'];
				}
			} elseif (!$customer_id) {
				$customer['username'] = $customer['email'];
			}
		} elseif (!$customer_id) {
			$this->error['email'] = _l("Your email address is required.");
		}

		if ((isset($customer['zone_id']) || isset($customer['country_id'])) && !$this->Model_Address->validate($customer)) {
			$this->error += $this->Model_Address->fetchError();
		}

		if (!empty($data['phone']) && !validate('phone', $data['phone'])) {
			$this->error['phone'] = _l("The phone number you provided is invalid.");
		}

		if (isset($customer['password'])) {
			if (!validate('password', $customer['password'])) {
				$this->error['password'] = $this->validation->fetchError();
			} elseif (isset($customer['confirm']) && $customer['confirm'] !== $customer['password']) {
				$this->error['confirm'] = _l("Your password and confirmation do not match.");
			} else {
				$customer['password'] = $this->customer->encrypt($customer['password']);
			}
		} elseif (!$customer_id) {
			$customer['no_password_set'] = true;
			$customer['password']        = $this->customer->encrypt($this->generatePassword());
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

		$customer['approved'] = option('config_customer_approval') ? 1 : 0;

		if ($customer_id) {
			$this->update('customer', $customer, $customer_id);
		} else {
			$customer_id = $this->insert('customer', $customer);
		}

		if ($customer_id) {
			//Address will be extracted from customer information, if it exists
			$this->saveAddress($customer_id, null, $customer);

			//Customer MetaData
			if (!empty($customer['meta'])) {
				foreach ($customer['meta'] as $key => $value) {
					$this->setMeta($customer_id, $key, $value);
				}
			}

			$customer['customer_id'] = $customer_id;

			call('mail/new_customer', $customer);
		}

		return $customer_id;
	}

	/** Customer Meta Data **/
	public function addMeta($customer_id, $key, $value)
	{
		if (!$customer_id) {
			$this->error['customer_id'] = _l("The customer does not exist.");
			return false;
		}

		$this->meta[$customer_id][$key] = $value;
		clear_cache('customer.' . $customer_id);

		$serialized = (int)_is_object($value);

		if ($serialized) {
			$value = serialize($value);
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

	public function getMeta($customer_id, $key = null, $default = null)
	{
		if (empty($this->meta[$customer_id])) {
			$rows = $this->queryRows("SELECT * FROM {$this->t['customer_meta']} WHERE customer_id = " . (int)$customer_id);

			if ($rows) {
				foreach ($rows as $row) {
					$this->meta[$customer_id][$row['key']] = $row['serialized'] ? unserialize($row['value']) : $row['value'];
				}
			} else {
				$this->meta[$customer_id] = array();
			}
		}

		if ($key) {
			return isset($this->meta[$customer_id][$key]) ? $this->meta[$customer_id][$key] : $default;
		}

		return $this->meta[$customer_id];
	}

	public function deleteMeta($customer_id, $key)
	{
		unset($this->meta[$customer_id][$key]);

		clear_cache('customer.' . $customer_id);

		$where = array(
			'customer_id' => $customer_id,
			'key'         => $key,
		);

		return $this->delete('customer_meta', $where);
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

		if (!$address_id) {
			if ($address_id = $this->addressExists($customer_id, $address)) {
				return $address_id;
			}
		}

		$address_id = $this->Model_Address->save($address_id, $address);

		if (!$address_id) {
			$this->error = $this->Model_Address->fetchError();
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

	public function addressExists($customer_id, $address)
	{
		unset($address['address_id']);

		$where = $this->getWhere('address', $address);

		if (empty($where)) {
			return false;
		}

		return $this->queryVar("SELECT a.address_id FROM {$this->t['address']} a LEFT JOIN {$this->t['customer_address']} ca ON (ca.address_id = a.address_id) WHERE $where AND ca.customer_id = " . (int)$customer_id);
	}

	public function getAddresses($customer_id, $sort = array(), $filter = array(), $options = array(), $total = false)
	{
		$options['join']        = "LEFT JOIN {$this->t['customer_address']} ca USING(address_id)";
		$filter['#customer_id'] = "ca.customer_id = " . (int)$customer_id;

		return $this->Model_Address->getRecords($sort, $filter, $options, $total);
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
				$this->error = $this->Model_Address->fetchError();
			}
		}

		return false;
	}

	public function getTotalAddresses($customer_id, $filter = array())
	{
		return $this->getAddresses($customer_id, null, $filter, 'COUNT(*)');
	}
}
