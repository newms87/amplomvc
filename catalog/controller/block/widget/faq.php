<?php
class Catalog_Controller_Block_Widget_Faq extends Controller
{
	public function build($settings)
	{
		//Your code goes here...

		$data = $settings;

		$this->render('block/widget/faq', $data);
	}
}
