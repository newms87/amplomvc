<?php
class Catalog_Controller_Block_Widget_Faq extends Controller
{
	public function index($settings)
	{
		$this->template->load('block/widget/faq');
		$this->language->load('block/widget/faq');

		//Your code goes here...

		$this->data = $settings;

		$this->render();
	}
}
