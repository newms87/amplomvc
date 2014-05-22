<?php

/**
 * Class App_Controller_Block_Widget_Youtube
 * Name: You Tube Videos
 */
class App_Controller_Block_Widget_Youtube extends App_Controller_Block_Block
{
	public function build($settings)
	{
		foreach ($settings['videos'] as &$video) {
			if (empty($video['width'])) {
				$video['width'] = 600;
			}

			if (empty($video['height'])) {
				$video['height'] = 480;
			}
		}

		$data = $settings;

		$this->render('block/widget/youtube', $data);
	}

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
