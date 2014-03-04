<?php
/**
 * Name: Specials
 */
class Admin_Controller_Block_Widget_Specials extends Controller
{
	public function settings(&$settings)
	{
		$this->data['settings'] = $settings;

		//The Template
		$this->template->load('block/widget/specials_settings');

		//Render
		$this->render();
	}

	public function save()
	{
		return $this->error;
	}
}
