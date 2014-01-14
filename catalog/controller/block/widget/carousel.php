<?php
class Catalog_Controller_Block_Widget_Carousel extends Controller
{
	public function index($settings, $carousel_id = null)
	{
		//Template and Language
		$this->template->load('block/widget/carousel');
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

		$settings['params'] += $default_params;

		$this->data['json_params'] = json_encode($settings['params']);

		$this->data += $settings;

		//Render
		$this->render();
	}
}
