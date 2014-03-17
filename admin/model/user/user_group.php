<?php
class Admin_Model_User_UserGroup extends Model
{
	public function addUserGroup($data)
	{
		$data['permission'] = !empty($data['permissions']) ? serialize($data['permissions']) : '';

		$this->insert('user_group', $data);
	}

	public function editUserGroup($user_group_id, $data)
	{
		$data['permission'] = !empty($data['permissions']) ? serialize($data['permissions']) : '';

		$this->update('user_group', $data, $user_group_id);
	}

	public function deleteUserGroup($user_group_id)
	{
		$this->delete('user_group', $user_group_id);
	}

	//TODO: Change permissions to $data[$type][$page] = 1 format
	public function addPermission($user_group_id, $type, $page)
	{
		$permission = $this->queryVar("SELECT permission FROM " . DB_PREFIX . "user_group WHERE user_group_id = " . (int)$user_group_id);

		$permission = $permission ? unserialize($permission) : array();

		$permission[$type][] = $page;

		$user_group_data = array(
			'permission' => serialize($permission),
		);

		$this->update('user_group', $user_group_data, $user_group_id);
	}

	public function getUserGroup($user_group_id)
	{
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "user_group WHERE user_group_id = '" . (int)$user_group_id . "'");

		$user_group = array(
			'name'        => $query->row['name'],
			'permissions' => unserialize($query->row['permission'])
		);

		return $user_group;
	}

	public function getUserGroups($data = array())
	{
		$sql = "SELECT * FROM " . DB_PREFIX . "user_group";

		$sql .= " ORDER BY name";

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->query($sql);

		return $query->rows;
	}

	public function get_controller_list()
	{
		$ignore = array(
			'common/home',
			'common/startup',
			'common/login',
			'common/logout',
			'common/forgotten',
			'common/reset',
			'error/not_found',
			'error/permission',
			'common/footer',
			'common/header'
		);

		$files = glob(DIR_SITE . 'admin/controller/*/*.php');

		$permissions = array();

		foreach ($files as $file) {
			$data = explode('/', dirname($file));

			$permission = end($data) . '/' . basename($file, '.php');

			if (!in_array($permission, $ignore)) {
				$permissions[$permission] = $permission;
			}
		}

		return $permissions;
	}

	public function getTotalUserGroups()
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "user_group");

		return $query->row['total'];
	}
}
