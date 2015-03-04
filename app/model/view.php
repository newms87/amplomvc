<?php

class App_Model_View extends App_Model_Table
{
	protected $table = 'view', $primary_key = 'view_id';
	static $view_listings;
	static $meta = array();

	public function save($view_id, $view)
	{
		if (isset($view['query'])) {
			if (!is_string($view['query'])) {
				$view['query'] = http_build_query($view['query']);
			} else {
				$view['query'] = html_entity_decode(urldecode($view['query']));
			}
		}

		clear_cache('view');
		self::$view_listings = null;

		if (!empty($view['settings'])) {
			if ($view_id) {
				$view['settings'] += (array)$this->getViewSettings($view_id);
			}

			$view['settings'] = serialize($view['settings']);
		}

		if ($view_id) {
			return $this->update('view', $view, $view_id);
		} else {
			if (empty($view['group'])) {
				$this->error = _l("View group is required");
				return false;
			}

			if (!isset($view['name'])) {
				$view['name'] = slug($view['title']);
			}

			if (!isset($view['sort_order'])) {
				$view['sort_order'] = (int)$this->queryVar("SELECT MAX(sort_order) FROM {$this->t['view']} WHERE `group` = '" . $this->escape($view['group']) . "'") + 1;
			}

			return $this->insert('view', $view);
		}
	}

	public function getRecord($view_id, $select = '*')
	{
		$view = parent::getRecord($view_id, $select);

		if (!empty($view['settings'])) {
			$view['settings'] = unserialize($view['settings']);
		}

		return $view;
	}

	public function getViewSettings($view_id)
	{
		$settings = $this->queryVar("SELECT settings FROM {$this->t['view']} WHERE view_id = " . (int)$view_id);

		if ($settings) {
			$settings = unserialize($settings);

			if (is_array($settings)) {
				return $settings;
			}
		}

		return array();
	}

	public function saveViewSetting($view_id, $key, $value = null)
	{
		$settings = $this->getViewSettings($view_id);

		if (!is_array($settings)) {
			$settings = array();
		}

		if ($value === null) {
			unset($settings[$key]);
		} else {
			$settings[$key] = $value;
		}

		return $this->saveViewSettings($view_id, $settings);
	}

	public function saveViewSettings($view_id, $settings)
	{
		clear_cache('view');
		self::$view_listings = null;

		return $this->update('view', array('settings' => serialize($settings)), $view_id);
	}

	public function getViews($group)
	{
		$views = $this->queryRows("SELECT * FROM {$this->t['view']} WHERE `group` = '" . $this->escape($group) . "'");

		foreach ($views as &$view) {
			parse_str($view['query'], $view['query']);

			if (!empty($view['settings'])) {
				$view['settings'] = unserialize($view['settings']);
			}
		}
		unset($view);

		sort_by($views, 'sort_order');

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
		self::$view_listings = null;

		return parent::remove($view_id);
	}

	public function getViewMeta($view_id, $key = null, $single = true)
	{
		if (!isset(self::$meta[$view_id])) {
			$rows = $this->queryRows("SELECT * FROM {$this->t['view_meta']} WHERE view_id = " . (int)$view_id);

			foreach ($rows as $row) {
				self::$meta[$view_id][$row['key']][] = $row['serialized'] ? unserialize($row['value']) : $row['value'];
			}
		}

		if ($key) {
			if (isset(self::$meta[$view_id][$key])) {
				return $single ? current(self::$meta[$view_id][$key]) : self::$meta[$view_id][$key];
			}
		} elseif (isset(self::$meta[$view_id])) {
			return self::$meta[$view_id];
		}

		return null;
	}

	public function saveViewMeta($view_id, $key, $value = null, $single = true)
	{
		if (_is_object($value)) {
			$value      = serialize($value);
			$serialized = 1;
		} else {
			$serialized = 0;
		}

		self::$meta = array();

		if ($single) {
			$this->delete('view_meta', array('view_id' => $view_id));

			if ($value === null) {
				return true;
			}
		}

		$view_meta = array(
			'view_id'    => $view_id,
			'key'        => $key,
			'value'      => $value,
			'serialized' => $serialized,
			'date'       => $this->date->now(),
		);

		return $this->insert('view_meta', $view_meta);
	}

	public function updateViewMeta($view_meta_id, $key, $value = null)
	{
		if (_is_object($value)) {
			$value      = serialize($value);
			$serialized = 1;
		} else {
			$serialized = 0;
		}

		self::$meta = array();

		if ($value === null) {
			return $this->delete('view_meta', $view_meta_id);
		}

		$view_meta = array(
			'value'      => $value,
			'serialized' => $serialized,
			'date'       => $this->date->now(),
		);

		return $this->update('view_meta', $view_meta, $view_meta_id);
	}
}
