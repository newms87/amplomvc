<?php

class App_Model_Localisation_Language extends App_Model_Table
{
	protected $table = 'language', $primary_key = 'language_id';

	public function addLanguage($data)
	{
		$language_id = $this->insert('language', $data);

		clear_cache('language');
	}

	public function editLanguage($language_id, $data)
	{
		$this->update('language', $data, $language_id);

		clear_cache('language');
	}

	public function deleteLanguage($language_id)
	{
		$this->delete('language', $language_id);

		clear_cache('language');
	}

	/**
	 * Retrieve all the languages that are not disabled (eg: languages with status 0 (enabled) and 1 (active))
	 *
	 * @return Array - a list of enabled and active languages.
	 */
	public function getEnabledLanguages()
	{
		$language_list = cache('language.list');

		if (!$language_list) {
			$languages = $this->queryRows("SELECT language_id, name, code, image, sort_order FROM {$this->t['language']} WHERE status >= 0 ORDER BY sort_order");

			$language_list = array();

			foreach ($languages as $language) {
				$language_list[$language['language_id']] = $language;
			}

			cache('language.list', $language_list);
		}

		return $language_list;
	}
}
