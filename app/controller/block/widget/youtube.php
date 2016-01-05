<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC
 * @link http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt files in the root directory.
 */

/**
 * Class App_Controller_Block_Widget_Youtube
 * Name: You Tube Videos
 */
class App_Controller_Block_Widget_Youtube extends App_Controller_Block_Block
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

	public function settings(&$settings)
	{
		//Your code goes here

		$data['settings'] = $settings;

		$this->render('block/widget/youtube_settings', $data);
	}

}
