<?php
/**
 * Name: You Tube
 */
class Admin_Controller_Block_Widget_Youtube extends Controller
{
	public function settings(&$settings)
	{
		$this->view->load('block/widget/youtube_settings');

		//Your code goes here

		$this->data['settings'] = $settings;

		$this->render();
	}

	public function save()
	{
		return $this->error;
	}
}
