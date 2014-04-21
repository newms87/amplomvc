<?php
/**
 * Name: You Tube
 */
class Admin_Controller_Block_Widget_Youtube extends Controller
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
