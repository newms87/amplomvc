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
		$orig_get = $_GET;

		$_GET += $settings['default_query'];

		$views = $this->Model_Block_Widget_Views->getViews($settings['group']);

		if (!isset($settings['view_listing_id']) && !empty($settings['view_listing'])) {
			$view_listing = $this->Model_Block_Widget_Views->getViewListingBySlug($settings['view_listing']);

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
			'show'            => !empty($settings['path']) ? 1 : 0,
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
			$listing = $this->Model_Block_Widget_Views->getViewListing($view['view_listing_id']);

			if (!$listing) {
				$view['show'] = 0;
			}

			if ($view['show']) {
				$action = new Action($listing['path']);

				if (!$action->isValid()) {
					$view['show'] = 0;
					continue;
				}

				if (is_string($listing['query'])) {
					parse_str($listing['query'], $listing['query']);
				}

				$view['controller'] = $action->getController();
				$view['method']     = $action->getMethod();
				$view['query']      = $listing['query'] + $view['query'];
			}
		}
		unset($view);

		$settings['views'] = $views;

		$settings['data_view_listings'] = $this->Model_Block_Widget_Views->getViewListings();

		//$settings['data_user_groups'] = $this->Model_User_User->getUserGroups();

		$settings['can_modify'] = user_can('modify', $this->route->getPath());

		//Action
		$settings['save_view']   = site_url('block/widget/views/save_view');
		$settings['remove_view'] = site_url('block/widget/views/remove_view');

		//Render
		$this->render('block/widget/views', $settings);

		//Restore Query to original
		$_GET = $orig_get;
	}

	public function listing()
	{
		$view_listing_id = _request('view_listing_id');

		echo "hurray " . $view_listing_id;
		exit;
	}

	public function create()
	{
		$view_listing_id = $this->Model_Block_Widget_Views->createView($_POST);

		if ($view_listing_id) {
			message('success', _l("The View has been created"));

			$view = array(
				'group'           => $_POST['group'],
				'view_listing_id' => $view_listing_id,
				'user_group_id'   => 0,
				'name'            => $_POST['name'],
				'title'           => _post('title', $_POST['name']),
				'query'           => '',
				'show'            => 1,
				'sort_order'      => 0,
			);

			$this->Model_Block_Widget_Views->saveView($view);
		} else {
			message('error', $this->Model_Block_Widget_Views->getError());
		}

		if (IS_AJAX) {
			output($this->message->toJSON());
		} else {
			redirect(_post('path', 'common/home'));
		}
	}

	public function save_view()
	{
		$view_id = $this->Model_Block_Widget_Views->save($_POST['view_id'], $_POST);

		if ($view_id) {
			message('success', _l("%s view was saved!", $_POST['title']));
		} else {
			message('error', $this->Model_Block_Widget_Views->getError());
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
		$view = $this->Model_Block_Widget_Views->getView($_POST['view_id']);

		if ($view) {
			if ($this->Model_Block_Widget_Views->remove($_POST['view_id'])) {
				message('success', _l("The %s view has been removed", $view['title']));
			} else {
				message('error', $this->Model_Block_Widget_Views->getError());
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
			$this->Model_Block_Widget_Views->save($view_id, array('sort_order' => $sort_order));
		}

		if ($this->Model_Block_Widget_Views->hasError()) {
			message('error', $this->Model_Block_Widget_Views->getError());
		} else {
			message('success', _l("Sort Order of Views has been updated"));
		}

		//Ajax only method
		output($this->message->toJSON());
	}
}
