<?php
class Catalog_Controller_Block_Widget_Faq extends Controller
{
	public function index($settings)
	{
		$this->view->load('block/widget/faq');
		//Your code goes here...

		$this->data = $settings;

		$this->render();
	}
}
