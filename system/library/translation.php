<?php

class Translation extends Library
{
	public function translate($table, $object_id, &$data)
	{
		if ($this->language->info('code') == option('config_language')) {
			return;
		}

		$table = $this->escape($table);

		$translations = cache('translate.' . $table . '.' . (int)$object_id);

		if (!$translations) {
			$language_id = option('config_language_id');

			$translations = array();

			$translate_list = $this->queryRows("SELECT translation_id, `field` FROM {$this->t['translation']} WHERE `table` = '$table'");

			foreach ($translate_list as $row) {
				$result = $this->queryRow("SELECT text FROM {$this->t['translation_text']} WHERE translation_id = '$row[translation_id]' AND language_id = '$language_id' AND object_id = '" . (int)$object_id . "' AND `text` != ''");

				if ($result) {
					$translations[$row['field']] = html_entity_decode($result['text']);
				}
			}

			cache('translate.' . $table . '.' . (int)$object_id, $translations);
		}

		$data = $translations + $data;
	}

	public function translateAll($table, $object_key, &$data)
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

		$languages = cache('language.id_list');

		if (!$languages) {
			$language_ids = $this->query("SELECT language_id FROM {$this->t['language']} WHERE status >= '0'");

			$languages = array();

			foreach ($language_ids->rows as $language) {
				if ($language['language_id'] == option('config_language_id')) {
					continue;
				}

				$languages[$language['language_id']] = '';
			}

			cache('language.id_list', $languages);
		}

		if (empty($languages)) {
			return false;
		}

		$translations = array();

		$results = $this->queryRows("SELECT translation_id, `field` FROM {$this->t['translation']} WHERE `table` = '" . $this->escape($table) . "'");

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
				"SELECT language_id, text FROM {$this->t['translation_text']} WHERE translation_id = '$translation[translation_id]'" .
				" AND object_id = '" . $this->escape($object_id) . "'";

			$t_rows = $this->queryRows($query);

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
			$translation_ids = $this->queryColumn("SELECT translation_id FROM {$this->t['translation']} t WHERE `table` = '" . $this->escape($table) . "' AND `field` NOT IN ('" . implode("','", array_keys($translations)) . "')");

			if ($translation_ids) {
				$this->query("DELETE FROM {$this->t['translation_text']} WHERE translation_id IN (" . implode(',', $translation_ids) . ") AND object_id = " . (int)$object_id);
			}

			$translation_ids = $this->queryColumn("SELECT translation_id FROM {$this->t['translation']} t WHERE 0 IN (SELECT COUNT(*) FROM {$this->t['translation_text']} tt WHERE tt.translation_id = t.translation_id)");

			if ($translation_ids) {
				$this->query("DELETE FROM {$this->t['translation']} WHERE translation_id IN (" . implode(',', $translation_ids) . ")");
			}
		}
	}

	public function set($table, $field, $object_id, $language_id, $text)
	{
		$translation_id = $this->get_translation_id($table, $field);

		$this->query("DELETE FROM {$this->t['translation_text']} WHERE translation_id = '$translation_id' AND object_id = '" . (int)$object_id . "' AND language_id = '" . (int)$language_id . "'");
		$this->query("INSERT INTO {$this->t['translation_text']} SET translation_id = '$translation_id', object_id = '" . (int)$object_id . "', language_id = '" . (int)$language_id . "', text = '" . $this->escape($text) . "'");

		clear_cache('translate');
	}

	public function deleteAll($table, $remove_table = false)
	{
		$translation_ids = $this->queryColumn("SELECT translation_id FROM {$this->t['translation']} WHERE `table` = '" . $this->escape($table) . "'");

		if ($translation_ids) {
			foreach ($translation_ids as $translation_id) {
				$this->query("DELETE FROM {$this->t['translation_text']} WHERE translation_id = $translation_id");

				if ($remove_table) {
					$this->query("DELETE FROM {$this->t['translation']} WHERE translation_id = $translation_id");
				}
			}
		}
	}

	public function deleteTranslation($table, $object_id, $field = null)
	{
		if ($field) {
			$translation_id = $this->get_translation_id($table, $field, false);

			$translations = array(array('translation_id' => $translation_id));
		} else {
			$translations = $this->queryRows("SELECT translation_id FROM {$this->t['translation']} WHERE `table` = '" . $this->escape($table) . "'");
		}

		foreach ($translations as $translation) {
			$this->query("DELETE FROM {$this->t['translation_text']} WHERE translation_id = '$translation[translation_id]' AND object_id = '" . (int)$object_id . "'");
		}

		clear_cache('translate');
	}

	public function get_translation_id($table, $field, $create = true)
	{
		$translation_id = $this->queryVar("SELECT translation_id FROM {$this->t['translation']} WHERE `table` = '" . $this->escape($table) . "' AND `field` = '" . $this->escape($field) . "' LIMIT 1");

		if (!$translation_id && $create) {
			$translation_id = $this->add($table, $field);
		}

		return $translation_id;
	}

	public function add($table, $field)
	{
		clear_cache('translate');

		$translation = array(
			'table' => $table,
			'field' => $field,
		);

		return $this->insert('translation', $translation);
	}

	public function remove($table, $field)
	{
		$translation_id = $this->get_translation_id($table, $field, false);

		$this->query("DELETE FROM {$this->t['translation_text']} WHERE translation_id = '$translation_id'");
		$this->query("DELETE FROM {$this->t['translation']} WHERE translation_id = '$translation_id'");

		clear_cache('translate');
	}
}
