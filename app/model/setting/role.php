<?php

class App_Model_Setting_Role extends Model
{
	public function save($user_role_id, $data)
	{
		if (isset($data['name'])) {
			if (!validate('text', $data['name'], 3, 64)) {
				$this->error['name'] = _l("Group Name must be between 3 and 64 characters");
			}

			if (!$user_role_id && $this->queryVar("SELECT COUNT(*) FROM " . self::$tables['user_role'] . " WHERE `name` = '" . $this->escape($data['name']) . "'")) {
				$this->error['name'] = _l("Group Name already exists!");
			}
		} elseif (!$user_role_id) {
			$this->error['name'] = _l("Group Name is required.");
		}

		if ($this->error) {
			return false;
		}

		clear_cache('user_role');

		$data['permissions'] = !empty($data['permissions']) ? serialize($data['permissions']) : '';

		if ($user_role_id) {
			$this->update('user_role', $data, $user_role_id);
		} else {
			$user_role_id = $this->insert('user_role', $data);
		}

		return $user_role_id;
	}

	public function remove($user_role_id)
	{
		$filter = array(
			'user_role_id' => $user_role_id,
		);

		$total_users = $this->Model_User->getTotalUsers($filter);

		if ($total_users) {
			$name                           = $this->queryVar("SELECT name FROM " . self::$tables['user_role'] . " WHERE user_role_id = " . (int)$user_role_id);
			$this->error['user_role_users'] = _l("The user group %s currently has %s users associated and cannot be deleted.", $name, $total_users);

			return false;
		}

		clear_cache('user_role');
		return $this->delete('user_role', $user_role_id);
	}

	public function getRole($user_role_id)
	{
		$user_role = cache('user_role.' . $user_role_id);

		if (!$user_role) {
			$user_role = $this->queryRow("SELECT * FROM " . self::$tables['user_role'] . " WHERE user_role_id = " . (int)$user_role_id);

			if ($user_role) {
				$user_role['permissions'] = unserialize($user_role['permissions']);
			} else {
				$user_role = array(
					'name'        => '',
					'permissions' => array(),
				);
			}

			cache('user_role.' . $user_role_id, $user_role);
		}

		return $user_role;
	}

	public function getRoleId($role)
	{
		return $this->queryVar("SELECT user_role_id FROM " . self::$tables['user_role'] . " WHERE name = '" . $this->escape($role) . "'");
	}

	public function getRoleName($user_role_id)
	{
		return $this->queryVar("SELECT name FROM " . self::$tables['user_role'] . " WHERE user_role_id = " . (int)$user_role_id);
	}

	public function getRoles($sort = array(), $filter = array(), $select = '*', $total = false, $index = null)
	{
		//Select
		$select = $this->extractSelect('user_role', $select);

		//From
		$from = self::$tables['user_role'];

		//Where
		$where = $this->extractWhere('user_role', $filter);

		//Order and Limit
		list($order, $limit) = $this->extractOrderLimit($sort);

		//The Query
		return $this->queryRows("SELECT $select FROM $from WHERE $where $order $limit", $index, $total);
	}

	public function getRestrictedAreas()
	{
		$admin_dir = DIR_SITE . 'app/controller/admin/';
		$files     = get_files($admin_dir, 'php', FILELIST_RELATIVE);

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
		$dashboards = $this->Model_Dashboard->getDashboards();

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

	public function getTotalRoles($filter = array())
	{
		return $this->getRoles(array(), $filter, 'COUNT(*)');
	}

	public function getViewListingId()
	{
		$view_listing_id = $this->Model_View->getViewListingBySlug('user_role_list');

		if (!$view_listing_id) {
			$view_listing = array(
				'name' => _l("User Roles"),
			   'slug' => 'user_role_list',
			   'path' => 'admin/settings/role/listing',
			);

			$view_listing_id = $this->Model_View->saveViewListing(null, $view_listing);
		}

		return $view_listing_id;
	}

	public function getColumns($filter = array())
	{
		static $merge;

		if (!$merge) {
			$merge = array();
		}

		return $this->getTableColumns('user_role', $merge, $filter);
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
