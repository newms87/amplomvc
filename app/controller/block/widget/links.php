<?php

/**
 * Class App_Controller_Block_Widget_Links
 * Name: Link Builder
 */
class App_Controller_Block_Widget_Links extends App_Controller_Block_Block
{
	public function build($settings)
	{
		$this->render('block/widget/links', $settings);
	}
}
