<?php
class Catalog_Controller_Block_Widget_CardSelect extends Controller
{
	public function index($settings)
	{
		foreach ($settings['videos'] as &$video) {
			if (empty($video['width'])) {
				$video['width'] = 600;
			}

			if (empty($video['height'])) {
				$video['height'] = 480;
			}
		}

		$data = $settings;

		$this->render('block/widget/card_select', $data);
	}
}
