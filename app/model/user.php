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
				$user_info = $this->Model_User->getUserByUsername($user['username']);

				if ($user_info && $user_info['user_id'] !== (int)$user_id) {
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
				$this->error['password'] = $this->validation->getError();
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

		clear_cache('user');

		//New User
		if (!$user_id) {
			$user['date_added'] = $this->date->now();
			$user_id            = $this->insert('user', $user);
		} else {
			//Update User
			$user_id = $this->update('user', $user, $user_id);
		}

		if (!empty($user['meta_exactly']) && !isset($user['meta'])) {
			$user['meta'] = array();
		}

		if (isset($user['meta'])) {
			$this->setMeta($user_id, $user['meta'], !empty($user['meta_exactly']));
		}

		return $user_id;
	}

	public function editPassword($user_id, $password)
	{
		$data = array(
			'password' => $this->user->encrypt($password),
		);

		clear_cache('user');

		return $this->update('user', $data, $user_id);
	}

	public function remove($user_id)
	{
		clear_cache('user');

		return $this->delete('user', $user_id);
	}

	public function getUser($user_id)
	{
		return $this->queryRow("SELECT * FROM `" . DB_PREFIX . "user` WHERE user_id = '" . (int)$user_id . "'");
	}

	public function addMeta($user_id, $key, $value, $multi = false)
	{
		$serialized = (int)_is_object($value);

		if ($serialized) {
			$value = serialize($value);
		}

		if (!$multi) {
			$where = array(
				'user_id' => $user_id,
				'key'     => $key,
			);

			$this->delete('user_meta', $where);
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
				//Delete old value (if any)
				$where = array(
					'user_id' => $user_id,
					'key'     => $key,
				);

				$this->delete('user_meta', $where);
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

	public function getMeta($user_id, $key = null)
	{
		if ($key) {
			$value = $this->queryRow("SELECT `value`, serialized FROM " . self::$prefix . "user_meta WHERE user_id = " . (int)$user_id . " AND `key` = '" . $this->escape($key) . "' LIMIT 1");
			if ($value) {
				return $value['serialized'] ? unserialize($value['value']) : $value['value'];
			}

			return null;
		}

		$meta = $this->queryRows("SELECT * FROM " . self::$prefix . "user_meta WHERE user_id = " . (int)$user_id, 'key');

		foreach ($meta as &$m) {
			$m = $m['serialized'] ? unserialize($m['value']) : $m['value'];
		}
		unset($m);

		return $meta;
	}

	public function getUserByUsername($username)
	{
		return $this->queryRow("SELECT * FROM `" . DB_PREFIX . "user` WHERE username = '" . $this->escape($username) . "'");
	}

	public function getUsers($sort = array(), $filter = array(), $select = null, $total = false, $index = null)
	{
		//Select
		$select = $this->extractSelect($this->table, $select);

		//From
		$from = $this->p_table;

		//Where
		if (isset($filter['user_role'])) {
			$roles = $this->Model_Setting_Role->getRoles(null, null, '*', false, 'name');

			$user_roles = is_array($filter['user_role']) ? $filter['user_role'] : array($filter['user_role']);

			if (isset($filter['user_role_id'])) {
				$filter['user_role_id'] = is_array($filter['user_role_id']) ? $filter['user_role_id'] : array($filter['user_role_id']);
			} else {
				$filter['user_role_id'] = array();
			}

			foreach ($user_roles as $role_name) {
				if (isset($roles[$role_name])) {
					$filter['user_role_id'][] = $roles[$role_name]['user_role_id'];
				}
			}
		}

		$where = $this->extractWhere($this->table, $filter);

		if (isset($filter['name'])) {
			$where .= " AND CONCAT(firstname, ' ', lastname) like '%" . $this->escape($filter['name']) . "%'";
		}

		//Order and Limit
		if (!empty($filter['sort'])) {
			if ($filter['sort'] === 'name') {
				$filter['sort'] = array(
					'lastname'  => $filter['order'],
					'firstname' => $filter['order'],
				);
			}
		}

		list($order, $limit) = $this->extractOrderLimit($sort);

		//The Query
		return $this->queryRows("SELECT $select FROM $from WHERE $where $order $limit", $index, $total);
	}

	public function getTotalUsers($filter = array())
	{
		return $this->getUsers(array(), $filter, "COUNT(*)");
	}

	public function getColumns($filter = array())
	{
		$columns = array(
			'user_role_id' => array(
				'type'         => 'select',
				'display_name' => _l("Role"),
				'build_data'   => $this->Model_Setting_Role->getRoles(),
				'build_config' => array(
					'user_role_id',
					'name'
				),
				'filter'       => 'select',
				'sortable'     => true,
			),
			'status'       => array(
				'type'         => 'select',
				'display_name' => _l("Status"),
				'build_data'   => array(
					0 => _l("Disabled"),
					1 => _l("Enabled"),
				),
				'filter'       => true,
				'sortable'     => true,
			),
		);

		$columns = $this->getTableColumns('user', $columns, $filter);

		unset($columns['password']);

		return $columns;
	}
}
