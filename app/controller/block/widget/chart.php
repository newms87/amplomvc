<?php

/**
 * Class App_Controller_Block_Widget_Listing
 * Name: The Listings for Admin
 */
class App_Controller_Block_Widget_Chart extends App_Controller_Block_Block
{
	public function build($settings = array())
	{
		$defaults = array(
			'type'     => '',
			'labels'   => '',
			'datasets' => array(),
			'options'  => array(
				'responsive' => true,
			),
		);

		$settings += $defaults;

		if (empty($settings['datasets']) || empty($settings['labels'])) {
			$chart = $this->chart->convert($settings['data'], $settings['settings']);

			if (empty($settings['labels'])) {
				$settings['labels'] = $chart['labels'];
			}

			if (empty($settings['datasets'])) {
				$settings['datasets'] = $chart['datasets'];
			}
		}

		if (empty($settings['type'])) {
			$settings['type'] = isset($settings['settings']['type']) ? $settings['settings']['type'] : 'Bar';
		}

		$settings['chart_data'] = array(
			'labels'   => $settings['labels'],
			'datasets' => $settings['datasets'],
		);

		//Render
		output($this->render('block/widget/chart', $settings, 'admin'));
	}


}
