<?php

class App_Model_Setting_Role extends Model
{
	public function save($user_role_id, $data)
	{
		if (!$user_role_id || isset($data['name'])) {
			if (!validate('text', $data['name'], 3, 64)) {
				$this->error['name'] = _l("Group Name must be between 3 and 64 characters");
			}
		}

		if ($this->error) {
			return false;
		}

		$this->cache->delete('user_role');

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
			$name                           = $this->queryVar("SELECT name FROM " . DB_PREFIX . "user_role WHERE user_role_id = " . (int)$user_role_id);
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
				'name'        => '',
				'permissions' => array(),
			);
		}

		return $user_role;
	}

	public function getRoleId($role)
	{
		return $this->queryVar("SELECT user_role_id FROM " . $this->prefix . "user_role WHERE name = '" . $this->escape($role) . "'");
	}

	public function getRoleName($user_role_id)
	{
		return $this->queryVar("SELECT name FROM " . $this->prefix . "user_role WHERE user_role_id = " . (int)$user_role_id);
	}

	public function getRoles($sort = array(), $filter = array(), $select = '*', $total = false, $index = null)
	{
		//Select
		$select = $this->extractSelect('user_role', $select);

		//From
		$from = DB_PREFIX . "user_role";

		//Where
		$where = $this->extractWhere('user_role', $filter);

		//Order and Limit
		list($order, $limit) = $this->extractOrderLimit($sort);

		//The Query
		$calc_rows = ($total && $this->useCalcFoundRows('page', $sort, $filter)) ? "SQL_CALC_FOUND_ROWS" : '';

		$rows = $this->queryRows("SELECT $calc_rows $select FROM $from WHERE $where $order $limit", $index);

		if ($total) {
			$query      = $calc_rows ? "SELECT FOUND_ROWS()" : "SELECT COUNT(*) FROM $from WHERE $where";
			$total_rows = $this->queryVar($query);

			return array(
				$rows,
				$total_rows
			);
		}

		return $rows;
	}

	public function getRestrictedAreas()
	{
		$admin_dir = DIR_SITE . 'app/controller/admin/';
		$files     = $this->tool->getFiles($admin_dir, 'php', FILELIST_RELATIVE);

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

				$class_parts[] = $this->tool->_2CamelCase($p);
			}

			require_once $admin_dir . $file;
			$methods = get_class_methods("App_Controller_Admin_" . implode("_", $class_parts));

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
		return $this->getRoles($filter, '', false);
	}

	public function getViewListingId()
	{
		$view_listing_id = $this->Model_View->getViewListingBySlug('user_role_list');

		if (!$view_listing_id) {
			$view_listing = array(
				'name' => _l("User Roles"),
			   'slug' => 'user_role_list',
			   'path' => 'admin/setting/role/listing',
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
