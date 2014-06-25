<?php

/**
 * Class App_Controller_Block_Widget_Views
 * Name: Views Widget
 */
class App_Controller_Block_Widget_Views extends App_Controller_Block_Block
{
	public function build($settings)
	{
		if (empty($settings['path'])) {
			return;
		}

		$action = new Action($settings['path']);

		if (!$action->isValid()) {
			return;
		}

		$settings['controller'] = $action->getController();
		$settings['listing']    = $action->getMethod();

		//Save Original Query
		$orig_get = $_GET;

		$views = $this->Model_Block_Widget_Views->getViews($settings['path']);


		if (!$views) {
			$views[] = array(
				'view_id' => 0,
				'name'    => 'default',
				'title'   => _l("Default"),
				'path'    => $settings['path'],
				'query'   => $_GET,
				'show'    => 1,
			);
		}

		$views['__ac_template__'] = array(
			'view_id' => 0,
			'name'    => 'new-view-__ac_template__',
			'title'   => 'New View __ac_template__',
			'path'    => $settings['path'],
			'query'   => array(),
			'show'    => 0,
		);

		$settings['views'] = $views;

		//Action
		$settings['save_view'] = site_url('block/widget/views/save_view');
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
			$this->message->add('success', _l("%s view was saved!", $_POST['title']));
		} else {
			$this->message->add('error', $this->Model_Block_Widget_Views->getError());
		}

		if ($this->request->isAjax()) {
			$this->message->add('view_id', $view_id);
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
				$this->message->add('success', _l("The %s view has been removed", $view['title']));
			} else {
				$this->message->add('error', $this->Model_Block_Widget_Views->getError());
			}

			if ($this->request->isAjax()) {
				output($this->message->toJSON());
			} else {
				redirect($view['path']);
			}
		}
	}
}
