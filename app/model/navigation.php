<?php

class App_Model_Navigation extends App_Model_Table
{
	protected $table = 'navigation', $primary_key = 'navigation_id';

	public function getGroupId($navigation_group_id)
	{
		if (is_string($navigation_group_id) && preg_match("/[^\\d]/", $navigation_group_id)) {
			$name                = $navigation_group_id;
			$navigation_group_id = $this->getGroupByName($name);

			if (!$navigation_group_id) {
				return false;
			}
		}

		return $navigation_group_id;
	}

	public function save($navigation_id, $link)
	{
		if (empty($link['display_name'])) {
			$this->error['display_name'] = _l("You must specify the display_name when adding a new navigation link!");
		}

		if (empty($link['name'])) {
			$link['name'] = slug($link['display_name']);
		}

		if (empty($link['navigation_group_id'])) {
			if (empty($link['group'])) {
				$this->error['navigation_group_id'] = _l("You must specify a Navigation Group (either navigation_group_id or group).");
			} else {
				$link['navigation_group_id'] = $this->getGroupByName($link['group']);

				if (!$link['navigation_group_id']) {
					$this->error['group'] = _l("Unknown Navigation Group %s", $link['group']);
				}
			}
		}

		//Link already exists
		if ($this->getLinkByName($link['navigation_group_id'], $link['name'])) {
			$this->error['duplicate'][] = _l("The navigation link %s already exists", $link['name']);
		}

		if ($this->error) {
			return false;
		}

		if (empty($link['parent_id']) && !empty($link['parent'])) {
			$parent = $this->getLinkByName($link['navigation_group_id'], $link['parent']);

			if ($parent) {
				$link['parent_id'] = $parent['navigation_id'];
			}
		}

		if (!isset($link['status'])) {
			$link['status'] = 1;
		}

		clear_cache('navigation');

		if ($navigation_id) {
			$navigation_id = $this->update("navigation", $link, $navigation_id);
		} else {
			$navigation_id = $this->insert("navigation", $link);
		}

		if ($navigation_id) {
			if (!empty($link['children'])) {
				$sort_order = 0;

				foreach ($link['children'] as $name => $child) {
					$child['parent_id'] = $navigation_id;

					if (empty($child['name'])) {
						$child['name'] = $name;
					}

					if (!isset($child['sort_order'])) {
						$child['sort_order'] = $sort_order++;
					}

					if (!isset($child['navigation_group_id'])) {
						$child['navigation_group_id'] = $link['navigation_group_id'];
					}

					$this->save(null, $child);
				}
			}
		}

		return $navigation_id;
	}

	public function saveGroup($navigation_group_id, $group)
	{
		if (is_string($navigation_group_id)) {
			$group['name']       = $navigation_group_id;
			$navigation_group_id = $this->getGroupByName($group['name']);
		}

		if (isset($group['name'])) {
			if (!validate('text', $group['name'], 3, 64)) {
				$this->error['name'] = _l("Navigation Group Name must be between 3 and 64 characters!");
			}

			if (!$navigation_group_id && $this->getGroupByName($group['name'])) {
				$this->error['name'] = _l("A Group with that name already exists!");
			}
		} elseif (!$navigation_group_id) {
			$this->error['name'] = _l("Navigation Group Name is required!");
		}

		if ($this->error) {
			return false;
		}

		if (!isset($group['status'])) {
			$group['status'] = 1;
		}

		if ($navigation_group_id) {
			if (isset($group['links'])) {
				$this->delete("navigation", array("navigation_group_id" => $navigation_group_id));
			}

			$navigation_group_id = $this->update("navigation_group", $group, $navigation_group_id);
		} else {
			$navigation_group_id = $this->insert("navigation_group", $group);
		}

		//Add Links
		if (!empty($group['links'])) {
			$this->toTree($group['links']);

			$this->saveGroupLinks($navigation_group_id, $group['links'], false);
		}

		clear_cache('navigation');

		return $navigation_group_id;
	}

	public function saveGroupLinks($navigation_group_id, $links, $append = true)
	{
		$navigation_group_id = $this->getGroupId($navigation_group_id);

		if (!$navigation_group_id) {
			$this->error['group'] = _l("Unknown Navigation Group");
			return false;
		}

		if (!$append) {
			$this->delete('navigation', array('navigation_group_id' => $navigation_group_id));
		}

		$sort_order = 0;

		foreach ($links as $name => $link) {
			if (empty($link['name'])) {
				$link['name'] = $name;
			}

			if (!isset($link['sort_order'])) {
				$link['sort_order'] = $sort_order++;
			}

			$link['navigation_group_id'] = $navigation_group_id;

			$this->save(null, $link);
		}

		return true;
	}

	public function removeGroup($navigation_group_id)
	{
		$navigation_group_id = $this->getGroupId($navigation_group_id);

		if (!$navigation_group_id) {
			$this->error['group'] = _l("Unknown Navigation Group");
			return false;
		}

		$this->delete("navigation_group", $navigation_group_id);
		$this->delete("navigation", array("navigation_group_id" => $navigation_group_id));

		clear_cache('navigation');

		return true;
	}

	public function remove($navigation_id)
	{
		if (!$navigation_id) {
			$this->error['navigation_id'] = _l("Cannot remove Navigation ID 0");
			return false;
		}

		clear_cache('navigation');

		$children = $this->queryColumn("SELECT navigation_id FROM {$this->t['navigation']} WHERE parent_id = " . (int)$navigation_id);

		foreach ($children as $child_id) {
			$this->remove($child_id);
		}

		return $this->delete("navigation", $navigation_id);
	}

	public function removeGroupLink($navigation_group_id, $name)
	{
		$navigation_group_id = $this->getGroupId($navigation_group_id);

		if (!$navigation_group_id) {
			$this->error['group'] = _l("Unknown Navigation Group");
			return false;
		}

		$navigation_id = $this->queryVar("SELECT navigation_id FROM {$this->t['navigation']} WHERE `name` = '" . $this->escape($name) . "' AND navigation_group_id = " . (int)$navigation_group_id);

		return $this->remove($navigation_id);
	}

	public function removeGroupLinks($group, $links)
	{
		$navigation_group_id = $this->getGroupId($group);

		foreach ($links as $name => $link) {
			$this->removeGroupLink($navigation_group_id, isset($link['name']) ? $link['name'] : $name);
		}

		return empty($this->error);
	}

	public function getLinkByName($navigation_group_id, $name)
	{
		return $this->queryRow("SELECT * FROM " . $this->t[$this->table] . " WHERE navigation_group_id = " . (int)$navigation_group_id . " AND `name` = '" . $this->escape($name) . "'");
	}

	public function getGroupByName($name)
	{
		return $this->queryVar("SELECT navigation_group_id FROM {$this->t['navigation_group']} WHERE `name` = '" . $this->escape($name) . "'");
	}

	public function getGroup($navigation_group_id)
	{
		$group = $this->queryRow("SELECT * FROM {$this->t['navigation_group']} WHERE navigation_group_id = " . (int)$navigation_group_id);

		$group['links'] = $this->getGroupLinks($navigation_group_id);

		return $group;
	}

	public function getGroups($sort = array(), $filter = array(), $options = array(), $total = false)
	{
		$orig_table = $this->table;
		$this->table = 'navigation_group';

		$records = parent::getRecords($sort, $filter, $options, $total);

		$this->table = $orig_table;

		$total ? $rows = &$records[0] : $rows = &$records;

		foreach ($rows as &$row) {
			$row['links'] = $this->getGroupLinks($row['navigation_group_id']);
		}
		unset($row);

		return $records;
	}

	public function getNavigationGroup($name = 'all')
	{
		$navigation_groups = cache("navigation_group.$name");

		if (!isset($navigation_groups)) {
			$filter = array(
				'status' => 1,
			);

			if ($name === 'all') {
				$filter['!name'] = 'admin';
			} else {
				$filter['name'] = $name;
			}

			$navigation_groups = $this->getGroups(null, $filter, array('index' => 'name'));

			foreach ($navigation_groups as &$group) {
				if (empty($group['links'])) {
					continue;
				}

				foreach ($group['links'] as $key => &$link) {
					if (!empty($link['path']) || !empty($link['query'])) {
						$link['href'] = site_url($link['path'], $link['query']);
					}
				}
				unset($link);

				$this->toTree($group['links']);
			}
			unset($group);

			cache("navigation_group.$name", $navigation_groups);
		}

		//Filter Conditional Links And Access Permissions
		//TODO: This leaves null values in group links. Consider changing approach.
		foreach ($navigation_groups as &$group) {
			$this->checkLinks($group['links']);
		}
		unset($group);

		return $navigation_groups;
	}

	public function getGroupLinks($navigation_group_id)
	{
		return $this->queryRows("SELECT * FROM {$this->t['navigation']} WHERE navigation_group_id = '" . (int)$navigation_group_id . "' ORDER BY parent_id, sort_order ASC", 'navigation_id');
	}

	public function getTotalGroups($filter)
	{
		return $this->getGroups(null, $filter, 'COUNT(*)');
	}

	public function checkLinks(&$links)
	{
		$is_active  = false;
		$has_active = false;

		foreach ($links as &$link) {
			if (!empty($link['children'])) {
				$has_active = $this->checkLinks($link['children']);
			}

			//Filter by Conditions
			if (!empty($link['condition']) && !check_condition($link['condition'])) {
				$link['active'] = false;
				continue;
			}

			//Filter restricted paths, current user cannot access
			if (IS_ADMIN) {
				if ($link['path'] && !user_can('r', $link['path'])) {
					$link['active'] = false;
					continue;
				}
			}

			//Filter empty non-links
			if (!$has_active && empty($link['path'])) {
				$link['active'] = false;
				continue;
			}

			$link['active'] = true;
			$is_active      = true;
		}

		return $is_active;
	}

	public function toTree(&$links)
	{
		$parent_ref = array();

		foreach ($links as $key => &$link) {
			if (!isset($link['children'])) {
				$link['children'] = array();
			}

			if (!empty($link['navigation_id'])) {
				$parent_ref[$link['navigation_id']] = &$link;
			} else {
				$parent_ref[$key] = &$link;
			}

			if (empty($link['name'])) {
				$link['name'] = $key;
			}

			$parent_ref[$link['name']] = &$link;

			if (!empty($link['parent_id'])) {
				$parent_ref[$link['parent_id']]['children'][] = &$link;
				unset($links[$key]);
			} elseif (!empty($link['parent'])) {
				$parent_ref[$link['parent']]['children'][] = &$link;
			}
		}
		unset($link);
	}

	public function resetAdminNavigationGroup()
	{
		$links = array(
			'home'       => array(
				'display_name' => 'Home',
				'path'         => '',
			),

			'dashboards' => array(
				'display_name' => "Dashboards",
				'path'         => 'admin/dashboard',
			),

			'content'    => array(
				'display_name' => 'Content',
				'children'     => array(
					'content_blocks' => array(
						'display_name' => 'Blocks',
						'path'         => 'admin/block',
					),
					'content_pages'  => array(
						'display_name' => 'Pages',
						'path'         => 'admin/page',
					),
				),
			),

			'users'      => array(
				'display_name' => 'Users',
				'children'     => array(
					'users_users'      => array(
						'display_name' => 'Users',
						'path'         => 'admin/user',
					),
					'users_user_roles' => array(
						'display_name' => 'User Roles',
						'path'         => 'admin/settings/role',
					),
				),
			),

			'system'     => array(
				'display_name' => 'System',
				'children'     => array(
					'system_settings'          => array(
						'display_name' => 'Settings',
						'path'         => 'admin/settings',
					),
					'system_mail'              => array(
						'display_name' => 'Mail',
						'children'     => array(
							'system_mail_send_email'    => array(
								'display_name' => 'Send Email',
								'path'         => 'admin/mail/send_email',
							),
							'system_mail_mail_messages' => array(
								'display_name' => 'Mail Messages',
								'path'         => 'admin/mail/messages',
							),
							'system_mail_error'         => array(
								'display_name' => 'Failed Messages',
								'path'         => 'admin/mail/error',
							),
						),
					),
					'system_views'             => array(
						'display_name' => 'Views',
						'path'         => 'admin/view',
					),
					'system_navigation'        => array(
						'display_name' => 'Navigation',
						'path'         => 'admin/navigation',
					),
					'system_system_clearcache' => array(
						'display_name' => 'Clear Cache',
						'path'         => 'admin/settings/clear_cache',
						'query'        => 'redirect',
					),
					'system_logs'              => array(
						'display_name' => 'Logs',
						'path'         => 'admin/logs',
					),
					'system_history'           => array(
						'display_name' => 'History',
						'path'         => 'admin/history',
					),
					'system_localisation'      => array(
						'display_name' => 'Localisation',
						'children'     => array(
							'system_localisation_currencies' => array(
								'display_name' => 'Currencies',
								'path'         => 'admin/localisation/currency',
							),
							'system_localisation_languages'  => array(
								'display_name' => 'Languages',
								'path'         => 'admin/localisation/language',
							),
							'system_localisation_zones'      => array(
								'display_name' => 'Zones',
								'path'         => 'admin/localisation/zone',
							),
							'system_localisation_countries'  => array(
								'display_name' => 'Countries',
								'path'         => 'admin/localisation/country',
							),
							'system_localisation_geo_zones'  => array(
								'display_name' => 'Geo Zones',
								'path'         => 'admin/localisation/geo_zone',
							),
						),
					),

					'system_plugins'    => array(
						'display_name' => 'Plugins',
						'path'         => 'admin/plugin',
					),
				),
			),
		);

		$this->removeGroup('admin');

		$group = array(
			'name'   => 'admin',
			'status' => 1,
			'links'  => $links,
		);

		return $this->saveGroup(null, $group);
	}
}
