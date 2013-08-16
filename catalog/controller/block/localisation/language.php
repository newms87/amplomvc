<?php
class Catalog_Controller_Block_Localisation_Language extends Controller
{
	public function index()
	{
		$this->template->load('block/localisation/language');
		$this->language->load('block/localisation/language');

		$this->data['action'] = $this->url->link($this->url->getPath(), $this->url->getQueryExclude('language_code') . '&language_code=');

		$languages = $this->language->getLanguages();

		foreach ($languages as &$language) {
			$language['thumb'] = $this->image->resize(DIR_IMAGE . 'flags/' . $language['image'], 16, 11);
		}

		$this->data['languages'] = $languages;

		$this->render();
	}
}
