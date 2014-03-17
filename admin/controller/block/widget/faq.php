<?php
/**
 * Name: Frequently Asked Questions
 */
class Admin_Controller_Block_Widget_Faq extends Controller
{

	public function settings(&$settings)
	{
		$this->view->load('block/widget/faq_settings');

		$this->data['settings'] = $settings;

		$this->render();
	}

	public function save()
	{
		return $this->error;
	}
}
