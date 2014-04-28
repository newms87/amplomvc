<?php
class Catalog_Controller_Block_Localisation_Language extends Controller
{
	public function build()
	{
		$data['action'] = site_url($this->url->getPath(), $this->url->getQueryExclude('language_code') . '&language_code=');

		$languages = $this->language->getLanguages();

		foreach ($languages as &$language) {
			$language['thumb'] = $this->image->resize(DIR_IMAGE . 'flags/' . $language['image'], 16, 11);
		}

		$data['languages'] = $languages;

		$this->render('block/localisation/language', $data);
	}
}
