<?php
class ControllerModuleLanguage extends Controller {
	protected function index() {
		$this->template->load('module/language');
		$this->language->load('module/language');
		
		$this->data['action'] = $this->url->link($_GET['route'], $this->url->get_query_exclude('language_code') . '&language_code=');

		$languages = $this->model_localisation_language->getLanguages();
		
		foreach($languages as &$language){
			$language['thumb'] = $this->image->resize(DIR_IMAGE . 'flags/' . $language['image'], 16, 11);
		}
		
		$this->data['languages'] = $languages;
				
		$this->render();
	}
}
