<?php
class Admin_Controller_Block_Extras_SocialMedia extends Controller
{
	
	public function settings(&$settings)
	{
		$this->language->load('block/extras/social_media');
			
		$this->template->load('block/extras/social_media_settings');

		$thumb_width = 40;
		$thumb_height = 40;
		
		if (!isset($settings['networks'])) {
			$settings['networks'] = array();
		}
		
		foreach ($settings['networks'] as &$network) {
			$network['thumb'] = $this->image->resize($network['icon'], $thumb_width, $thumb_height);
		}
		
		$this->data += $settings;
		
		$this->data['thumb_width'] = $thumb_width;
		$this->data['thumb_height'] = $thumb_height;
		
		$this->data['no_image'] = $this->image->resize('no_image.png', $thumb_width, $thumb_height);
		
		$this->render();
	}
	
	/*
	public function profile(&$profiles)
	{
		$this->language->load('block/extras/social_media');
		
		$this->template->load('block/extras/social_media_profile');

		$this->data += $profiles;
		
		$this->render();
	}
	*/
	
	public function validate()
	{
		$this->language->load('block/extras/social_media');
		
		if (!empty($_POST['settings']['networks'])) {
			foreach ($_POST['settings']['networks'] as $network) {
				if (!$this->validation->url($network['href'])) {
					$this->error['networks'][] = $this->_('error_network_url', $network['href']);
				}
			}
		}

		return $this->error;
	}
}
