<?php

/**
 * Class App_Controller_Block_Extras_Sharing
 * Name: Social Sharing for Amplo MVC
 */
class App_Controller_Block_Extras_Sharing extends App_Controller_Block_Block
{
	public function build($settings)
	{
		$this->render('block/extras/sharing', $settings);
	}
}
