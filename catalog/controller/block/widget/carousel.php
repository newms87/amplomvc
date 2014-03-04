<?php
class Catalog_Controller_Block_Widget_Carousel extends Controller
{
	public function index($settings, $carousel_id = null)
	{
		if (!is_null($carousel_id)) {
			$settings = $this->Model_Block_Block->getBlockProfileSettings('widget/carousel', $carousel_id) + $settings;
		}

		//The Data
		$this->data['slider_id'] = 'carousel_' . uniqid();

		//Slides
		foreach ($settings['slides'] as &$slide) {
			if (!empty($slide['image_width'])) {
				$slide['thumb'] = $this->image->resize($slide['image'], $slide['image_width'], $slide['image_height']);
			} else {
				$slide['thumb'] = $this->image->get($slide['image']);
			}
		}
		unset($slide);

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

				$this->tool->fillDefaults($settings['nivo'], $default_params);
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

				$this->tool->fillDefaults($settings['slidesjs'], $default_params);
				break;
		}

		$this->data += $settings;

		//The Template
		$this->template->load('block/widget/carousel');

		//Render
		$this->render();
	}
}
