<?php
class Catalog_Controller_Block_Widget_Youtube extends Controller
{
	public function index($settings)
	{
		$this->view->load('block/widget/youtube');
		foreach ($settings['videos'] as &$video) {
			if (empty($video['width'])) {
				$video['width'] = 600;
			}

			if (empty($video['height'])) {
				$video['height'] = 480;
			}
		}

		$this->data = $settings;

		$this->render();
	}
}
