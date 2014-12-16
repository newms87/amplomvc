<?php
class Extend extends Library
{
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
			foreach ($routes as $route) {
				$layout['layout_route'][] = array(
					'route'    => $route
				);
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
