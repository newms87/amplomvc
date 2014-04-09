<?php

/**
 * Name: Carousel
 */
class Admin_Controller_Block_Widget_Carousel extends Admin_Controller_Block_Block
{
	public function instances(&$instances)
	{
		//Defaults
		$defaults = array(
			'slider'   => 'slidesjs',
			'slides'   => array(),

			//Nivo Settings
			'nivo'     => array(
				'pauseTime' => 4000,
				'animSpeed' => 500,
			),

			//Slides JS Settings
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

		foreach ($instances as &$instance) {
			$instance = array_replace_recursive($instance, $defaults);
		}
		unset($instance);

		//AC Template for slides
		$instances['__ac_template__']['settings'] = $defaults;

		$instances['__ac_template__']['settings']['slides']['__ac_template__'] = array(
			'title'      => 'New Slide __ac_template__',
			'image'      => '',
			'href'       => '',
			'target'     => '_blank',
			'sort_order' => 0,
		);

		$data = array(
			'instances' => $instances,
		);

		//Template Data
		$data['data_sliders'] = array(
			'nivo'     => "Nivo Slider",
			'slidesjs' => "Slides JS",
		);

		$data['data_yes_no'] = array(
			'true'  => _l("Yes"),
			'false' => _l("No"),
		);

		$data['data_effects'] = array(
			'fade'  => _l("Fade"),
			'slide' => _l("Slide"),
		);

		$data['data_targets'] = array(
			'_blank'  => _l("New Window"),
			'_self'   => _l("Self"),
			'_parent' => _l("Parent"),
			'_top'    => _l("Top"),
		);

		//Render
		return $this->render('block/widget/carousel_instances', $data);
	}

	public function save()
	{
		return $this->error;
	}
}
