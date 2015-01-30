<?php

class App_Model_Layout extends App_Model_Table
{
	protected $table = 'layout', $primary_key = 'layout_id';

	public function save($layout_id, $layout)
	{
		if (isset($layout['name'])) {
			if (!validate('text', $layout['name'], 3, 64)) {
				$this->error['name'] = _l("Layout Name must be between 3 and 64 characters!");
			}
		} elseif (!$layout_id) {
			$this->error['name'] = _l("Layout name is required.");
		}

		if ($this->error) {
			return false;
		}

		if ($layout_id) {
			$layout_id = $this->update('layout', $layout, $layout_id);
		} else {
			$layout_id = $this->insert('layout', $layout);
		}

		if (isset($layout['routes'])) {
			$this->delete('layout_route', array('layout_id' => $layout_id));

			foreach ($layout['routes'] as $route) {
				$route['layout_id'] = $layout_id;

				$this->insert('layout_route', $route);
			}
		}

		clear_cache('layout');

		return $layout_id;
	}

	public function remove($layout_id)
	{
		$this->delete('layout', $layout_id);
		$this->delete('layout_route', array('layout_id' => $layout_id));
		clear_cache('layout');

		return true;
	}

	public function getRoutes($layout_id)
	{
		return $this->queryRows("SELECT * FROM {$this->t['layout_route']} WHERE layout_id = '" . (int)$layout_id . "'");
	}

	public function getLayoutRoutes()
	{
		return $this->queryRows("SELECT * FROM {$this->t['layout_route']} ORDER BY `route` ASC");
	}
}
