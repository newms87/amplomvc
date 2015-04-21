<?php

class App_Model_User extends App_Model_Table
{
	protected $table = 'user', $primary_key = 'user_id';

	public function save($user_id, $user)
	{
		if (isset($user['username'])) {
			if (!validate('text', $user['username'], 3, 128)) {
				$this->error['username'] = _l("Username must be between 3 and 128 characters!");
			} else {
				$existing_id = $this->findRecord(array('username' => $user['username']));

				if ($existing_id !== (int)$user_id) {
					$this->error['username'] = _l("Username is already in use!");
				}
			}
		} elseif (!$user_id) {
			$this->error['username'] = _l("Please provide a Username!");
		}

		//Ensure password is set for new user (can be encrypted_password), or check if updating password
		if (empty($user['password'])) {
			unset($user['password']);
		}

		if (isset($user['password'])) {
			if (!validate('password', $user['password'], isset($user['confirm']) ? $user['confirm'] : null)) {
				$this->error['password'] = $this->validation->fetchError();
			}
		} elseif (!$user_id && empty($user['encrypted_password'])) {
			$this->error['password'] = _l("You must enter a password");
		}

		if ($this->error) {
			return false;
		}

		if (isset($user['password'])) {
			$user['password'] = $this->user->encrypt($user['password']);
		} elseif (!empty($user['encrypted_password'])) {
			$user['password'] = $user['encrypted_password'];
		}

		//New User
		if (!$user_id) {
			$user['date_added'] = $this->date->now();
		}

		$user_id = parent::save($user_id, $user);

		if (!empty($user['meta_exactly']) && !isset($user['meta'])) {
			$user['meta'] = array();
		}

		if (isset($user['meta'])) {
			$this->setMeta($user_id, $user['meta'], !empty($user['meta_exactly']));
		}

		return $user_id;
	}

	public function addMeta($user_id, $key, $value, $multi = false)
	{
		$serialized = (int)_is_object($value);

		if ($serialized) {
			$value = serialize($value);
		}

		if (!$multi) {
			$this->deleteMeta($user_id, $key);
		}

		$data = array(
			'user_id'    => $user_id,
			'key'        => $key,
			'value'      => $value,
			'serialized' => $serialized,
		);

		$this->insert('user_meta', $data);
	}

	public function setMeta($user_id, $meta, $exactly = false)
	{
		if ($exactly) {
			$this->delete('user_meta', array('user_id' => $user_id));
		}

		foreach ($meta as $key => $value) {
			$serialized = (int)_is_object($value);

			if ($serialized) {
				$value = serialize($value);
			}

			if (!$exactly) {
				$this->deleteMeta($user_id, $key);
			}

			//Add new value
			$data = array(
				'user_id'    => $user_id,
				'key'        => $key,
				'value'      => $value,
				'serialized' => $serialized,
			);

			$this->insert('user_meta', $data);
		}

		return true;
	}

	public function deleteMeta($user_id, $key)
	{
		$where = array(
			'user_id' => $user_id,
			'key'     => $key,
		);

		return $this->delete('user_meta', $where);
	}

	public function getMeta($user_id, $key = null)
	{
		if ($key) {
			$value = $this->queryRow("SELECT `value`, serialized FROM {$this->t['user_meta']} WHERE user_id = " . (int)$user_id . " AND `key` = '" . $this->escape($key) . "' LIMIT 1");

			if ($value) {
				return $value['serialized'] ? unserialize($value['value']) : $value['value'];
			}

			return null;
		}

		$meta = $this->queryRows("SELECT * FROM {$this->t['user_meta']} WHERE user_id = " . (int)$user_id, 'key');

		foreach ($meta as &$m) {
			$m = $m['serialized'] ? unserialize($m['value']) : $m['value'];
		}
		unset($m);

		return $meta;
	}

	public function getRecords($sort = array(), $filter = array(), $options = array(), $total = false)
	{
		//Where
		if (isset($filter['user_role'])) {
			$options['join']      = "LEFT JOIN {$this->t['user_role']} ur USING (user_role_id)";
			if (is_string($filter['user_role'])) {
				$filter['#user_role'] = "AND ur.`name` like '%" . $this->escape($filter['user_role']) . "%'";
			} else {
				$filter['#user_role'] = "AND ur.`name` IN ('" . implode("','", $this->escape($filter['user_role'])) . "')";
			}
		}

		if (isset($filter['name'])) {
			$filter['#name'] = "AND CONCAT(first_name, ' ', last_name) like '%" . $this->escape($filter['name']) . "%'";
		}

		//Order and Limit
		if (!empty($filter['sort']['name'])) {
			$ord = $filter['sort']['name'];
			unset($filter['sort']['name']);

			$filter['sort'] += array(
				'last_name'  => $ord,
				'first_name' => $ord,
			);
		}

		return parent::getRecords($sort, $filter, $options, $total);
	}

	public function getColumns($filter = array())
	{
		$columns = array(
			'user_role_id' => array(
				'type'         => 'select',
				'display_name' => _l("Role"),
				'build_data'   => $this->Model_UserRole->getRecords(null, null, array('cache' => true)),
				'build_config' => array(
					'user_role_id',
					'name'
				),
				'filter'       => 'select',
				'sort'         => true,
			),
			'status'       => array(
				'type'         => 'select',
				'display_name' => _l("Status"),
				'build_data'   => array(
					0 => _l("Disabled"),
					1 => _l("Enabled"),
				),
				'filter'       => true,
				'sort'         => true,
			),
		);

		$columns = $this->getTableColumns('user', $columns, $filter);

		unset($columns['password']);

		return $columns;
	}
}
