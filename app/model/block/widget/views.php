<?php

class App_Model_Block_Widget_Views extends Model
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
		return $this->queryRow("SELECT * FROM " . $this->prefix . "view WHERE view_id = " . (int)$view_id);
	}

	public function getViews($group)
	{
		$views = $this->queryRows("SELECT * FROM " . $this->prefix . "view WHERE `group` = '" . $this->escape($group) . "'");

		foreach ($views as &$view) {
			parse_str($view['query'], $view['query']);
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

	public function createView($view_listing)
	{
		$view_listing += array(
			'name'  => '',
			'slug'  => '',
			'path'  => 'block/widget/views/listing',
			'query' => '',
			'sql'   => '',
		);

		$view_listing_id = $this->saveViewListing(null, $view_listing);

		if (!empty($view_listing['sql'])) {
			$result = $this->query("CREATE VIEW " . $view_listing['sql']);

			if (!$result) {
				return false;
			}
		}

		$query = 'view_listing_id=' . $view_listing_id . ($view_listing['query'] ? '&' . $view_listing['query'] : '');

		$this->saveViewListing($view_listing_id, array('query' => $query));

		return $view_listing_id;
	}

	public function saveViewListing($view_listing_id, $view_listing)
	{
		if (!$view_listing_id || isset($view_listing['name'])) {
			if (!validate('text', $view_listing['name'], 2, 45)) {
				$this->error['name'] = _l("The name must be between 2 and 45 charaters");
				return false;
			}

			if (!$view_listing_id) {
				if ($this->queryVar("SELECT COUNT(*) FROM " . $this->prefix . "view_listing WHERE `name` = '" . $this->escape($view_listing['name']) . "'")) {
					$this->error['name'] = _l("A View Listing with the name %s already exists.", $view_listing['name']);
					return false;
				}
			}
		}

		$this->cache->delete('view_listing');

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
		return $this->delete('view_listing', $view_listing_id);
	}

	public function getViewListingBySlug($slug)
	{
		if (!self::$view_listings) {
			$this->getViewListings();
		}

		return array_search_key('slug', $slug, self::$view_listings);
	}

	public function getViewListing($view_listing_id)
	{
		if (!self::$view_listings) {
			$this->getViewListings();
		}

		return isset(self::$view_listings[$view_listing_id]) ? self::$view_listings[$view_listing_id] : null;
	}

	public function getViewListings()
	{
		if (!self::$view_listings) {
			self::$view_listings = cache('view_listings');

			if (!self::$view_listings) {
				self::$view_listings = $this->queryRows("SELECT * FROM " . $this->prefix . "view_listing", 'view_listing_id');

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

	protected function resetViewListings()
	{
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
		);

		foreach ($view_listings as $view_listing) {
			$this->saveViewListing(null, $view_listing);
		}
	}
}