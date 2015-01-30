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

	public function getView($view_id)
	{
		$view = $this->queryRow("SELECT * FROM {$this->t['view']} WHERE view_id = " . (int)$view_id);

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
		clear_cache('view');
		self::$view_listings = null;

		return $this->delete('view', $view_id);
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

	/*********************
	 *   View Listings   *
	 *********************/

	public function saveViewListing($view_listing_id, $view_listing)
	{
		if (!$view_listing_id || isset($view_listing['name'])) {
			if (!validate('text', $view_listing['name'], 2, 45)) {
				$this->error['name'] = _l("The name must be between 2 and 45 characters");
				return false;
			}

			if (!$view_listing_id) {
				if ($this->queryVar("SELECT COUNT(*) FROM {$this->t['view_listing']} WHERE `name` = '" . $this->escape($view_listing['name']) . "'")) {
					$this->error['name'] = _l("A View Listing with the name %s already exists.", $view_listing['name']);
					return false;
				}
			}
		}

		if (!$view_listing_id && empty($view_listing['slug'])) {
			$view_listing['slug'] = slug($view_listing['name']);
		}

		self::$view_listings = null;
		clear_cache('view_listing');

		//Create the SQL View
		if (!empty($view_listing['sql'])) {
			$slug = !empty($view_listing['slug']) ? $view_listing['slug'] : $this->queryField('view_listing', 'slug', $view_listing_id);
			$this->query("DROP VIEW IF EXISTS `" . $this->t[$slug] . "`");

			$display_errors = ini_get('display_errors');
			ini_set('display_errors', 0);

			$result = $this->query("CREATE VIEW `" . $this->t[$slug] . "` AS " . $view_listing['sql']);

			ini_set('display_errors', $display_errors);

			if (!$result) {
				$this->error['sql'] = _l("Invalid SELECT statement.<br /><Br /> %s", $this->db->getQueryError());
				return false;
			}

			//TODO- this can probably be optimized
			clear_cache('model');
		}

		if ($view_listing_id) {
			unset($view_listing['slug']);
			$view_listing_id = $this->update('view_listing', $view_listing, $view_listing_id);
		} else {
			if (empty($view_listing['slug'])) {
				$view_listing['slug'] = slug($view_listing['name']);
			}

			$view_listing_id = $this->insert('view_listing', $view_listing);
		}

		return $view_listing_id;
	}

	public function syncViewListing($view_listing)
	{
		if (empty($view_listing['name'])) {
			$this->error['name'] = _l("Must provide a name");
		}

		if (empty($view_listing['path'])) {
			$this->error['path'] = _l("Must provide a path");
		}

		if (!empty($this->error)) {
			return false;
		}

		$view_listing_id = $this->queryVar("SELECT view_listing_id FROM {$this->t['view_listing']} WHERE `name` = '" . $this->escape($view_listing['name']) . "' AND `path` = '" . $this->escape($view_listing['path']) . "'");

		if (!$view_listing_id) {
			$view_listing_id = $this->saveViewListing(null, $view_listing);
		}

		return $view_listing_id;
	}

	public function removeViewListing($view_listing_id)
	{
		self::$view_listings = null;
		clear_cache('view_listing');

		$this->delete('view', array('view_listing_id' => $view_listing_id));

		return $this->delete('view_listing', $view_listing_id);
	}

	public function getViewListingRecords($view_listing_id, $sort = array(), $filter = array(), $select = null, $total = false, $index = null)
	{
		$table = $this->getViewListingTable($view_listing_id);

		if (!$table) {
			$this->error['table'] = _l("The view listing with ID (%s) did not exist.", (int)$view_listing_id);
			return false;
		}

		$select = $this->extractSelect($table, $select);

		//From
		$from = $this->t[$table];

		//Where
		$where = $this->extractWhere($table, $filter);

		//Order By & Limit
		list($order, $limit) = $this->extractOrderLimit($sort);

		//The Query
		return $this->queryRows("SELECT $select FROM $from WHERE $where $order $limit", $index, $total);
	}

	public function getTotalViewListingRecords($view_listing_id, $filter = array())
	{
		return $this->getViewListingRecords($view_listing_id, array(), $filter, 'COUNT(*)');
	}

	public function getViewListingTable($view_listing_id)
	{
		return $this->queryVar("SELECT slug FROM {$this->t['view_listing']} WHERE view_listing_id = " . (int)$view_listing_id);
	}

	public function getViewListingBySlug($slug)
	{
		if (!self::$view_listings) {
			$this->getAllViewListings();
		}

		return array_search_key('slug', $slug, self::$view_listings);
	}

	public function getViewListing($view_listing_id)
	{
		if (_is_object($view_listing_id)) {
			return $view_listing_id;
		}

		if (!self::$view_listings) {
			$this->getAllViewListings();
		}

		return isset(self::$view_listings[$view_listing_id]) ? self::$view_listings[$view_listing_id] : null;
	}

	public function getAllViewListings()
	{
		if (!self::$view_listings) {
			self::$view_listings = cache('view_listings');

			if (!self::$view_listings) {
				$sort = array(
					'name' => 'ASC',
				);

				self::$view_listings = $this->getViewListings($sort, null, '*', false, 'view_listing_id');

				if (!self::$view_listings) {
					//Initialize the View Listings if the table is empty
					if (!$this->queryVar("SELECT COUNT(*) FROM {$this->t['view_listing']}")) {
						$this->resetViewListings();
						self::$view_listings = $this->getViewListings($sort, null, '*', false, 'view_listing_id');
					}
				}

				cache('view_listings', self::$view_listings);
			}
		}

		return self::$view_listings;
	}

	public function getViewListings($sort = array(), $filter = array(), $select = '*', $total = false, $index = null)
	{
		//Select
		$select = $this->extractSelect('view_listing', $select);

		//From
		$from = $this->t['view_listing'];

		//Where
		$where = $this->extractWhere('view_listing', $filter);

		//Order By & Limit
		list($order, $limit) = $this->extractOrderLimit($sort);

		//The Query
		return $this->queryRows("SELECT $select FROM $from WHERE $where $order $limit", $index, $total);
	}

	public function getTotalViewListings($filter = array())
	{
		return $this->getViewListings(array(), $filter, 'COUNT(*)');
	}

	public function getColumns($filter = array())
	{
		return $this->getTableColumns('view_listing', array(), $filter);
	}

	public function getViewListingColumns($view_listing_id, $filter)
	{
		$table = $this->getViewListingTable($view_listing_id);

		return $this->getTableColumns($table, array(), $filter);
	}

	protected function resetViewListings()
	{
		$view_listings = array(
			'clients'       => array(
				'path'  => 'admin/client/listing',
				'query' => '',
				'name'  => 'Clients',
			),
			'pages'         => array(
				'path'  => 'admin/page/listing',
				'query' => '',
				'name'  => 'Page List',
			),
			'view_listings' => array(
				'path'  => 'admin/view/listing',
				'query' => '',
				'name'  => 'View Listings',
			),
		);

		foreach ($view_listings as $view_listing) {
			$this->saveViewListing(null, $view_listing);
		}
	}
}
