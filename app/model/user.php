<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC
 * @link http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt files in the root directory.
 */

class App_Model_User extends App_Model_Table
{
	protected $table = 'user', $primary_key = 'user_id';

	public function save($user_id, $user)
	{
		if (!user_can('w', 'admin/user/save')) {
			unset($user['user_role_id']);
			unset($user['username']);
		}

		if (isset($user['username'])) {
			if (!validate('text', $user['username'], 3, 128)) {
				$this->error['username'] = _l("Username must be between 3 and 128 characters!");
			} else {
				$existing_id = $this->findRecord(array('#username' => "username = '" . $this->escape($user['username']) . "'"));

				if ($existing_id && $existing_id !== (int)$user_id) {
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

		return parent::save($user_id, $user);
	}

	public function getUser($user_id)
	{
		$user = $this->getRecord($user_id);

		if ($user) {
			$user += $this->Model_Meta->get('user', $user_id);
		}

		return $user;
	}

	public function getRole($user_id)
	{
		return $this->Model_UserRole->getField($this->getField($user_id, 'user_role_id'), 'name');
	}

	public function getRecords($sort = array(), $filter = array(), $options = array(), $total = false)
	{
		//Where
		if (isset($filter['user_role'])) {
			$options['join']['user_role'] = array(
				'type'  => "LEFT JOIN",
				'alias' => 'ur',
				'on'    => 'user_role_id',
			);

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

	public function getColumns($filter = array(), $merge = array())
	{
		$merge += array(
			'user_role_id' => array(
				'type'   => 'select',
				'label'  => _l("Role"),
				'build'  => array(
					'data'  => $this->Model_UserRole->getRecords(null, null, array('cache' => true)),
					'value' => 'user_role_id',
					'label' => 'name',
				),
				'filter' => 'multiselect',
				'sort'   => true,
			),
			'status'       => array(
				'type'   => 'select',
				'label'  => _l("Status"),
				'build'  => array(
					'data' => array(
						0 => _l("Disabled"),
						1 => _l("Enabled"),
					),
				),
				'filter' => true,
				'sort'   => true,
			),
		);

		$columns = parent::getColumns($filter, $merge);

		unset($columns['password']);

		return $columns;
	}
}
