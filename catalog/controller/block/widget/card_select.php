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

		$this->data = $settings;

		$this->view->load('block/widget/card_select');

		$this->render();
	}
}
