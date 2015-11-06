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

	public function saveGroupLinks($navigation_group_id, $links, $append = true, $overwrite = false)
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

			$navigation_id = $this->queryVar("SELECT navigation_id FROM {$this->t['navigation']} WHERE `name` = '" . $this->escape($link['name']) . "' AND navigation_group_id = " . (int)$navigation_group_id);

			//Create New Link or Overwrite link data
			if (!$navigation_id || $overwrite) {
				$navigation_id = $this->save($navigation_id, $link);
			}

			if ($navigation_id && !empty($link['children'])) {
				foreach ($link['children'] as &$child) {
					$child['parent_id'] = $navigation_id;
				}

				$this->saveGroupLinks($navigation_group_id, $link['children'], true, $overwrite);
			}
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
		$orig_table  = $this->table;
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
		$navigation_groups = null;//cache("navigation_group.$name." . DOMAIN);

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

				$group_links = array();

				foreach ($group['links'] as $key => $link) {
					if (!empty($link['path']) || !empty($link['query'])) {
						$link['href'] = site_url($link['path'], $link['query']);
					}

					$group_links[isset($link['name']) ? $link['name'] : $key] = $link;
				}

				$group['links'] = $group_links;

				$this->toTree($group['links']);
			}
			unset($group);

			cache("navigation_group.$name." . DOMAIN, $navigation_groups);
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
				if (preg_match("/^[a-z0-9_-]+\\/[a-z0-9_-]+/i", $link['path']) && $link['path'] && !user_can('r', $link['path'])) {
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

			if (empty($link['name'])) {
				$link['name'] = $key;
			}

			if (empty($link['navigation_id'])) {
				$link['navigation_id'] = $key;
			}

			$parent_ref[$link['navigation_id']] = &$link;
			$parent_ref[$link['name']] = &$link;

			if (!empty($link['parent_id'])) {
				$parent_ref[$link['parent_id']]['children'][$link['name']] = &$link;
				unset($links[$key]);
			} elseif (!empty($link['parent'])) {
				$parent_ref[$link['parent']]['children'][$link['name']] = &$link;
			}
		}
		unset($link);
	}

	public function restoreAdminNavigation()
	{
		$links = array();
		require_once(DIR_SITE . 'app/model/data/admin_navigation.php');

		return $this->saveGroupLinks('admin', $links);
	}

	public function resetAdminNavigationGroup()
	{
		$links = array();
		require_once(DIR_SITE . 'app/model/data/admin_navigation.php');

		$this->removeGroup('admin');

		$group = array(
			'name'   => 'admin',
			'status' => 1,
			'links'  => $links,
		);

		return $this->saveGroup(null, $group);
	}

	public function getColumns($filter = array(), $merge = array())
	{
		$merge += array(
			'name'   => array(
				'type'   => 'text',
				'label'  => _l("Navigation Group"),
				'filter' => true,
				'sort'   => true,
			),
			'status' => array(
				'type'   => 'select',
				'label'  => _l("Status"),
				'filter' => true,
				'build'  => array(
					'data' => array(
						0 => _l("Disabled"),
						1 => _l("Enabled"),
					),
				),
				'sort'   => true,
			),
		);

		return parent::getColumns($filter, $merge);
	}
}
