<?php

/**
 * Class App_Controller_Block_Extras_SocialMedia
 * Name: Social Media for Amplo MVC
 */
class App_Controller_Block_Extras_SocialMedia extends App_Controller_Block_Block
{
	public function build($settings, $type = 'standard')
	{
		if (isset($settings['networks'])) {
			//TODO: MOVE the size settings to the block profile
			if (!isset($settings['width'])) {
				$settings['width'] = $settings['height'] = 25;
			}

			foreach ($settings['networks'] as &$network) {
				$network['thumb'] = $this->image->resize($network['icon'], $settings['width'], $settings['height']);
			}
			unset($network);

		} else {
			return; // we return without rendering because there is nothing to output.
		}

		switch ($type) {
			case 'standard':
				$template = 'block/extras/my_networks';
				break;
			default:
				$template = 'block/extras/my_networks';
				break;
		}

		$this->render($template, $settings);
	}

	public function settings(&$settings)
	{
		$thumb_width  = 40;
		$thumb_height = 40;

		if (!isset($settings['networks'])) {
			$settings['networks'] = array();
		}

		foreach ($settings['networks'] as &$network) {
			$network['thumb'] = $this->image->resize($network['icon'], $thumb_width, $thumb_height);
		}

		$settings['thumb_width']  = $thumb_width;
		$settings['thumb_height'] = $thumb_height;

		$settings['no_image'] = $this->image->resize('no_image.png', $thumb_width, $thumb_height);

		$this->render('block/extras/social_media_settings', $settings);
	}

	public function save()
	{
		if (!empty($_POST['settings']['networks'])) {
			foreach ($_POST['settings']['networks'] as $network) {
				if (!$this->validation->url($network['href'])) {
					$this->error['networks'][] = _l("%s is not a valid URL. You must include the http:// or https:// protocol.", $network['href']);
				}
			}
		}

		return $this->error;
	}
}
