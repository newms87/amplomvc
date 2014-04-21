<?php
class Catalog_Controller_Block_Module_Press extends Controller
{
	public function index($settings)
	{
		$settings['image_width']  = 185;
		$settings['image_height'] = 240;

		$settings['auto_size'] = true;

		foreach ($settings['press_items'] as &$press) {
			if (!empty($press['images'])) {
				$thumb          = current($press['images']);
				$press['thumb'] = $this->image->resize($thumb, $settings['image_width'], $settings['image_height']);

				foreach ($press['images'] as &$image) {
					if ($settings['auto_size']) {
						$image = $this->image->get($image);
					} else {
						$width  = !empty($press['image_width']) ? $press['image_width'] : $settings['image_width'];
						$height = !empty($press['image_height']) ? $press['image_height'] : $settings['image_height'];

						$image = $this->image->resize($image, $width, $height);
					}
				}
				unset($image);
			}

			$press['description'] = html_entity_decode($press['description']);
		}
		unset($press);

		$data['press_list'] = $settings['press_items'];

		$this->render('block/module/press', $data);
	}
}
