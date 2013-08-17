<?php
class Translation extends Library
{
	public function translate($table, $object_id, &$data)
	{
		if ($this->language->code() == $this->config->get('config_language')) {
			return;
		}

		$table = $this->db->escape($table);

		$translations = $this->cache->get('translate.' . $table . '.' . (int)$object_id);

		if (!$translations) {
			$language_id = $this->config->get('config_language_id');

			$translations = array();

			$translate_list = $this->db->queryRows("SELECT translation_id, `field` FROM " . DB_PREFIX . "translation WHERE `table` = '$table'");

			foreach ($translate_list as $row) {
				$result = $this->db->queryRow("SELECT text FROM " . DB_PREFIX . "translation_text WHERE translation_id = '$row[translation_id]' AND language_id = '$language_id' AND object_id = '" . (int)$object_id . "' AND `text` != ''");

				if ($result) {
					$translations[$row['field']] = html_entity_decode($result['text']);
				}
			}

			$this->cache->set('translate.' . $table . '.' . (int)$object_id, $translations);
		}

		$data = $translations + $data;
	}

	public function translate_all($table, $object_key, &$data)
	{
		foreach ($data as $key => $item) {
			if ($object_key) {
				$this->translate($table, $item[$object_key], $data[$key]);
			} else {
				$this->translate($table, $key, $data[$key]);
			}
		}
	}

	public function getTranslations($table, $object_id, $fields = array())
	{
		if (isset($_POST['translations'])) {
			return $_POST['translations'];
		}

		$languages = $this->cache->get('language.id_list');

		if (!$languages) {
			$language_ids = $this->db->query("SELECT language_id FROM " . DB_PREFIX . "language WHERE status >= '0'");

			$languages = array();

			foreach ($language_ids->rows as $language) {
				if ($language['language_id'] == $this->config->get('config_language_id')) {
					continue;
				}

				$languages[$language['language_id']] = '';
			}

			$this->cache->set('language.id_list', $languages);
		}

		if (empty($languages)) {
			return false;
		}

		$translations = array();

		$results = $this->db->queryRows("SELECT translation_id, `field` FROM " . DB_PREFIX . "translation WHERE `table` = '" . $this->db->escape($table) . "'");

		//Identify all necessary fields
		if (empty($fields)) {
			$fields = array_column($results, 'field');
		}

		//set all fields with all languages
		foreach ($fields as $field) {
			$translations[$field] = $languages;
		}

		foreach ($results as $translation) {
			$query =
				"SELECT language_id, text FROM " . DB_PREFIX . "translation_text" .
				" WHERE translation_id = '$translation[translation_id]'" .
				" AND object_id = '" . $this->db->escape($object_id) . "'";

			$t_rows = $this->db->queryRows($query);

			foreach ($t_rows as $row) {
				$translations[$translation['field']][$row['language_id']] = html_entity_decode($row['text']);
			}
		}

		return $translations;
	}

	public function setTranslations($table, $object_id, $translations)
	{
		if (!empty($translations) && is_array($translations)) {
			foreach ($translations as $field => $translation) {
				foreach ($translation as $language_id => $text) {
					$this->set($table, $field, $object_id, $language_id, $text);
				}
			}

			//Clean up Translation Table
			$translation_ids = $this->db->queryColumn("SELECT translation_id FROM " . DB_PREFIX . "translation t WHERE `table` = '" . $this->db->escape($table) . "' AND `field` NOT IN ('" . implode("','", array_keys($translations)) . "')");

			if ($translation_ids) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "translation_text WHERE translation_id IN (" . implode(',', $translation_ids) . ") AND object_id = " . (int)$object_id);
			}

			$translation_ids = $this->db->queryColumn("SELECT translation_id FROM " . DB_PREFIX . "translation t WHERE 0 IN (SELECT COUNT(*) FROM " . DB_PREFIX . "translation_text tt WHERE tt.translation_id = t.translation_id)");

			if ($translation_ids) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "translation WHERE translation_id IN (" . implode(',', $translation_ids) . ")");
			}
		}
	}

	public function set($table, $field, $object_id, $language_id, $text)
	{
		$translation_id = $this->get_translation_id($table, $field);

		$this->db->query("DELETE FROM " . DB_PREFIX . "translation_text WHERE translation_id = '$translation_id' AND object_id = '" . (int)$object_id . "' AND language_id = '" . (int)$language_id . "'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "translation_text SET translation_id = '$translation_id', object_id = '" . (int)$object_id . "', language_id = '" . (int)$language_id . "', text = '" . $this->db->escape($text) . "'");

		$this->cache->delete('translate');
	}

	public function deleteAll($table, $remove_table = false)
	{
		$translation_ids = $this->db->queryColumn("SELECT translation_id FROM " . DB_PREFIX . "translation WHERE `table` = '" . $this->db->escape($table) . "'");

		if ($translation_ids) {
			foreach ($translation_ids as $translation_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "translation_text WHERE translation_id = $translation_id");

				if ($remove_table) {
					$this->db->query("DELETE FROM " . DB_PREFIX . "translation WHERE translation_id = $translation_id");
				}
			}
		}
	}

	public function delete($table, $object_id, $field = null)
	{
		if ($field) {
			$translation_id = $this->get_translation_id($table, $field, false);

			$translations = array(array('translation_id' => $translation_id));
		} else {
			$translations = $this->db->queryRows("SELECT translation_id FROM " . DB_PREFIX . "translation WHERE `table` = '" . $this->db->escape($table) . "'");
		}

		foreach ($translations as $translation) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "translation_text WHERE translation_id = '$translation[translation_id]' AND object_id = '" . (int)$object_id . "'");
		}

		$this->cache->delete('translate');
	}

	public function get_translation_id($table, $field, $create = true)
	{
		$translation_id = $this->db->queryVar("SELECT translation_id FROM " . DB_PREFIX . "translation WHERE `table` = '" . $this->db->escape($table) . "' AND `field` = '" . $this->db->escape($field) . "' LIMIT 1");

		if (!$translation_id && $create) {
			$translation_id = $this->add($table, $field);
		}

		return $translation_id;
	}

	public function add($table, $field)
	{
		$this->cache->delete('translate');

		$this->db->query("INSERT INTO " . DB_PREFIX . "translation SET `table` = '" . $this->db->escape($table) . "', `field` = '" . $this->db->escape($field) . "'");

		return $this->db->getLastId();
	}

	public function remove($table, $field)
	{
		$translation_id = $this->get_translation_id($table, $field, false);

		$this->db->query("DELETE FROM " . DB_PREFIX . "translation_text WHERE translation_id = '$translation_id'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "translation WHERE translation_id = '$translation_id'");

		$this->cache->delete('translate');
	}
}
