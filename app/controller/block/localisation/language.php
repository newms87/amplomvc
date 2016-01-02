<?php

class App_Controller_Block_Localisation_Language extends App_Controller_Block_Block
{
	public function build()
	{
		$data['action'] = site_url($this->router->getPath(), _get_exclude('language_code') . '&language_code=');

		$languages = $this->Model_Localisation_Language->getRecords(null, null, array('cache' => true));

		foreach ($languages as &$language) {
			$language['thumb'] = image(DIR_IMAGE . 'flags/' . $language['image'], 16, 11);
		}

		$data['languages'] = $languages;

		$this->render('block/localisation/language', $data);
	}
}
