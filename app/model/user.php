<?php

class App_Model_User extends Model
{
	public function save($user_id, $data)
	{
		if (empty($data['password'])) {
			unset($data['password']);
		}

		if (!$this->validate($user_id, $data)) {
			return false;
		}

		if (isset($data['password'])) {
			$data['password'] = $this->user->encrypt($data['password']);
		} elseif (isset($data['encrypted_password'])) {
			$data['password'] = $data['encrypted_password'];
		}

		$this->cache->delete('user');

		//New User
		if (!$user_id) {
			$data['date_added'] = $this->date->now();
			$user_id            = $this->insert('user', $data);
		} else {
			//Update User
			$user_id = $this->update('user', $data, $user_id);
		}

		if (!empty($data['meta_exactly']) && !isset($data['meta'])) {
			$data['meta'] = array();
		}

		if (isset($data['meta'])) {
			$this->setMeta($user_id, $data['meta'], !empty($data['meta_exactly']));
		}

		return $user_id;
	}

	public function editPassword($user_id, $password)
	{
		$data = array(
			'password' => $this->user->encrypt($password),
		);

		$this->cache->delete('user');

		return $this->update('user', $data, $user_id);
	}

	public function remove($user_id)
	{
		$this->cache->delete('user');

		return $this->delete('user', $user_id);
	}

	public function getUser($user_id)
	{
		return $this->queryRow("SELECT * FROM `" . DB_PREFIX . "user` WHERE user_id = '" . (int)$user_id . "'");
	}

	public function setMeta($user_id, $meta, $exactly = false)
	{
		if ($exactly) {
			$this->delete('user_meta', array('user_id' => $user_id));
		}

		foreach ($meta as $key => $value) {
			if (_is_object($value)) {
				$value      = serialize($value);
				$serialized = 1;
			} else {
				$serialized = 0;
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

	public function getMeta($user_id)
	{
		$meta = $this->queryRows("SELECT * FROM " . $this->prefix . "user_meta WHERE user_id = " . (int)$user_id, 'key');

		foreach ($meta as &$m) {
			if ($m['serialized']) {
				$m['value'] = unserialize($m['value']);
			}
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
		if (!$select) {
			$select = '*';
		}

		//From
		$from = DB_PREFIX . "user";

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

		$where = $this->extractWhere('user', $filter);

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

		$order = $this->extractOrder($sort);
		$limit = $this->extractLimit($sort);

		//The Query
		$calc_rows = ($total && $this->calcFoundRows('user', $sort, $filter)) ? "SQL_CALC_FOUND_ROWS " : '';

		$rows = $this->queryRows("SELECT $calc_rows $select FROM $from WHERE $where $order $limit", $index);

		//Get Results
		if ($total) {
			$query      = $calc_rows ? "SELECT FOUND_ROWS()" : "SELECT COUNT(*) FROM $from WHERE $where";
			$total_rows = $this->queryVar($query);

			return array(
				$rows,
				$total_rows,
			);
		}

		return $rows;
	}

	public function getTotalUsers($filter = array())
	{
		return $this->getUsers(array(), $filter, "COUNT(*)");
	}

	public function validate($user_id, $user)
	{
		if (!$user_id || isset($user['username'])) {
			if (!validate('text', $user['username'], 3, 128)) {
				$this->error['username'] = _l("Username must be between 3 and 128 characters!");
			} else {
				$user_info = $this->Model_User->getUserByUsername($user['username']);

				if ($user_info && $user_info['user_id'] !== $user_id) {
					$this->error['username'] = _l("Username is already in use!");
				}
			}
		}

		if (!$user_id || isset($user['firstname'])) {
			if (!validate('text', $user['firstname'], 1, 32)) {
				$this->error['firstname'] = _l("First Name must be between 1 and 32 characters!");
			}
		}

		if (!$user_id || isset($user['lastname'])) {
			if (!validate('text', $user['lastname'], 1, 32)) {
				$this->error['lastname'] = _l("Last Name must be between 1 and 32 characters!");
			}
		}

		//Ensure password is set for new user (can be encrypted_password), or check if updating password
		if ((!$user_id && empty($user['encrypted_password'])) || isset($user['password'])) {
			if (!validate('password', $user['password'], isset($user['confirm']) ? $user['confirm'] : null)) {
				$this->error['password'] = $this->validation->getError();
			}
		}

		return empty($this->error);
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
