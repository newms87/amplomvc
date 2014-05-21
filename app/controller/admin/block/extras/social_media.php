<?php
/**
 * Name: Social Media
 */
class App_Controller_Admin_Block_Extras_SocialMedia extends Controller
{
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
