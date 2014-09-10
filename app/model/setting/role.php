<?php
class App_Model_Setting_Role extends Model
{
	public function add($data)
	{
		if (!validate('text', $data['name'], 3, 64)) {
			$this->error['name'] = _l("Group Name must be between 3 and 64 characters");
		}

		if ($this->error) {
			return false;
		}

		$this->cache->delete('user_role');

		foreach ($data['permissions'] as &$level) {
			$level = array_combine($level, $level);
		}
		unset($level);

		$data['permissions'] = !empty($data['permissions']) ? serialize($data['permissions']) : '';

		return $this->insert('user_role', $data);
	}

	public function edit($user_role_id, $data)
	{
		if (!validate('text', $data['name'], 3, 64)) {
			$this->error['name'] = _l("Group Name must be between 3 and 64 characters");
		}

		if ($this->error) {
			return false;
		}

		$this->cache->delete('user_role');

		foreach ($data['permissions'] as &$level) {
			$level = array_combine($level, $level);
		}
		unset($level);

		$data['permissions'] = !empty($data['permissions']) ? serialize($data['permissions']) : '';

		return $this->update('user_role', $data, $user_role_id);
	}

	public function remove($user_role_id)
	{
		$filter = array(
			'user_role_id' => $user_role_id,
		);

		$total_users = $this->Model_User->getTotalUsers($filter);

		if ($total_users) {
			$name = $this->queryVar("SELECT name FROM " . DB_PREFIX . "user_role WHERE user_role_id = " . (int)$user_role_id);
			$this->error['user_role_users'] = _l("The user group %s currently has %s users associated and cannot be deleted.", $name, $total_users);

			return false;
		}

		$this->cache->delete('user_role');
		return $this->delete('user_role', $user_role_id);
	}

	public function getRole($user_role_id)
	{
		$user_role = $this->queryRow("SELECT * FROM " . DB_PREFIX . "user_role WHERE user_role_id = " . (int)$user_role_id);

		if ($user_role) {
			$user_role['permissions'] = unserialize($user_role['permissions']);
		} else {
			$user_role = array(
				'name' => '',
			   'permissions' => array(),
			);
		}

		return $user_role;
	}

	public function getRoleId($role)
	{
		return $this->queryVar("SELECT user_role_id FROM " . DB_PREFIX . "user_role WHERE name = '" . $this->escape($role) . "'");
	}

	public function getRoles($filter = array(), $select = '*', $index = null)
	{
		//Select
		if ($index === false) {
			$select = "COUNT(*)";
		}

		//From
		$from = DB_PREFIX . "user_role";

		//Where
		$where = "1";

		//Order and Limit
		if ($index !== false) {
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

	public function getControllers()
	{
		$files = $this->tool->getFiles(DIR_SITE . 'app/controller/admin/', 'php', FILELIST_RELATIVE);

		$ignore = array(
			'common',
		   'error',
		);

		$permissions = array();

		foreach ($files as $file) {
			$path = str_replace('.php', '', $file);

			$parts = explode('/', $path);

			if (in_array($parts[0], $ignore)) {
				continue;
			}

			$permissions[$path] = $path;
		}

		return $permissions;
	}

	public function getTotalRoles($filter = array())
	{
		return $this->getRoles($filter, '', false);
	}
}