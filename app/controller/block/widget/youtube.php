<?php
class App_Controller_Block_Widget_Youtube extends Controller
{
	public function build($settings)
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

		$this->render('block/widget/youtube', $data);
	}
}
