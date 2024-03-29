<?php

/**
 * @author  Daniel Newman
 * @date    3/20/2013
 * @package Amplo MVC
 * @link    http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYING.txt and LICENSE.txt files in the root directory.
 */
class App_Model_ViewListing extends App_Model_Table
{
	protected $table = 'view_listing', $primary_key = 'view_listing_id';
	static $view_listings;

	/*********************
	 *   View Listings   *
	 *********************/

	public function save($view_listing_id, $view_listing)
	{
		if (isset($view_listing['name'])) {
			if (!validate('text', $view_listing['name'], 2, 45)) {
				$this->error['name'] = _l("The name must be between 2 and 45 characters");
			} elseif (!$view_listing_id) {
				if ($this->queryVar("SELECT COUNT(*) FROM {$this->t['view_listing']} WHERE `name` = '" . $this->escape($view_listing['name']) . "'")) {
					$this->error['name'] = _l("A View Listing with the name %s already exists.", $view_listing['name']);
				}
			}
		} elseif (!$view_listing_id) {
			if (empty($view_listing['name'])) {
				$this->error['name'] = _l("View Listing Name is required.");
			}
		}

		if ($this->error) {
			return false;
		}

		if (!$view_listing_id && empty($view_listing['slug'])) {
			$view_listing['slug'] = 'vl_' . slug($view_listing['name']);
		}

		self::$view_listings = null;
		clear_cache('view_listing');

		//Create the SQL View
		if (!empty($view_listing['sql'])) {
			$slug = !empty($view_listing['slug']) ? $view_listing['slug'] : $this->getField($view_listing_id, 'slug');

			$this->query("DROP VIEW IF EXISTS `" . DB_PREFIX . $slug . "`");

			$display_errors = ini_get('display_errors');
			ini_set('display_errors', 0);

			$result = $this->query("CREATE VIEW `" . DB_PREFIX . $slug . "` AS " . $view_listing['sql']);

			message('notify', "CREATE VIEW `" . DB_PREFIX . $slug . "` AS " . $view_listing['sql']);
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
			$view_listing_id = $this->insert('view_listing', $view_listing);

			if ($view_listing_id) {
				if (empty($view_listing['path']) && !empty($view_listing['sql'])) {
					$view_config = array(
						'path'  => 'block/widget/views/listing',
						'query' => 'view_listing_id=' . $view_listing_id,
					);

					$this->update('view_listing', $view_config, $view_listing_id);
				}
			}
		}

		return $view_listing_id;
	}

	public function sync($view_listing)
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
			$view_listing_id = $this->save(null, $view_listing);
		}

		return $view_listing_id;
	}

	public function remove($view_listing_id)
	{
		self::$view_listings = null;

		$this->delete('view', array('view_listing_id' => $view_listing_id));

		return parent::remove($view_listing_id);
	}


	/*******************************************************
	 * Created View Listing Tables Access / Update methods *
	 *******************************************************/

	public function getViewListingRecords($view_listing_id, $sort = array(), $filter = array(), $options = array(), $total = false)
	{
		$table = $this->getViewListingTable($view_listing_id);

		if (!$table) {
			$this->error['table'] = _l("The view listing with ID (%s) did not exist.", (int)$view_listing_id);

			return array(
				0 => array(),
				1 => 0,
			);
		}

		$orig_table  = $this->table;
		$this->table = $table;

		$records = parent::getRecords($sort, $filter, $options, $total);

		$this->table = $orig_table;

		return $records;
	}

	public function getTotalViewListingRecords($view_listing_id, $filter = array())
	{
		return $this->getViewListingRecords($view_listing_id, array(), $filter, 'COUNT(*)');
	}

	public function getViewListingTable($view_listing_id)
	{
		$slug = $this->queryVar("SELECT slug FROM {$this->t['view_listing']} WHERE view_listing_id = " . (int)$view_listing_id);

		if (!isset($this->t[$slug])) {
			return false;
		}

		return $slug;
	}

	public function getViewListingBySlug($slug)
	{
		if (!self::$view_listings) {
			$this->getAllViewListings();
		}

		return array_search_key('slug', $slug, self::$view_listings);
	}

	public function getAllViewListings()
	{
		if (!self::$view_listings) {
			self::$view_listings = cache('view_listings');

			if (!self::$view_listings) {
				$sort    = array('name' => 'ASC');
				$options = array(
					'index' => 'view_listing_id',
					'cache' => true,
				);

				self::$view_listings = $this->getRecords($sort, null, $options);

				if (!self::$view_listings) {
					//Initialize the View Listings if the table is empty
					if (!$this->queryVar("SELECT COUNT(*) FROM {$this->t['view_listing']}")) {
						$this->resetViewListings();
						self::$view_listings = $this->getRecords($sort, null, $options);
					}
				}

				cache('view_listings', self::$view_listings);
			}
		}

		return self::$view_listings;
	}

	public function getViewListingColumns($view_listing_id, $filter, $merge = array())
	{
		$table = $this->getViewListingTable($view_listing_id);

		return $this->getTableColumns($table, $filter, $merge);
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
			$this->save(null, $view_listing);
		}
	}
}
