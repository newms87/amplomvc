<?php
class Admin_Model_Localisation_Language extends Model
{
	public function addLanguage($data)
	{
		$language_id = $this->insert('language', $data);

		$this->cache->delete('language');
	}

	public function editLanguage($language_id, $data)
	{
		$this->update('language', $data, $language_id);

		$this->cache->delete('language');
	}

	public function deleteLanguage($language_id)
	{
		$this->delete('language', $language_id);

		$this->cache->delete('language');
	}

	public function getLanguage($language_id)
	{
		return $this->queryRow("SELECT * FROM " . DB_PREFIX . "language WHERE language_id = '" . (int)$language_id . "'");
	}

	public function getLanguages($data = array(), $select = '*', $total = false)
	{
		if ($total) {
			$select = "COUNT(*) as total";
		} elseif (!$select) {
			$select = '*';
		}

		$from = DB_PREFIX . "language";

		$where = "1";

		if (!empty($data['status'])) {
			if (!is_array($data['status'])) {
				$data['status'] = array($data['status']);
			}

			$where .= " AND status IN ('" . implode("','", $data['status']) . "')";
		} else {
			$where .= " AND status IN ('1', '0')";
		}

		if (!$total) {
			$order = $this->extract_order($data);
			$limit = $this->extract_limit($data);
		} else {
			$order = '';
			$limit = '';
		}

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
}
