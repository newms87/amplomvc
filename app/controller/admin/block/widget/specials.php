<?php
/**
 * Name: Specials
 */
class App_Controller_Admin_Block_Widget_Specials extends Controller
{
	public function settings(&$settings)
	{
		$data['settings'] = $settings;

		//Render
		$this->render('block/widget/specials_settings', $data);
	}

	public function save()
	{
		return $this->error;
	}
}
