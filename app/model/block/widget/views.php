<?php
class App_Model_Block_Widget_Views extends Model
{
	public function save($view_id, $view)
	{
		if (!isset($view['group'])) {
			$this->error = _l("View group is required");
			return false;
		}

		if (!isset($view['name'])) {
			$view['name'] = slug($view['title']);
		}

		if (!is_string($view['query'])) {
			$view['query'] = http_build_query($view['query']);
		} else {
			$view['query'] = html_entity_decode(urldecode($view['query']));
		}

		if ($view_id) {
			return $this->update('view', $view, $view_id);
		} else {
			return $this->insert('view', $view);
		}
	}

	public function getView($view_id)
	{
		return $this->queryRow("SELECT * FROM " . $this->prefix . "view WHERE view_id = " . (int)$view_id);
	}

	public function getViews($group)
	{
		$views = $this->queryRows("SELECT * FROM " . $this->prefix . "view WHERE `group` = '" . $this->escape($group) . "'");

		foreach ($views as &$view) {
			parse_str($view['query'], $view['query']);
		} unset($view);

		return $views;
	}

	public function removeGroup($group)
	{
		$views = $this->getViews($group);

		foreach ($views as $view) {
			$this->remove($view['view_id']);
		}

		return true;
	}

	public function remove($view_id)
	{
		return $this->delete('view', $view_id);
	}
}