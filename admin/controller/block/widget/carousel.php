<?php
class Admin_Controller_Block_Widget_Carousel extends Controller
{
	public function profile_settings(&$profile_settings)
	{
		$this->template->load('block/widget/carousel_profile_settings');

		$defaults = array(
			'slides' => array(),
			'params' => array(
				'pauseTime' => 4000,
				'animSpeed' => 500,
			),
		);

		foreach ($profile_settings as &$profile_setting) {
			foreach ($defaults as $key => $default) {
				if (!isset($profile_setting[$key])) {
					$profile_setting[$key] = $default;
				}
			}
		}
		unset ($profile_setting);

		//AC Template for slides
		$profile_settings['__ac_template__']['slides']['__ac_template__'] = array(
			'title' => 'New Slide __ac_template__',
			'image' => '',
			'sort_order' => 0,
		);

		$this->data['profile_settings'] = $profile_settings;

		$this->render();
	}

	public function validate()
	{
		return $this->error;
	}
}
