<?php
class App_Controller_Block_Widget_Faq extends Controller
{
	public function build($settings)
	{
		//Your code goes here...

		$data = $settings;

		$this->render('block/widget/faq', $data);
	}

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
