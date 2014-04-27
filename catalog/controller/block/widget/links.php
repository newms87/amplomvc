<?php
class Catalog_Controller_Block_Widget_Links extends Controller
{
	public function build($settings)
	{
		$data = $settings;

		$this->render('block/widget/links');
	}
}
