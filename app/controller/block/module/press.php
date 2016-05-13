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
 * Class App_Controller_Block_Module_Press
 * Name: Amplo Press / Editorial
 */
class App_Controller_Block_Module_Press extends App_Controller_Block_Block
{
	public function build($settings)
	{
		$settings['image_width']  = 185;
		$settings['image_height'] = 240;

		$settings['auto_size'] = true;

		foreach ($settings['press_items'] as &$press) {
			if (!empty($press['images'])) {
				$thumb          = current($press['images']);
				$press['thumb'] = image($thumb, $settings['image_width'], $settings['image_height']);

				foreach ($press['images'] as &$image) {
					if ($settings['auto_size']) {
						$image = $this->image->get($image);
					} else {
						$width  = !empty($press['image_width']) ? $press['image_width'] : $settings['image_width'];
						$height = !empty($press['image_height']) ? $press['image_height'] : $settings['image_height'];

						$image = image($image, $width, $height);
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

	public function settings(&$settings)
	{
		if (!isset($settings['press_items'])) {
			$data['press_items'] = array();
		}

		$data += $settings;

		$this->render('block/module/press_settings', $data);
	}
}
