<?php
class App_Model_Block_Widget_Views extends Model
{
	public function save($view_id, $view)
	{
		if (!isset($view['name'])) {
			$view['name'] = $this->tool->getSlug($view['title']);
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

	public function getViews($path)
	{
		$views = $this->queryRows("SELECT * FROM " . $this->prefix . "view WHERE `path` = '" . $this->escape($path) . "'");

		foreach ($views as &$view) {
			parse_str($view['query'], $view['query']);
		} unset($view);

		return $views;
	}

	public function remove($view_id)
	{
		return $this->delete('view', $view_id);
	}
}