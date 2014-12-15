<?php

class App_Model_Navigation extends App_Model_Table
{
	protected $table = 'navigation', $primary_key = 'navigation_id';

	public function addNavigationGroup($data)
	{
		if (!$this->validateNavigationGroup($data)) {
			return false;
		}

		if (!isset($data['status'])) {
			$data['status'] = 1;
		}

		$navigation_group_id = $this->insert("navigation_group", $data);

		//Add Stores
		foreach ($data['stores'] as $store_id) {
			$store_data = array(
				'navigation_group_id' => $navigation_group_id,
				'store_id'            => $store_id
			);

			$this->insert("navigation_store", $store_data);
		}

		//Add Links
		if (!empty($data['links'])) {
			$this->addNavigationLinks($navigation_group_id, $data['links']);
		}

		clear_cache('navigation');

		return true;
	}

	public function editNavigationGroup($navigation_group_id, $data)
	{
		$data['navigation_group_id'] = $navigation_group_id;

		if (!$this->validateNavigationGroup($data)) {
			return false;
		}

		$this->update("navigation_group", $data, $navigation_group_id);

		//Update Links
		if (isset($data['links'])) {
			$this->delete("navigation", array("navigation_group_id" => $navigation_group_id));

			if (!empty($data['links'])) {
				$this->addNavigationLinks($navigation_group_id, $data['links']);
			}
		}

		clear_cache('navigation');

		return true;
	}

	public function deleteNavigationGroup($navigation_group_id)
	{
		if (!$this->validateDeleteNavigationGroup($navigation_group_id)) {
			return false;
		}

		$this->delete("navigation_group", $navigation_group_id);
		$this->delete("navigation", array("navigation_group_id" => $navigation_group_id));

		clear_cache('navigation');

		return true;
	}

	public function removeNavigationGroup($group)
	{
		$navigation_group_id = $this->queryVar("SELECT navigation_group_id FROM " . $this->prefix . "navigation_group WHERE `name` = '" . $this->escape($group) . "'");

		if ($navigation_group_id) {
			return $this->deleteNavigationGroup($navigation_group_id);
		}

		$this->error['navigation_group_id'] = _l("Unknown Navigation Group %s", $group);

		return false;
	}

	public function addNavigationLink($navigation_group_id, $link)
	{
		if (!$this->validateNavigationLink($navigation_group_id, $link)) {
			return false;
		}

		$link['navigation_group_id'] = $navigation_group_id;

		if (!empty($link['parent'])) {
			$link['parent_id'] = $this->queryVar("SELECT navigation_id FROM " . DB_PREFIX . "navigation WHERE `name` = '" . $this->escape($link['parent']) . "'");
		}

		clear_cache('navigation');

		return $this->insert("navigation", $link);
	}

	public function addNavigationLinkTree($navigation_group_id, $links, $parent_id = 0)
	{
		$sort_order = 0;

		foreach ($links as $name => $link) {
			if (!isset($link['sort_order'])) {
				$link['sort_order'] = $sort_order++;
			}

			if (empty($link['name'])) {
				$link['name'] = $name;
			}

			if (!isset($link['status'])) {
				$link['status'] = 1;
			}

			$link['parent_id'] = $parent_id;

			$navigation_id = $this->addNavigationLink($navigation_group_id, $link);

			if (!empty($link['children'])) {
				$this->addNavigationLinkTree($navigation_group_id, $link['children'], $navigation_id);
			}
		}

		return empty($this->error);
	}

	public function addNavigationLinks($navigation_group_id, $links)
	{
		//Transform links into Tree structure (if not already)
		foreach ($links as $nav_id => &$link) {
			if (empty($link['name'])) {
				$link['name'] = $nav_id;
			}

			if (!isset($link['status'])) {
				$link['status'] = 1;
			}

			if (isset($link['parent_id']) && ($pid = $link['parent_id']) != 0) {
				if (!isset($links[$pid]['children'])) {
					$links[$pid]['children'] = array();
				}

				$links[$pid]['children'][$nav_id] = & $link;
			} else {
				$link['parent_id'] = 0;
			}
		}
		unset($link);

		foreach ($links as $key => $link) {
			if ($link['parent_id'] > 0) {
				unset($links[$key]);
			}
		}

		return $this->addNavigationLinkTree($navigation_group_id, $links);
	}

	public function editNavigationLink($navigation_group_id, $navigation_id, $link)
	{
		$link['navigation_id'] = $navigation_id;

		if (!$this->validateNavigationLink($navigation_group_id, $link)) {
			return false;
		}

		$link['navigation_group_id'] = $navigation_group_id;
		$link['navigation_id']       = $navigation_id;

		if (!empty($link['parent'])) {
			$link['parent_id'] = $this->queryVar("SELECT navigation_id FROM " . DB_PREFIX . "navigation WHERE `name` = '" . $this->escape($link['parent']) . "'");
		}

		clear_cache('navigation');

		return $this->update("navigation", $link, $navigation_id);
	}

	public function deleteNavigationLink($navigation_id)
	{
		clear_cache('navigation');

		$children = $this->queryColumn("SELECT navigation_id FROM " . DB_PREFIX . "navigation WHERE parent_id = " . (int)$navigation_id);

		foreach ($children as $child_id) {
			$this->deleteNavigationLink($child_id);
		}

		return $this->delete("navigation", $navigation_id);
	}

	public function getGroup($navigation_group_id)
	{
		$group = $this->queryRow("SELECT * FROM " . DB_PREFIX . "navigation_group WHERE navigation_group_id = " . (int)$navigation_group_id);

		$group['links']  = $this->getGroupLinks($navigation_group_id);

		return $group;
	}

	public function getGroups($sort = array(), $filter = array(), $select = '*', $total = false, $index = null)
	{
		//Select
		$select = $this->extractSelect('navigation_group', $select);

		//From
		$from = $this->prefix . 'navigation_group';

		//Where
		$where = $this->extractWhere('navigation_group', $filter);

		//Order and Limit
		list($order, $limit) = $this->extractOrderLimit($sort);

		//The Query
		$result = $this->queryRows("SELECT $select FROM $from WHERE $where $order $limit", $index, $total);

		$total ? $rows = &$results[0] : $rows = &$results;

		foreach ($rows as &$row) {
			$row['links'] = $this->getGroupLinks($row['navigation_group_id']);
		}
		unset($row);

		return $result;
	}

	public function getStoreLinks()
	{
		$nav_groups = cache("navigation_group.store");

		if (is_null($nav_groups)) {
			$filter = array(
				'status' => 1,
			);

			$navigation_groups = $this->getGroups(null, $filter, '*', false, 'name');

			$nav_groups = array();

			foreach ($navigation_groups as &$group) {
				if (empty($group['links'])) {
					continue;
				}
				$parent_ref = array();

				foreach ($group['links'] as $key => &$link) {
					$link['children']                   = array();
					$parent_ref[$link['navigation_id']] = &$link;

					if ($link['parent_id']) {
						$parent_ref[$link['parent_id']]['children'][] = &$link;
						unset($group['links'][$key]);
					}
				}
				unset($link);
			}
			unset($group);

			cache("navigation_group.store", $nav_groups);
		}

		//Filter Conditional Links And Access Permissions


		//TODO: This leaves null values in group links. Consider changing approach.
		foreach ($nav_groups as &$group) {
			$this->filterLinks($group);
		}
		unset($group);

		return $nav_groups;
	}

	public function getNavigationLinks()
	{
		$nav_groups = cache('navigation_groups.admin');

		if (!$nav_groups) {
			$query = $this->query("SELECT ng.* FROM " . DB_PREFIX . "navigation_group ng WHERE ng.status='1'");

			$nav_groups = array();

			foreach ($query->rows as &$group) {
				$nav_group_links = $this->getGroupLinks($group['navigation_group_id']);

				$parent_ref = array();

				foreach ($nav_group_links as $key => &$link) {
					if (!empty($parent_ref[$link['navigation_id']]['children'])) {
						$link['children'] = & $parent_ref[$link['navigation_id']]['children'];
					} else {
						$link['children'] = array();
					}

					$parent_ref[$link['navigation_id']] = & $link;

					if ($link['parent_id']) {
						$parent_ref[$link['parent_id']]['children'][] = & $link;
						unset($nav_group_links[$key]);
					}
				}

				$nav_groups[$group['name']] = $nav_group_links;
			}

			cache('navigation_groups.admin', $nav_groups);
		}

		return $nav_groups;
	}

	public function getGroupLinks($navigation_group_id)
	{
		return $this->queryRows("SELECT * FROM " . DB_PREFIX . "navigation WHERE navigation_group_id = '" . (int)$navigation_group_id . "' ORDER BY parent_id, sort_order ASC", 'navigation_id');
	}

	public function getTotalNavigationGroups($filter)
	{
		return $this->getGroups(null, $filter, 'COUNT(*)');
	}

	public function resetAdminNavigationGroup()
	{
		$links = array(
			'home'    => array(
				'display_name' => 'Home',
				'href'         => '',
			),

			'dashboards' => array(
				'display_name' => "Dashboards",
				'href'         => 'admin/dashboard',
			),

			'content' => array(
				'display_name' => 'Content',
				'children'     => array(
					'content_blocks' => array(
						'display_name' => 'Blocks',
						'href'         => 'admin/block',
					),
					'content_pages'  => array(
						'display_name' => 'Pages',
						'href'         => 'admin/page',
					),
				),
			),

			'plugins' => array(
				'display_name' => 'Plugins',
				'href'         => 'admin/plugin',
			),

			'users'   => array(
				'display_name' => 'Users',
				'children'     => array(
					'users_users'       => array(
						'display_name' => 'Users',
						'href'         => 'admin/user',
					),
					'users_user_roles' => array(
						'display_name' => 'User Roles',
						'href'         => 'admin/settings/role',
					),
				),
			),

			'system'  => array(
				'display_name' => 'System',
				'children'     => array(
					'system_settings'     => array(
						'display_name' => 'Settings',
						'href'         => 'admin/settings/store',
						'children'     => array(
							'system_settings_general'              => array(
								'display_name' => 'General',
								'href'         => 'admin/settings/setting',
							),
							'system_settings_update'               => array(
								'display_name' => 'Update',
								'href'         => 'admin/settings/update',
							),
						),
					),
					'system_mail'         => array(
						'display_name' => 'Mail',
						'children'     => array(
							'system_mail_send_email'    => array(
								'display_name' => 'Send Email',
								'href'         => 'admin/mail/send_email',
							),
							'system_mail_mail_messages' => array(
								'display_name' => 'Mail Messages',
								'href'         => 'admin/mail/messages',
							),
							'system_mail_error'         => array(
								'display_name' => 'Failed Messages',
								'href'         => 'admin/mail/error',
							),
						),
					),
					'system_views' => array(
						'display_name' => 'Views',
					   'href' => 'admin/view',
					),
					'system_url_alias'    => array(
						'display_name' => 'URL Alias',
						'href'         => 'admin/settings/url_alias',
					),
					'system_cron'         => array(
						'display_name' => 'Cron',
						'href'         => 'admin/settings/cron',
					),
					'system_navigation'   => array(
						'display_name' => 'Navigation',
						'href'         => 'admin/navigation',
					),
					'system_design'       => array(
						'display_name' => 'Design',
						'children'     => array(
							'system_design_layouts' => array(
								'display_name' => 'Layouts',
								'href'         => 'admin/design/layout',
							),
						),
					),
					'system_system_clearcache' => array(
						'display_name' => 'Clear Cache',
						'href'         => 'admin/tool/tool/clear_cache',
					),
					'system_system_tools' => array(
						'display_name' => 'System Tools',
						'href'         => 'admin/tool/tool',
					),
					'system_logs'         => array(
						'display_name' => 'Logs',
						'href'         => 'admin/tool/logs',
					),
					'system_localisation' => array(
						'display_name' => 'Localisation',
						'children'     => array(
							'system_localisation_currencies' => array(
								'display_name' => 'Currencies',
								'href'         => 'admin/localisation/currency',
							),
							'system_localisation_languages'  => array(
								'display_name' => 'Languages',
								'href'         => 'admin/localisation/language',
							),
							'system_localisation_zones'      => array(
								'display_name' => 'Zones',
								'href'         => 'admin/localisation/zone',
							),
							'system_localisation_countries'  => array(
								'display_name' => 'Countries',
								'href'         => 'admin/localisation/country',
							),
							'system_localisation_geo_zones'  => array(
								'display_name' => 'Geo Zones',
								'href'         => 'admin/localisation/geo_zone',
							),
						),
					),
				),
			),
		);

		$result = $this->query("SELECT navigation_group_id FROM " . DB_PREFIX . "navigation_group WHERE name = 'admin'");

		if ($result->num_rows) {
			$this->deleteNavigationGroup($result->row['navigation_group_id']);
		}

		$data = array(
			'name'   => 'admin',
			'status' => 1,
			'stores' => array(-1),
			'links'  => $links,
		);

		return $this->addNavigationGroup($data);
	}

	public function validateNavigationGroup($data)
	{
		if (isset($data['name']) && !validate('text', $data['name'], 3, 64)) {
			$this->error['name'] = _l("Navigation Group Name must be between 3 and 64 characters!");
		}

		if (!empty($data['links'])) {
			foreach ($data['links'] as $key => $link) {
				$this->validateNavigationLink($key, $link);
			}
		}

		return empty($this->error);
	}

	public function validateDeleteNavigationGroup()
	{
		return empty($this->error);
	}

	public function validateNavigationLink($navigation_id, $link)
	{
		if (!empty($link['name'])) {
			$link_name = $link['name'];
		} elseif (!empty($link['display_name'])) {
			$link_name = $link['display_name'];
		} else {
			$link_name = $navigation_id;
		}

		if (empty($link_name) || !validate('text', $link_name, 1, 45)) {
			$this->error["links[$navigation_id][name]"] = _l("The name for the link %s must be between 1 and 45 characters!", $link_name);
		}

		if (empty($link['display_name']) || !validate('text', $link['display_name'], 1, 255)) {
			$this->error["links[$navigation_id][display_name]"] = _l("The Display Name for the link %s must be between 1 and 255 characters!", $link_name);
		}

		return empty($this->error);
	}
}
