<?php

/**
 * Name: Carousel
 */
class App_Controller_Admin_Block_Widget_Carousel extends App_Controller_Admin_Block_Block
{
	public function instance($row, $instance, $last = true)
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


		$instance['settings'] = array_replace_recursive($defaults, $instance['settings']);

		//AC Template
		if ($row === '__ac_template__') {
			$instance['settings']['slides']['__ac_template__'] = array(
				'title'      => 'New Slide __ac_template__',
				'image'      => '',
				'href'       => '',
				'target'     => '_blank',
				'sort_order' => 0,
			);
		}


		$data = array(
			'row'      => $row,
			'instance' => $instance,
			'last'     => $last,
		);

		//Template Data
		$data['data_sliders'] = array(
			'nivo'     => "Nivo Slider",
			'slidesjs' => "Slides JS",
		);

		$data['data_true_false'] = array(
			'true'  => _l("Yes"),
			'false' => _l("No"),
		);

		$data['data_yes_no'] = array(
			0 => _l("Yes"),
			1 => _l("No"),
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
		return $this->render('block/widget/carousel/instance', $data);
	}
}
