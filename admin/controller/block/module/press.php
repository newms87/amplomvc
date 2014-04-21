<?php
/**
 * Name: Press
 */
class Admin_Controller_Block_Module_Press extends Controller
{
	public function settings(&$settings)
	{
		if (!isset($settings['press_items'])) {
			$data['press_items'] = array();
		}

		$data += $settings;

		$this->render('block/module/press_settings', $data);
	}

	public function save()
	{
		return $this->error;
	}
}
