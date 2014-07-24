<?php

class App_Model_View extends Model
{
	static $view_listings;

	public function save($view_id, $view)
	{
		if (isset($view['query'])) {
			if (!is_string($view['query'])) {
				$view['query'] = http_build_query($view['query']);
			} else {
				$view['query'] = html_entity_decode(urldecode($view['query']));
			}
		}

		if (!empty($view['settings'])) {
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

			return $this->insert('view', $view);
		}
	}

	public function getView($view_id)
	{
		$view = $this->queryRow("SELECT * FROM " . $this->prefix . "view WHERE view_id = " . (int)$view_id);

		if (!empty($view['settings'])) {
			$view['settings'] = unserialize($view['settings']);
		}

		return $view;
	}

	public function getViewSettings($view_id)
	{
		$settings = $this->queryVar("SELECT settings FROM " . $this->prefix . "view WHERE view_id = " . (int)$view_id);

		if ($settings) {
			return unserialize($settings);
		}

		return array();
	}

	public function getViews($group)
	{
		$views = $this->queryRows("SELECT * FROM " . $this->prefix . "view WHERE `group` = '" . $this->escape($group) . "'");

		foreach ($views as &$view) {
			parse_str($view['query'], $view['query']);

			if (!empty($view['settings'])) {
				$view['settings'] = unserialize($view['settings']);
			}
		}
		unset($view);

		uasort($views, function ($a, $b) {
			return $a['sort_order'] > $b['sort_order'];
		});

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
				if ($this->queryVar("SELECT COUNT(*) FROM " . $this->prefix . "view_listing WHERE `name` = '" . $this->escape($view_listing['name']) . "'")) {
					$this->error['name'] = _l("A View Listing with the name %s already exists.", $view_listing['name']);
					return false;
				}
			}
		}

		if (!$view_listing_id && empty($view_listing['slug'])) {
			$view_listing['slug'] = slug($view_listing['name']);
		}

		$this->cache->delete('view_listing');

		//Create the SQL View
		if (!empty($view_listing['sql'])) {
			$slug = !empty($view_listing['slug']) ? $view_listing['slug'] : $this->queryField('view_listing', 'slug', $view_listing_id);
			$this->query("DROP VIEW IF EXISTS `" . $this->prefix . "$slug`");

			$display_errors = ini_get('display_errors');
			ini_set('display_errors', 0);

			$result = $this->query("CREATE VIEW `" . $this->prefix . "$slug` AS " . $view_listing['sql']);

			ini_set('display_errors', $display_errors);

			if (!$result) {
				$this->error['sql'] = _l("Invalid SELECT statement.<br /><Br /> %s", $this->db->getQueryError());
				return false;
			}
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

	public function removeViewListing($view_listing_id)
	{
		$this->cache->delete('view_listing');

		$this->delete('view', array('view_listing_id' => $view_listing_id));

		return $this->delete('view_listing', $view_listing_id);
	}

	public function getRecords($view_listing_id, $sort = array(), $filter = array(), $select = '*', $index = null)
	{
		$table = $this->getViewListingTable($view_listing_id);

		if (!$table) {
			$this->error['table'] = _l("The view listing with ID (%s) did not exist.", (int)$view_listing_id);
			return false;
		}

		//Select
		if ($index === false) {
			$select = 'COUNT(*)';
		}

		//From
		$from = $this->prefix . $table;

		//Where
		$where = $this->extractFilter($table, $filter);

		//Order By & Limit
		if ($index !== false) {
			$order = $this->extractOrder($sort);
			$limit = $this->extractLimit($sort);
		} else {
			$order = '';
			$limit = '';
		}

		//The Query
		$query = "SELECT $select FROM $from WHERE $where $order $limit";

		//Get Total
		if ($index === false) {
			return $this->queryVar($query);
		}

		//Get Rows
		return $this->queryRows($query, $index);
	}

	public function getTotalRecords($view_listing_id, $filter = array())
	{
		return $this->getRecords($view_listing_id, array(), $filter, '', false);
	}

	public function getViewListingTable($view_listing_id)
	{
		return $this->queryVar("SELECT slug FROM " . $this->prefix . "view_listing WHERE view_listing_id = " . (int)$view_listing_id);
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
				self::$view_listings = $this->queryRows("SELECT * FROM " . $this->prefix . "view_listing ORDER BY name", 'view_listing_id');

				if (!self::$view_listings) {
					//Initialize the View Listings if the table is empty
					if (!$this->queryVar("SELECT COUNT(*) FROM " . $this->prefix . "view_listing")) {
						$this->resetViewListings();
						return $this->getListings();
					}
				}

				cache('view_listings', self::$view_listings);
			}
		}

		return self::$view_listings;
	}

	public function getViewListings($sort = array(), $filter = array(), $select = '*', $index = null)
	{
		//Select
		if ($index === false) {
			$select = 'COUNT(*)';
		}

		//From
		$from = $this->prefix . "view_listing vl";

		$where = $this->extractFilter('view_listing vl', $filter);

		//Order By & Limit
		if ($index !== false) {
			$order = $this->extractOrder($sort);
			$limit = $this->extractLimit($sort);
		} else {
			$order = '';
			$limit = '';
		}

		//The Query
		$query = "SELECT $select FROM $from WHERE $where $order $limit";

		//Get Total
		if ($index === false) {
			return $this->queryVar($query);
		}

		return $this->queryRows($query, $index);
	}

	public function getTotalViewListings($filter = array())
	{
		return $this->getViewListings(array(), $filter, '', false);
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
		$this->cache->delete('view_listing');

		$view_listings = array(
			'clients' => array(
				'path'  => 'admin/client/listing',
				'query' => '',
				'name'  => 'Clients',
			),
			'pages'   => array(
				'path'  => 'admin/page/listing',
				'query' => '',
				'name'  => 'Page List',
			),
		   'view_listings' => array(
			   'path' => 'admin/view/listing',
		      'query' => '',
		      'name' => 'View Listings',
		   ),
		);

		foreach ($view_listings as $view_listing) {
			$this->saveViewListing(null, $view_listing);
		}
	}
}