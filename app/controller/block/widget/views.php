<?php

/**
 * Class App_Controller_Block_Widget_Views
 * Name: Views Widget
 */
class App_Controller_Block_Widget_Views extends App_Controller_Block_Block
{
	public function build($settings)
	{
		if (empty($settings['group'])) {
			$this->output = _l("Invalid View Options: 'group' must be set");
			return;
		}

		//Defaults
		$settings += array(
			'path'            => '',
			'view_listing_id' => '',
			'default_query'   => array(),
		);

		//Save Original Query
		$orig_get     = $_GET;
		$orig_request = $_REQUEST;

		$_GET += $settings['default_query'];

		$views = $this->Model_View->getViews($settings['group']);

		if (empty($settings['view_listing_id']) && !empty($settings['view_listing'])) {
			$view_listing = $this->Model_View->getViewListingBySlug($settings['view_listing']);

			if ($view_listing) {
				$settings['view_listing_id'] = $view_listing['view_listing_id'];
			}
		}

		$default_view = array(
			'view_id'         => 0,
			'group'           => $settings['group'],
			'view_listing_id' => $settings['view_listing_id'],
			'name'            => 'default',
			'title'           => _l("Default"),
			'path'            => $settings['path'],
			'query'           => $_GET,
			'settings'        => array(
				'size' => 100,
			),
			'view_type'       => '',
			'show'            => !empty($settings['view_listing_id']) ? 1 : 0,
		);

		if (!$views) {
			$views[] = $default_view;
		}

		//AC Template
		$views['__ac_template__'] = array(
				'name'  => 'new-view-__ac_template__',
				'title' => 'New View __ac_template__',
			) + $default_view;


		foreach ($views as $key => &$view) {
			$view += $default_view;

			if (!is_array($view['settings'])) {
				$view['settings'] = array();
			}

			$view['settings'] += $default_view['settings'];

			$listing = $this->Model_View->getViewListing($view['view_listing_id']);

			if (!$listing) {
				$view['show'] = 0;
			}

			if ($view['show']) {
				$action = new Action($listing['path']);

				if (!$action->isValid()) {
					$view['show'] = 0;
					continue;
				}

				if (!$view['query']) {
					$view['query'] = array();
				} elseif (is_string($view['query'])) {
					parse_str($view['query'], $view['query']);
				}

				if (!$listing['query']) {
					$listing['query'] = array();
				} elseif (is_string($listing['query'])) {
					parse_str($listing['query'], $listing['query']);
				}

				$view['query'] = $listing['query'] + $view['query'];

				$_GET     = $view['query'];
				$_REQUEST = $view['query'];

				$view['controller'] = $action->getController();
				$view['method']     = $action->getMethod();
				$view['params']     = $action->getParameters();

				$view['params']['view_id'] = $view['view_id'];

				//Restore Query to original
				$_GET     = $orig_get;
				$_REQUEST = $orig_request;
			}
		}
		unset($view);

		$settings['views'] = $views;

		$settings['data_view_listings'] = $this->Model_View->getAllViewListings();

		$settings['data_view_types'] = array(
			''     => 'view-list',
			'Bar'  => 'chart-bar',
			'Line' => 'chart-line',
			'Pie'  => 'chart-pie',
		);

		$settings['data_view_sizes'] = array(
			25  => '25%',
			33  => '33%',
			50  => '50%',
			100 => '100%',
		);

		//$settings['data_user_groups'] = $this->Model_User_User->getUserGroups();

		if (is_file(DIR_SITE . 'app/controller/' . $this->route->getPath())) {
			$settings['can_modify'] = user_can('modify', $this->route->getPath());
		} else {
			$settings['can_modify'] = true;
		}

		//Action
		$settings['save_view']   = site_url('block/widget/views/save_view');
		$settings['remove_view'] = site_url('block/widget/views/remove_view');

		//Render
		$this->render('block/widget/views', $settings);

		//Restore Query to original
		$_GET     = $orig_get;
		$_REQUEST = $orig_request;
	}

	public function listing($listing = array())
	{
		$view_listing_id = (int)_request('view_listing_id');

		if (!$view_listing_id) {
			$output = _l("View Listing not found with ID: %s", $view_listing_id);
			return IS_AJAX ? output($output) : $output;
		}

		//The Table Columns
		$requested_cols = _request('columns');

		$columns = $this->Model_View->getViewListingColumns($view_listing_id, $requested_cols);

		//The Sort & Filter Data
		$sort   = $this->sort->getQueryDefaults();
		$filter = _request('filter', array());

		$record_total = $this->Model_View->getTotalRecords($view_listing_id, $filter);
		$records      = $this->Model_View->getRecords($view_listing_id, $sort, $filter);

		$listing += array(
			'row_id'         => null,
			'extra_cols'     => $this->Model_View->getViewListingColumns($view_listing_id, false),
			'columns'        => $columns,
			'rows'           => $records,
			'filter_value'   => $filter,
			'pagination'     => true,
			'total_listings' => $record_total,
			'listing_path'   => 'block/widget/views/listing',
			'theme'          => 'admin'
		);

		$output = block('widget/listing', null, $listing);

		//Response
		if (IS_AJAX) {
			output($output);
		}

		return $output;
	}

	public function create()
	{
		$view_listing         = $_POST;
		$view_listing['path'] = 'block/widget/views/listing';

		$view_listing_id = $this->Model_View->saveViewListing(null, $view_listing);

		if ($view_listing_id) {
			$query = 'view_listing_id=' . $view_listing_id . (!empty($view_listing['query']) ? '&' . $view_listing['query'] : '');
			$this->Model_View->saveViewListing($view_listing_id, array('query' => $query));

			message('success', _l("The View has been created"));

			$view = array(
				'group'           => $_POST['group'],
				'view_listing_id' => $view_listing_id,
				'user_group_id'   => 0,
				'name'            => $_POST['name'],
				'title'           => _post('title', $_POST['name']),
				'query'           => '',
				'show'            => 1,
			);

			if (!$this->Model_View->save(null, $view)) {
				message('error', $this->Model_View->getError());
			}
		} else {
			message('error', $this->Model_View->getError());
		}

		if (IS_AJAX) {
			output($this->message->toJSON());
		} else {
			redirect(_request('redirect', _post('path', 'admin')));
		}
	}

	public function save_view()
	{
		$view_id = $this->Model_View->save($_POST['view_id'], $_POST);

		if ($view_id) {
			message('success', _l("%s view was saved!", $_POST['title']));
		} else {
			message('error', $this->Model_View->getError());
		}

		if (IS_AJAX) {
			message('view_id', $view_id);
			output($this->message->toJSON());
		} else {
			redirect($_POST['path']);
		}
	}

	public function remove_view()
	{
		$view = $this->Model_View->getView($_POST['view_id']);

		if ($view) {
			if ($this->Model_View->remove($_POST['view_id'])) {
				message('success', _l("The %s view has been removed", $view['title']));
			} else {
				message('error', $this->Model_View->getError());
			}

			if (IS_AJAX) {
				output($this->message->toJSON());
			} else {
				redirect($view['path']);
			}
		}
	}

	public function save_sort_order()
	{
		foreach (_post('sort_order', array()) as $view_id => $sort_order) {
			$this->Model_View->save($view_id, array('sort_order' => $sort_order));
		}

		if ($this->Model_View->hasError()) {
			message('error', $this->Model_View->getError());
		} else {
			message('success', _l("Sort Order of Views has been updated"));
		}

		//Ajax only method
		output($this->message->toJSON());
	}
}
