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
			$this->error['display_name'] = _l("You must specify the display_name when adding a new navigation link!");
			return false;
		}

		if (empty($link['name'])) {
			$link['name'] = slug($link['display_name']);
		}

		//Link already exists
		if ($this->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "navigation WHERE name = '" . $this->escape($link['name']) . "'")) {
			$this->error['duplicate'] = _l("The navigation link %s already exists", $link['name']);
			return false;
		}

		if (!$link['parent_id'] && $link['parent']) {
			$link['parent_id'] = $this->queryVar("SELECT navigation_id FROM " . DB_PREFIX . "navigation WHERE name = '" . $this->escape($link['parent']) . "'");
		}

		$navigation_group_id = $this->queryVar("SELECT navigation_group_id FROM " . DB_PREFIX . "navigation_group WHERE name = '" . $this->escape($group) . "'");

		if ($navigation_group_id) {
			$this->Model_Design_Navigation->addNavigationLink($navigation_group_id, $link);
		} else {
			$this->error['navigation_group'] = _l("The Navigation Group $group does not exist!");
			return false;
		}

		if (!empty($link['children'])) {
			foreach ($link['children'] as $child) {
				$child['parent'] = $link['name'];
				$this->addNavigationLink($group, $child);
			}
		}

		return true;
	}

	public function addNavigationLinks($group, $links)
	{
		foreach ($links as $name => $link) {
			if (!isset($link['name']) && is_string($name)) {
				$link['name'] = $name;
			}

			$this->addNavigationLink($group, $link);
		}

		return empty($this->error);
	}

	public function removeNavigationLink($group, $name)
	{
		$query = "SELECT navigation_id FROM " . DB_PREFIX . "navigation n" .
			" LEFT JOIN " . DB_PREFIX . "navigation_group ng ON (ng.navigation_group_id=n.navigation_group_id)" .
			" WHERE ng.name = '" . $this->escape($group) . "' AND n.name = '" . $this->escape($name) . "'";

		$navigation_ids = $this->queryColumn($query);

		foreach ($navigation_ids as $navigation_id) {
			$this->Model_Design_Navigation->deleteNavigationLink($navigation_id);
		}
	}

	public function removeNavigationLinks($group, $links)
	{
		foreach ($links as $name => $link) {
			$this->removeNavigationLink($group, isset($link['name']) ? $link['name'] : $name);
		}
	}

	public function addViewListing($view_listing)
	{
		$view_listing_id = $this->Model_View->saveViewListing(null, $view_listing);

		if (!$view_listing_id) {
			$this->error = $this->Model_View->getError();
		}

		return $view_listing_id;
	}

	public function removeViewListing($name)
	{
		$view_listing = $this->Model_View->getViewListingBySlug(slug($name));

		if ($view_listing) {
			$view_listing_id = $this->Model_View->removeViewListing($view_listing['view_listing_id']);

			if (!$view_listing_id) {
				$this->error = $this->Model_View->getError();
			}

			return $view_listing_id;
		} else {
			$this->error['name'] = _l("Could not locate View Listing with name %s", $name);
			return false;
		}
	}

	public function addLayout($name, $routes = array(), $data = array())
	{
		if (!is_array($routes)) {
			$routes = array($routes);
		}

		$exists = $this->queryVar("SELECT COUNT(*) as total FROM " . DB_PREFIX . "layout WHERE name='$name'");

		if ($exists) {
			message("warning", "Error while adding $name to layout! Duplicate name exists!");
			return false;
		}

		$layout = array(
			'name' => $name,
		);

		$layout += $data;

		if (!empty($routes)) {
			$stores = $this->Model_Setting_Store->getStores();

			foreach ($stores as $store) {
				foreach ($routes as $route) {
					$layout['layout_route'][] = array(
						'store_id' => $store['store_id'],
						'route'    => $route
					);
				}
			}
		}

		return $this->Model_Design_Layout->addLayout($layout);
	}

	//TODO: This should remove based on a unique ID not the name...
	public function removeLayout($name)
	{
		$result = $this->query("SELECT layout_id FROM " . DB_PREFIX . "layout WHERE name='" . $this->escape($name) . "' LIMIT 1");

		if ($result->num_rows) {
			$this->Model_Design_Layout->deleteLayout($result->row['layout_id']);
		}
	}

	public function addHook($hook_id, $action, $table, $callback, $param = null, $priority = 0)
	{
		$config_id = 'db_hook_' . $action . '_' . $table;

		$hooks = option($config_id);

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

	public function removeHook($hook_id)
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

		$this->addHook($hook_id, 'insert', $table, array('Extend' => 'update_hsv_value'), array(
			$table,
			$column
		));
		$this->addHook($hook_id, 'update', $table, array('Extend' => 'update_hsv_value'), array(
			$table,
			$column
		));

		$sort_column = '__image_sort__' . $column;

		$this->addColumn($table, $sort_column, 'FLOAT NULL');

		$key_column = $this->getKeyColumn($table);

		$rows = $this->queryRows("SELECT $key_column, $column, $sort_column FROM " . DB_PREFIX . "$table");

		foreach ($rows as $row) {
			$this->update_hsv_value($row, $table, $column, true);

			$this->query("UPDATE " . DB_PREFIX . "$table SET `$sort_column` = '$row[$sort_column]' WHERE `$key_column` = '$row[$key_column]'");
		}
	}

	public function disableImageSorting($table, $column)
	{
		$hook_id = '__image_sort__' . $table . '_' . $column;

		$this->removeHook($hook_id);

		$this->dropColumn($table, '__image_sort__' . $column);
	}

	public function update_hsv_value(&$data, $table, $column, $force = false)
	{
		if (!isset($data[$column])) {
			return;
		}

		//If the image has not changed, do nothing.
		if (!$force && $this->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "{$table} WHERE `{$column}` = '{$data[$column]}'")) {
			return;
		}

		$width  = option('config_image_admin_list_width');
		$height = option('config_image_admin_list_height');

		//Performance Optimization: Much quicker to resize (plus caching) than evaluate color or large image
		$image = str_replace(URL_IMAGE, DIR_IMAGE, image($data[$column], $width, $height));

		$colors = $this->image->get_dominant_color($image);

		$HSV                              = $this->image->RGB_to_HSV($colors['r'], $colors['g'], $colors['b']);
		$data['__image_sort__' . $column] = $HSV['H'];
	}
}
