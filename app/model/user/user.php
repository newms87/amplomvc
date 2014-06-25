<?php

class App_Model_User_User extends Model
{
	public function save($user_id, $data)
	{
		if (!$this->validate($user_id, $data)) {
			return false;
		}

		if (isset($data['password'])) {
			$data['password'] = $this->user->encrypt($data['password']);
		} elseif (isset($data['encrypted_password'])) {
			$data['password'] = $data['encrypted_password'];
		}

		//New User
		if (!$user_id) {
			$data['date_added'] = $this->date->now();
			$user_id = $this->insert('user', $data);
		} else {
			//Update User
			$user_id = $this->update('user', $data, $user_id);
		}

		return $user_id;
	}

	public function editPassword($user_id, $password)
	{
		$data = array(
			'password' => $this->user->encrypt($password),
		);

		return $this->update('user', $data, $user_id);
	}

	public function remove($user_id)
	{
		return $this->delete('user', $user_id);
	}

	public function getUser($user_id)
	{
		return $this->queryRow("SELECT * FROM `" . DB_PREFIX . "user` WHERE user_id = '" . (int)$user_id . "'");
	}

	public function getUserByUsername($username)
	{
		return $this->queryRow("SELECT * FROM `" . DB_PREFIX . "user` WHERE username = '" . $this->escape($username) . "'");
	}

	public function getUsers($filter = array(), $select = '*', $index = null)
	{
		//Select
		if ($index === false) {
			$select = "COUNT(*)";
		}

		//From
		$from = DB_PREFIX . "user";

		//Where
		$where = "1";

		if (isset($filter['name'])) {
			$where .= " AND CONCAT(lastname, ' ', firstname) like '%" . $this->escape($filter['name']) . "%'";
		}

		if (isset($filter['username'])) {
			$where .= " AND username like '%" . $this->escape($filter['username']) . "%'";
		}

		if (isset($filter['user_role'])) {
			$roles = $this->Model_User_Role->getRoles(null, '*', 'name');

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

		if (!empty($filter['user_role_id'])) {
			$user_role_ids = is_array($filter['user_role_id']) ? $this->escape($filter['user_role_id']) : array((int)$filter['user_role_id']);

			$where .= " AND user_role_id IN (" . implode(',', $user_role_ids) . ")";
		}

		if (isset($filter['status'])) {
			$where .= " AND status = " . (int)$filter['status'];
		}

		//Order and Limit
		if ($index !== false) {
			if (!empty($filter['sort'])) {
				if ($filter['sort'] === 'name') {
					$filter['sort'] = array(
						'lastname'  => $filter['order'],
						'firstname' => $filter['order'],
					);
				}
			}

			$order = $this->extractOrder($filter);
			$limit = $this->extractLimit($filter);
		} else {
			$order = '';
			$limit = '';
		}

		//The Query
		$query = "SELECT $select FROM $from WHERE $where $order $limit";

		if ($index === false) {
			return $this->queryVar($query);
		}

		return $this->queryRows($query, $index);
	}

	public function getTotalUsers($filter = array())
	{
		return $this->getUsers($filter, '', false);
	}

	public function getTotalUsersByGroupId($user_role_id)
	{
		return $this->queryVar("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "user` WHERE user_role_id = '" . (int)$user_role_id . "'");
	}

	public function getTotalUsersByEmail($email)
	{
		return $this->queryVar("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "user` WHERE email = '" . $this->escape($email) . "'");
	}

	public function validate($user_id, $user)
	{
		if (!$user_id || isset($user['username'])) {
			if (!validate('text', $user['username'], 3, 20)) {
				$this->error['username'] = _l("Username must be between 3 and 20 characters!");
			} else {
				$user_info = $this->Model_User_User->getUserByUsername($user['username']);

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
				'build_data'   => $this->Model_User_Role->getRoles(),
				'build_config' => array('user_role_id', 'name'),
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
