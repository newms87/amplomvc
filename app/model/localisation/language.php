<?php

class App_Model_Localisation_Language extends Model
{
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

	public function getLanguage($language_id)
	{
		return $this->queryRow("SELECT * FROM {$this->t['language']} WHERE language_id = '" . (int)$language_id . "'");
	}

	public function getLanguages($data = array(), $select = '*', $total = false)
	{
		if ($total) {
			$select = "COUNT(*) as total";
		} elseif (!$select) {
			$select = '*';
		}

		$from = $this->t['language'];

		$where = "1";

		if (!empty($data['status'])) {
			if (!is_array($data['status'])) {
				$data['status'] = array($data['status']);
			}

			$where .= " AND status IN ('" . implode("','", $data['status']) . "')";
		} else {
			$where .= " AND status IN ('1', '0')";
		}

		list($order, $limit) = $this->extractOrderLimit($data);

		$query = "SELECT $select FROM $from WHERE $where $order $limit";

		$result = $this->query($query);

		if ($total) {
			return $result->row['total'];
		}

		return $result->rows;
	}

	public function getTotalLanguages($data = array())
	{
		return $this->getLanguages($data, '', true);
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
