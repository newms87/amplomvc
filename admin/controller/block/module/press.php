<?php
/**
 * Name: Press
 */
class Admin_Controller_Block_Module_Press extends Controller
{
	public function settings(&$settings)
	{
		$this->view->load('block/module/press_settings');

		if (!isset($settings['press_items'])) {
			$this->data['press_items'] = array();
		}

		$this->data += $settings;

		$this->render();
	}

	public function save()
	{
		return $this->error;
	}
}
