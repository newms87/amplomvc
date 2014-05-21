<?php
/**
 * Name: You Tube
 */
class App_Controller_Admin_Block_Widget_Youtube extends Controller
{
	public function settings(&$settings)
	{
		//Your code goes here

		$data['settings'] = $settings;

		$this->render('block/widget/youtube_settings', $data);
	}

	public function save()
	{
		return $this->error;
	}
}
