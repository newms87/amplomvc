<?php
/**
 * Name: Carousel
 */
class Admin_Controller_Block_Widget_Carousel extends Controller
{
	public function profile_settings(&$profile_settings)
	{
		//Defaults
		$defaults = array(
			'slider'   => 'slidesjs',
			'slides'   => array(),
			'nivo'     => array(
				'pauseTime' => 4000,
				'animSpeed' => 500,
			),
			'slidesjs' => array(
				'width'      => 1024,
				'height'     => 400,
				'start'      => 1,
				'navigation' => array(
					'active' => 'false',
					'effect' => 'fade',
				),
				'pagination' => array(
					'active' => 'false',
					'effect' => 'fade',
				),
				'play'       => array(
					'active'       => 'false',
					'effect'       => 'fade',
					'interval'     => 5000,
					'auto'         => 'true',
					'swap'         => 'false',
					'pauseOnHover' => 'true',
					'restartDelay' => 2500,
				),
				'effect'     => array(
					'slide' => array(
						'speed' => 200,
					),
					'fade'  => array(
						'speed'     => 300,
						'crossfade' => 'true',
					),
				),
			),
		);

		foreach ($profile_settings as &$profile_setting) {
			$this->tool->fillDefaults($profile_setting, $defaults);
		}
		unset($profile_setting);

		//AC Template for slides
		$profile_settings['__ac_template__']['slides']['__ac_template__'] = array(
			'title'      => 'New Slide __ac_template__',
			'image'      => '',
			'sort_order' => 0,
		);

		$this->data['profile_settings'] = $profile_settings;

		//Template Data
		$this->data['data_sliders'] = array(
			'nivo'     => "Nivo Slider",
			'slidesjs' => "Slides JS",
		);

		$this->data['data_yes_no'] = array(
			'true'  => _l("Yes"),
			'false' => _l("No"),
		);

		$this->data['data_effects'] = array(
			'fade'  => _l("Fade"),
			'slide' => _l("Slide"),
		);


		//The Template
		$this->template->load('block/widget/carousel_profile_settings');

		//Render
		$this->render();
	}

	public function save()
	{
		return $this->error;
	}
}
