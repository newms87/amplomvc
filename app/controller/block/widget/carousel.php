<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC
 * @link http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYING.txt and LICENSE.txt files in the root directory.
 */

/**
 * Class App_Controller_Block_Widget_Carousel
 * Name: Amplo Carousel
 */
class App_Controller_Block_Widget_Carousel extends App_Controller_Block_Block
{
	public function build($instance)
	{
		$settings = !empty($instance['settings']) ? $instance['settings'] : $instance;

		if (empty($settings['slider']) || empty($settings['slides'])) {
			return '';
		}

		//Slides
		if (!empty($settings['slides'])) {
			foreach ($settings['slides'] as &$slide) {
				if (empty($slide['image_width'])) {
					$slide['image_width'] = null;
				}

				if (empty($slide['image_height'])) {
					$slide['image_height'] = null;
				}
			}
			unset($slide);
		} else {
			$settings['slides'] = array();
		}

		//Params
		switch ($settings['slider']) {
			case 'nivo':
				$default_params = array(
					'effect'           => 'random',
					'slices'           => 22,
					'boxCols'          => 12,
					'boxRows'          => 6,
					'animSpeed'        => 500,
					'pauseTime'        => 4000,
					'startSlide'       => 0,
					'directionNav'     => false,
					'controlNav'       => false,
					'controlNavThumbs' => false,
					'pauseOnHover'     => false,
					'manualAdvance'    => false,
					'prevText'         => 'Prev',
					'nextText'         => 'Next',
					'randomStart'      => false,
				);

				$settings['nivo'] = array_replace_recursive($settings['nivo'], $default_params);
				break;

			case 'slidejs':
			default:
				$default_params = array(
					'width'      => 1024,
					'height'     => 400,
					'start'      => 1,
					'navigation' => array(
						'active' => false,
						'effect' => 'fade',
					),
					'pagination' => array(
						'active' => false,
						'effect' => 'fade',
					),
					'play'       => array(
						'active'       => false,
						'effect'       => 'fade',
						'interval'     => 5000,
						'auto'         => true,
						'swap'         => false,
						'pauseOnHover' => true,
						'restartDelay' => 2500,
					),
					'effect'     => array(
						'slide' => array(
							'speed' => 200,
						),
						'fade'  => array(
							'speed'     => 300,
							'crossfade' => true,
						),
					),
				);

				array_walk_recursive($settings['slidesjs'], function (&$value) {
					if ($value === 'false' || $value === 'true') {
						$value = $value === 'true';
					}
				});

				$settings['slidejs'] = array_replace_recursive($settings['slidesjs'], $default_params);
				break;
		}

		$instance += $settings;

		//Render
		$this->render('block/widget/carousel', $instance);
	}

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
