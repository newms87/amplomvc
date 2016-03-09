<?php
/**
 * @author  Daniel Newman
 * @date    3/20/2013
 * @package Amplo MVC
 * @link    http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt files in the root directory.
 */

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
			'discrete' => false,
			'options'  => array(
				'responsive' => true,
			),
			'data'     => null,
			'settings' => array(),
		);

		$settings += $defaults;

		if (empty($settings['datasets']) || empty($settings['labels'])) {
			$chart = $this->chart->convert($settings['data'], $settings['settings']);

			if ($chart) {
				if (empty($settings['labels'])) {
					$settings['labels'] = $chart['labels'];
				}

				if (empty($settings['datasets'])) {
					$settings['datasets'] = $chart['datasets'];
				}

				if (empty($settings['discrete'])) {
					$settings['discrete'] = $chart['discrete'];
				}
			}
		}

		if (empty($settings['type'])) {
			$settings['type'] = isset($settings['settings']['type']) ? $settings['settings']['type'] : 'Bar';
		}

		$settings['chart_data'] = array(
			'labels'   => $settings['labels'],
			'datasets' => $settings['datasets'],
			'discrete' => $settings['discrete'],
		);

		//Render
		output($this->render('block/widget/chart', $settings, 'admin'));
	}
}
