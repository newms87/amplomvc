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
			} elseif ($this->emailRegistered($customer['email'])) {
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

		$customer['password'] = $this->encrypt($customer['password']);

		$customer['approved'] = option('config_customer_approval') ? 1 : 0;

		if ($customer_id) {
			$this->update('customer', $customer, $customer_id);
		} else {
			$customer_id = $this->insert('customer', $customer);
		}

		//Address will be extracted from customer information, if it exists
		$this->addAddress($customer);

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
}
