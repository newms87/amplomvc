<?php
class App_Controller_Block_Extras_Sharing extends Controller
{
	public function build($settings)
	{
		$this->render('block/extras/sharing', $settings);
	}
}
