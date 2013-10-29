<?php
class Extend extends Library
{
	public function addNavigationLink($group, $link)
	{
		$defaults = array(
			'title'      => '',
			'href'       => '',
			'query'      => '',
			'parent'     => '',
			'parent_id'  => 0,
			'parent'     => '',
			'sort_order' => 0,
			'status'     => 1,
		);

		$link += $defaults;

		if (empty($link['display_name'])) {
			$this->message->add("warning", __METHOD__ . "(): " . _("You must specify the display_name when adding a new navigation link!"));

			return false;
		}

		if (!$link['name']) {
			$link['name'] = $this->tool->getSlug($link['display_name']);
		}

		//Link already exists
		if ($this->db->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "navigation WHERE name = '" . $this->db->escape($link['name']) . "'")) {
			return 'exists';
		}

		if (!$link['parent_id'] && $link['parent']) {
			$link['parent_id'] = $this->db->queryVar("SELECT navigation_id FROM " . DB_PREFIX . "navigation WHERE name = '" . $this->db->escape($link['parent']) . "'");
		}

		$navigation_group_id = $this->db->queryVar("SELECT navigation_group_id FROM " . DB_PREFIX . "navigation_group WHERE name = '" . $this->db->escape($group) . "'");

		if ($navigation_group_id) {
			$this->Admin_Model_Design_Navigation->addNavigationLink($navigation_group_id, $link);
		}
		else {
			$this->message->add('warning', __METHOD__ . "(): " . _("The Navigation Group $group does not exist!"));
			return false;
		}

		return true;
	}

	public function removeNavigationLink($group, $name)
	{
		$query = "SELECT navigation_id FROM " . DB_PREFIX . "navigation n" .
			" LEFT JOIN " . DB_PREFIX . "navigation_group ng ON (ng.navigation_group_id=n.navigation_group_id)" .
			" WHERE ng.name = '" . $this->db->escape($group) . "' AND n.name = '" . $this->db->escape($name) . "'";

		$navigation_ids = $this->db->queryColumn($query);

		foreach ($navigation_ids as $navigation_id) {
			$this->Admin_Model_Design_Navigation->deleteNavigationLink($navigation_id);
		}
	}

	public function add_layout($name, $routes = array(), $data = array())
	{
		if (!is_array($routes)) {
			$routes = array($routes);
		}

		$exists = $this->db->queryVar("SELECT COUNT(*) as total FROM " . DB_PREFIX . "layout WHERE name='$name'");

		if ($exists) {
			$this->message->add("warning", "Error while adding $name to layout! Duplicate name exists!");
			return false;
		}

		$layout = array(
			'name' => $name,
		);

		$layout += $data;

		if (!empty($routes)) {
			$stores = $this->Admin_Model_Setting_Store->getStores();

			foreach ($stores as $store) {
				foreach ($routes as $route) {
					$layout['layout_route'][] = array(
						'store_id' => $store['store_id'],
						'route'    => $route
					);
				}
			}
		}

		return $this->Admin_Model_Design_Layout->addLayout($layout);
	}

	//TODO: This should remove based on a unique ID not the name...
	public function remove_layout($name)
	{
		$result = $this->db->query("SELECT layout_id FROM " . DB_PREFIX . "layout WHERE name='" . $this->db->escape($name) . "' LIMIT 1");

		if ($result->num_rows) {
			$this->Admin_Model_Design_Layout->deleteLayout($result->row['layout_id']);
		}
	}

	public function add_db_hook($hook_id, $action, $table, $callback, $param = null, $priority = 0)
	{
		$config_id = 'db_hook_' . $action . '_' . $table;

		$hooks = $this->config->get($config_id);

		if (!is_array($hooks)) {
			$hooks = array();
		}

		//We do not want to add multiple of the same hook!
		foreach ($hooks as $hook) {
			if ($hook['hook_id'] === $hook_id) {
				return;
			}
		}

		$hooks[] = array(
			'hook_id'  => $hook_id,
			'callback' => $callback,
			'param'    => $param,
			'priority' => $priority,
		);

		$this->config->save('db_hook', $config_id, $hooks);
	}

	public function remove_db_hook($hook_id)
	{
		$db_hooks = $this->config->loadGroup('db_hook');

		foreach ($db_hooks as $hook_key => $hook) {
			foreach ($hook as $h_key => $h) {
				if ($h['hook_id'] === $hook_id) {
					unset($db_hooks[$hook_key][$h_key]);
				}
			}

			if (empty($db_hooks[$hook_key])) {
				unset($db_hooks[$hook_key]);
			}
		}

		$this->config->saveGroup('db_hook', $db_hooks);
	}

	public function addControllerOverride($original, $alternate, $condition = '', $store_id = 0)
	{
		$overrides = $this->config->load('controller_override', 'controller_override', $store_id);

		if (!$overrides) {
			$overrides = array();
		}

		$overrides[] = array(
			'original'  => $original,
			'alternate' => $alternate,
			'condition' => $condition,
		);

		$this->config->save('controller_override', 'controller_override', $overrides, $store_id);

		$overrides = $this->config->load('controller_override', 'controller_override');
	}

	public function removeControllerOverride($original, $alternate, $condition = '', $store_id = 0)
	{
		$overrides = $this->config->load('controller_override', 'controller_override', $store_id);

		if ($overrides) {
			foreach ($overrides as $key => $override) {
				if ($override['original'] === $original && $override['alternate'] === $alternate && (string)$override['condition'] === $condition) {
					unset($overrides[$key]);
				}
			}

			$this->config->save('controller_override', 'controller_override', $overrides, $store_id);
		}
	}

	public function enable_image_sorting($table, $column)
	{
		$hook_id = '__image_sort__' . $table . '_' . $column;

		$this->add_db_hook($hook_id, 'insert', $table, array('Extend' => 'update_hsv_value'), array(
		                                                                                           $table,
		                                                                                           $column
		                                                                                      ));
		$this->add_db_hook($hook_id, 'update', $table, array('Extend' => 'update_hsv_value'), array(
		                                                                                           $table,
		                                                                                           $column
		                                                                                      ));

		$sort_column = '__image_sort__' . $column;

		$this->db->addColumn($table, $sort_column, 'FLOAT NULL');

		$key_column = $this->db->getKeyColumn($table);

		$rows = $this->db->queryRows("SELECT $key_column, $column, $sort_column FROM " . DB_PREFIX . "$table");

		foreach ($rows as $row) {
			$this->update_hsv_value($row, $table, $column, true);

			$this->db->query("UPDATE " . DB_PREFIX . "$table SET `$sort_column` = '$row[$sort_column]' WHERE `$key_column` = '$row[$key_column]'");
		}
	}

	public function disable_image_sorting($table, $column)
	{
		$hook_id = '__image_sort__' . $table . '_' . $column;

		$this->remove_db_hook($hook_id);

		$this->db->dropColumn($table, '__image_sort__' . $column);
	}

	public function update_hsv_value(&$data, $table, $column, $force = false)
	{
		if (!isset($data[$column])) {
			return;
		}

		//If the image has not changed, do nothing.
		if (!$force && $this->db->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "{$table} WHERE `{$column}` = '{$data[$column]}'")) {
			return;
		}

		$width  = $this->config->get('config_image_admin_list_width');
		$height = $this->config->get('config_image_admin_list_height');

		//Performance Optimization: Much quicker to resize (plus caching) than evaluate color or large image
		$image = str_replace(HTTP_IMAGE, DIR_IMAGE, $this->image->resize($data[$column], $width, $height));

		$colors = $this->image->get_dominant_color($image);

		$HSV                              = $this->image->RGB_to_HSV($colors['r'], $colors['g'], $colors['b']);
		$data['__image_sort__' . $column] = $HSV['H'];
	}
}
