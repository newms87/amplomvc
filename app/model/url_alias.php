<?php

class App_Model_UrlAlias extends App_Model_Table
{
	protected $table = 'url_alias', $primary_key = 'url_alias_id';

	public function save($url_alias_id, $url_alias)
	{
		clear_cache('url_alias');

		if (isset($url_alias['alias'])) {
			if (empty($url_alias['alias'])) {
				$this->error['alias'] = _l("Alias cannot be empty.");
			} else {
				$url_alias['alias'] = format('url', $url_alias['alias']);
			}
		} elseif (!$url_alias_id) {
			$this->error['alias'] = _l("Alias is required.");
		}

		if ($this->error) {
			return false;
		}

		if ($url_alias_id) {
			$this->update('url_alias', $url_alias, $url_alias_id);
		} else {
			$url_alias_id = $this->insert('url_alias', $url_alias);
		}

		return $url_alias_id;
	}

	public function remove($url_alias_id)
	{
		clear_cache('url_alias');

		return $this->delete('url_alias', $url_alias_id);
	}

	public function getColumns($filter = array())
	{
		//The Table Columns
		$columns = array(
			'status' => array(
				'type'         => 'select',
				'display_name' => _l("Status"),
				'build_data'   => array(
					0 => _l("Disabled"),
					1 => _l("Enabled"),
				),
				'filter'       => true,
				'sort'     => true,
			),
		);

		return $this->getTableColumns('url_alias', $columns, $filter);
	}

	public function getViewListingId()
	{
		$view_listing_id = $this->Model_ViewListing->getViewListingBySlug('url_alias_list');

		if (!$view_listing_id) {
			$view_listing = array(
				'name' => _l("URL Aliases"),
				'slug' => 'url_alias_list',
				'path' => 'admin/settings/url_alias/listing',
			);

			$view_listing_id = $this->Model_ViewListing->save(null, $view_listing);
		}

		return $view_listing_id;
	}

}
