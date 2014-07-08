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
			'path'          => '',
			'default_query' => array(),
		);

		//Save Original Query
		$orig_get = $_GET;

		$_GET += $settings['default_query'];

		$views = $this->Model_Block_Widget_Views->getViews($settings['group']);

		$default_view = array(
			'view_id' => 0,
			'group'   => $settings['group'],
			'name'    => 'default',
			'title'   => _l("Default"),
			'path'    => $settings['path'],
			'query'   => $_GET,
			'show'    => !empty($settings['path']) ? 1 : 0,
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
			if (empty($view['path'])) {
				$view['show'] = 0;
				continue;
			}

			if ($view['show']) {
				$action = new Action($view['path']);

				if (!$action->isValid()) {
					unset($views[$key]);
					continue;
				}

				$view['controller'] = $action->getController();
				$view['method']     = $action->getMethod();
			}
		}
		unset($view);

		$settings['views'] = $views;

		$settings['data_listing_paths'] = $this->Model_Block_Widget_Views->getListingPaths();

		//Action
		$settings['save_view']   = site_url('block/widget/views/save_view');
		$settings['remove_view'] = site_url('block/widget/views/remove_view');

		//Render
		$this->render('block/widget/views', $settings);

		//Restore Query to original
		$_GET = $orig_get;
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
}
