<?php
/**
 * Name: Frequently Asked Questions
 */
class Admin_Controller_Block_Widget_Faq extends Controller
{

	public function settings(&$settings)
	{
		$data['settings'] = $settings;

		$this->render('block/widget/faq_settings', $data);
	}

	public function save()
	{
		return $this->error;
	}
}
