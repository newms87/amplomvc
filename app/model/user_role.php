<?php

class App_Model_UserRole extends App_Model_Table
{
	protected $table = 'user_role', $primary_key = 'user_role_id';

	const
		TYPE_ADMIN = 'admin',
		TYPE_USER = 'user',
		TYPE_API = 'api',
		TYPE_CUSTOMER = 'customer',
		TYPE_GUEST = 'guest';

	static $types = array(
		self::TYPE_ADMIN    => 'Administrator',
		self::TYPE_USER     => 'User',
		self::TYPE_API      => 'API User',
		self::TYPE_CUSTOMER => 'Customer',
		self::TYPE_GUEST    => 'Guest',
	);

	public function save($user_role_id, $role)
	{
		if (isset($role['name'])) {
			if (!validate('text', $role['name'], 3, 64)) {
				$this->error['name'] = _l("Group Name must be between 3 and 64 characters");
			}

			if (!$user_role_id && $this->queryVar("SELECT COUNT(*) FROM {$this->t[$this->table]} WHERE `name` = '" . $this->escape($role['name']) . "'")) {
				$this->error['name'] = _l("User Group Name %s already exists!", $role['name']);
			}
		} elseif (!$user_role_id) {
			$this->error['name'] = _l("User Group Name is required.");
		}

		if ($this->error) {
			return false;
		}

		clear_cache($this->table);

		$role['permissions'] = !empty($role['permissions']) ? serialize($role['permissions']) : '';

		if ($user_role_id) {
			$this->update($this->table, $role, $user_role_id);
		} else {
			$user_role_id = $this->insert($this->table, $role);
		}

		return $user_role_id;
	}

	public function remove($user_role_id)
	{
		$filter = array(
			'user_role_id' => $user_role_id,
		);

		$total_users = $this->Model_User->getTotalRecords($filter);

		if ($total_users) {
			$this->error['user_role_users'] = _l("The user group %s currently has %s users associated and cannot be deleted.", $this->getField($user_role_id, 'name'), $total_users);

			return false;
		}

		clear_cache($this->table);

		return $this->delete($this->table, $user_role_id);
	}

	public function can($user_role_id, $level, $action)
	{
		$role = $this->getRole($user_role_id);

		if (!$role) {
			return false;
		}

		if (!$action) {
			return true;
		}

		if (!$level) {
			$level = 'w';
		}

		$path = explode('/', $action);
		$perm = $role['permissions'];

		foreach ($path as $p) {
			if (isset($perm[$p])) {
				$perm = $perm[$p];
				continue;
			}

			if (!isset($perm['*'])) {
				return false;
			}

			if (count($perm) === 1) {
				return $level === 'w' ? $perm['*'] === 'w' : (bool)$perm['*'];
			}

			return false;
		}

		if (isset($perm['index'])) {
			return $level === 'w' ? $perm['index']['*'] === 'w' : (bool)$perm['index']['*'];
		}

		if (isset($perm['*'])) {
			return $level === 'w' ? $perm['*'] === 'w' : (bool)$perm['*'];
		}

		return false;
	}

	public function getRole($user_role_id)
	{
		static $roles;

		if ($user_role_id && !isset($roles[$user_role_id])) {
			$user_role = cache($this->table . '.' . $user_role_id);

			if (!$user_role) {
				$user_role = $this->queryRow("SELECT * FROM {$this->t[$this->table]} WHERE user_role_id = " . (int)$user_role_id);

				if ($user_role) {
					$user_role['permissions'] = unserialize($user_role['permissions']);

					cache($this->table . '.' . $user_role_id, $user_role);
				}
			}

			$roles[$user_role_id] = $user_role;
		}

		if ($roles[$user_role_id]) {
			return $roles[$user_role_id];
		}

		//Default User Role
		return $user_role = array(
			'user_role_id' => 0,
			'type'         => '',
			'name'         => '',
			'permissions'  => array(),
		);
	}

	public function getRestrictedAreas()
	{
		$admin_dir = DIR_SITE . 'app/controller/admin/';
		$files     = get_files($admin_dir, 'php, mod', FILELIST_RELATIVE);

		$ignore = array(
			'load',
		);

		$areas = array(
			'admin' => array('*' => ''),
		);

		foreach ($files as $file) {
			$path        = str_replace('.php', '', $file);
			$parts       = explode('/', $path);
			$class_parts = array();

			if (is_file($admin_dir . $file . '.mod')) {
				$file .= '.mod';
			}

			$area = &$areas['admin'];

			foreach ($parts as $p) {
				if (!isset($area[$p])) {
					$area[$p] = array(
						'*' => '',
					);
				}

				$area = &$area[$p];

				$class_parts[] = _2camel($p);
			}

			require_once $admin_dir . $file;
			$methods = (array)get_class_methods("App_Controller_Admin_" . implode("_", $class_parts));

			foreach ($methods as $key => $method) {
				if (strpos($method, '__') === 0 || in_array($method, $ignore)) {
					continue;
				}

				$area[$method]['*'] = '';
			}
		}

		//Permissions for individual dashboards
		$dashboards = $this->Model_Dashboard->getRecords(null, null, array('cache' => true));

		$areas['admin']['dashboards'] = array(
			'*' => '',
		);

		foreach ($dashboards as $dash => $info) {
			$areas['admin']['dashboards'][$info['name']] = array(
				'*' => '',
			);
		}

		$this->sortAreas($areas);

		return $areas;
	}

	public function getColumns($filter = array(), $merge = array())
	{
		$merge += array(
			'type'       => array(
				'type'   => 'select',
				'label'  => _l("Type"),
				'build'  => array(
					'data' => App_Model_UserRole::$types,
				),
				'filter' => 'multiselect',
				'sort'   => true,
			),
			'user_count' => array(
				'type'   => 'text',
				'label'  => _l("# of Users"),
				'sort'   => true,
				'filter' => true,
			),
		);

		$columns = parent::getColumns($filter, $merge);

		//Disallowed Columns
		unset($columns['permissions']);

		return $columns;
	}

	private function sortAreas(&$areas)
	{
		if (is_array($areas)) {
			ksort($areas);

			foreach ($areas as &$a) {
				$this->sortAreas($a);
			}
		}
	}
}
