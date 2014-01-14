<?php
class System_Model_Language extends Model
{
	public function getLanguage($language_id)
	{
		return $this->queryRow("SELECT * FROM " . DB_PREFIX . "language WHERE language_id = " . (int)$language_id);
	}

	public function getLanguages()
	{
		$languages = $this->cache->get('language');

		if (!$languages) {
			$languages = $this->queryRows("SELECT * FROM " . DB_PREFIX . "language WHERE status = '1' ORDER BY sort_order, name");

			$this->cache->set('language', $languages);
		}

		return $languages;
	}

	/**
	 * Retrieve all the languages that are not disbaled (eg: languages with status 0 (enabled) and 1 (active))
	 *
	 * @return Array - a list of enabled and active languages.
	 */
	public function getEnabledLanguages()
	{
		$language_list = $this->cache->get('language.list');

		if (!$language_list) {
			$languages = $this->queryRows("SELECT language_id, name, code, image, sort_order FROM " . DB_PREFIX . "language WHERE status >= 0 ORDER BY sort_order");

			$language_list = array();

			foreach ($languages as $language) {
				$language_list[$language['language_id']] = $language;
			}

			$this->cache->set('language.list', $language_list);
		}

		return $language_list;
	}
}
