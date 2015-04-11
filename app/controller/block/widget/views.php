<?php

/**
 * Class App_Controller_Block_Widget_Views
 * Name: Views Widget
 */
class App_Controller_Block_Widget_Views extends App_Controller_Block_Block
{
	public function __construct()
	{
		parent::__construct();

		if (!$this->user->isLogged()) {
			redirect('admin/user/login');
		}
	}

	public function build($settings)
	{
		if (empty($settings['group'])) {
			$this->output = _l("Invalid View Options: 'group' must be set");
			return;
		}

		//Defaults
		$settings += array(
			'path'            => '',
			'query'           => '',
			'view_listing_id' => '',
			'default_query'   => array(),
		);

		//Save Original Query
		$orig_get     = $_GET;
		$orig_request = $_REQUEST;

		$_GET += $settings['default_query'];

		$views = $this->Model_View->getViews($settings['group']);

		if (empty($settings['view_listing_id'])) {
			if (!empty($settings['view_listing'])) {
				$view_listing = $this->Model_ViewListing->getViewListingBySlug($settings['view_listing']);

				if ($view_listing) {
					$settings['view_listing_id'] = $view_listing['view_listing_id'];
				}
			}

			if (!$settings['view_listing_id']) {
				$view_listing = array(
					'name'  => $settings['group'],
					'path'  => $settings['path'],
					'query' => $settings['query'],
				);

				$view_listing_id = $this->Model_ViewListing->sync($view_listing);

				if ($view_listing_id) {
					$settings['view_listing_id'] = $view_listing_id;
					return $this->build($settings);
				}
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

			if (!empty($view['view_listing_id'])) {
				$listing = $this->Model_ViewListing->getRecord($view['view_listing_id']);
			} else {
				$listing = $view;
			}

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

				if (is_string($settings['query'])) {
					parse_str($settings['query'], $settings['query']);
				}

				$view['query'] = $settings['query'] + $listing['query'] + $view['query'];

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

		$settings['data_view_listings'] = $this->Model_ViewListing->getAllViewListings();

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

		//$settings['data_user_groups'] = $this->Model_User->getUserGroups();

		if (is_file(DIR_SITE . 'app/controller/' . $this->route->getPath())) {
			$settings['can_modify'] = user_can('w', $this->route->getPath());
		} else {
			$settings['can_modify'] = true;
		}

		//Render
		$this->render('block/widget/views', $settings);

		//Restore Query to original
		$_GET     = $orig_get;
		$_REQUEST = $orig_request;
	}

	public function listing($listing = array())
	{
		$view_listing_id = !empty($listing['view_listing_id']) ? (int)$listing['view_listing_id'] : (int)_request('view_listing_id');

		if (!$view_listing_id) {
			$output = _l("View Listing not found with ID: %s", $view_listing_id);
			return $this->is_ajax ? output($output) : $output;
		}

		//The Table Columns
		$requested_cols = !empty($listing['columns']) ? $listing['columns'] : _request('columns');

		$columns = $this->Model_ViewListing->getViewListingColumns($view_listing_id, $requested_cols);

		//The Sort & Filter Data
		$sort    = !empty($listing['sort']) ? $listing['sort'] : (array)_request('sort');
		$filter  = !empty($listing['filter']) ? $listing['filter'] : (array)_request('filter');
		$options = !empty($listing['options']) ? $listing['options'] : array(
			'page'  => _get('page'),
			'limit' => _get('limit', IS_ADMIN ? option('admin_list_limit', 20) : option('list_limit', 20)),
		);

		list($records, $record_total) = $this->Model_ViewListing->getViewListingRecords($view_listing_id, $sort, $filter, null, true);

		if (!empty($listing['return_data'])) {
			$this->output = array(
				'records' => $records,
				'total'   => $record_total,
			);
		}

		$listing += array(
			'extra_cols'     => $this->Model_ViewListing->getViewListingColumns($view_listing_id, false),
			'columns'        => $columns,
			'records'        => $records,
			'sort'           => $sort,
			'filter_value'   => $filter,
			'pagination'     => true,
			'total_listings' => $record_total,
			'listing_path'   => 'block/widget/views/listing',
			'theme'          => 'admin'
		);

		$output = block('widget/listing', null, $listing + $options);

		//Response
		if ($this->is_ajax) {
			output($output);
		}

		return $output;
	}

	public function create()
	{
		$view_listing         = $_POST;
		$view_listing['path'] = 'block/widget/views/listing';

		$view_listing_id = $this->Model_ViewListing->save(null, $view_listing);

		if ($view_listing_id) {
			$query = 'view_listing_id=' . $view_listing_id . (!empty($view_listing['query']) ? '&' . $view_listing['query'] : '');
			$this->Model_ViewListing->save($view_listing_id, array('query' => $query));

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
				message('error', $this->Model_View->fetchError());
			}
		} else {
			message('error', $this->Model_ViewListing->fetchError());
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect(_request('redirect', _post('path', 'admin')));
		}
	}

	public function save_view()
	{
		$view_id = $this->Model_View->save(_post('view_id'), $_POST);

		if ($view_id) {
			message('success', _l("%s view was saved!", _post('title')));
		} else {
			message('error', $this->Model_View->fetchError());
		}

		if ($this->is_ajax) {
			message('data', array('view_id' => $view_id));
			output_message();
		} else {
			redirect(_post('path', ' admin'));
		}
	}

	public function remove_view()
	{
		$title = $this->Model_View->getField(_post('view_id'), 'title');

		if ($this->Model_View->remove(_post('view_id'))) {
			message('success', _l("The %s view has been removed", $title));
		} else {
			message('error', $this->Model_View->fetchError());
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('admin');
		}
	}

	public function save_sort_order()
	{
		foreach (_post('sort_order', array()) as $view_id => $sort_order) {
			$this->Model_View->save($view_id, array('sort_order' => $sort_order));
		}

		if ($this->Model_View->hasError()) {
			message('error', $this->Model_View->fetchError());
		} else {
			message('success', _l("Sort Order of Views has been updated"));
		}

		//Ajax only method
		output_message();
	}
}
