<?php
class Translation {
	private $regsitry;
	
	function __construct($registry){
		$this->registry = $registry;
	}
	
	public function __get($key){
		return $this->registry->get($key);
	}
	
	public function translate($table, $object_id, &$data){
		if($this->language->code() == $this->config->get('config_language')) return;
		
		$table = $this->db->escape($table);
		
		$translations = $this->cache->get('translate.' . $table . '.' . (int)$object_id);
		
		if(!$translations){
			$language_id = $this->config->get('config_language_id');
			
			$translations = array();
			
			$translate_list = $this->db->query_rows("SELECT translation_id, `field` FROM " . DB_PREFIX . "translation WHERE `table` = '$table'");
			
			foreach($translate_list as $row){
				$result = $this->db->query_row("SELECT text FROM " . DB_PREFIX . "translation_text WHERE translation_id = '$row[translation_id]' AND language_id = '$language_id' AND object_id = '" . (int)$object_id . "' AND `text` != ''");
				
				if($result){
					$translations[$row['field']] = html_entity_decode($result['text']);
				}
			}
			
			$this->cache->set('translate.' . $table . '.' . (int)$object_id, $translations);
		}

		$data = $translations + $data;
	}
	
	public function translate_all($table, $object_key, &$data){
		foreach($data as $key => $item){
			$this->translate($table, $item[$object_key], $data[$key]);
		}
	}
	
	public function get_translations($table, $object_id, $fields = array()){
		if(isset($_POST['translations'])) return $_POST['translations'];
		
		$languages = $this->cache->get('language.id_list');
		
		if(!$languages){
			$language_ids = $this->db->query("SELECT language_id FROM " . DB_PREFIX . "language WHERE status = '1'");
			
			$languages = array();
			
			foreach($language_ids->rows as $language){
				if($language['language_id'] == $this->config->get('config_language_id')) continue;
				
				$languages[$language['language_id']] = '';
			}
			
			$this->cache->set('language.id_list', $languages);
		}
		
		if(empty($languages)) return false;
		
		$translations = array();
		
		$result = $this->db->query("SELECT translation_id, `field` FROM " . DB_PREFIX . "translation WHERE `table` = '" . $this->db->escape($table) . "'");
		
		//Identify all necessary fields
		if(empty($fields)){
			$fields = array_column($result->rows, 'field');
		}
		
		//set all fields with all languages
		foreach($fields as $field){
			$translations[$field] = $languages;
		}
		
		foreach($result->rows as $translation){
			$query =
				"SELECT language_id, text FROM " . DB_PREFIX . "translation_text" .
				" WHERE translation_id = '$translation[translation_id]'" .
				" AND object_id = '" . $this->db->escape($object_id) . "'";
			
			$t_result = $this->db->query($query);
			
			foreach($t_result->rows as $row) {
				$translations[$translation['field']][$row['language_id']] = html_entity_decode($row['text']);
			}
		}
		
		return $translations;
	}
	
	public function set_translations($table, $object_id, $translations){
		if(!empty($translations)){
			foreach($translations as $field => $translation){
				foreach($translation as $language_id => $text){
					$this->set($table, $field, $object_id, $language_id, $text);
				}
			}
		}
	}
	
	public function set($table, $field, $object_id, $language_id, $text){
		$translation_id = $this->get_translation_id($table, $field);
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "translation_text WHERE translation_id = '$translation_id' AND object_id = '" . (int)$object_id . "' AND language_id = '" . (int)$language_id . "'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "translation_text SET translation_id = '$translation_id', object_id = '" . (int)$object_id . "', language_id = '" . (int)$language_id . "', text = '" . $this->db->escape($text) . "'");
		
		$this->cache->delete('translate');
	}
	
	public function delete($table, $object_id, $field = null){
		if($field){
			$translation_id = $this->get_translation_id($table, $field, false);
			
			$translations = array('translation_id' => $translation_id);
		}
		else{
			$result = $this->db->query("SELECT translation_id FROM " . DB_PREFIX . "translation WHERE `table` = '" . $this->db->escape($table) . "'");
			
			$translations = $result->rows;
		}
		
		foreach($translations as $translation){
			$this->db->query("DELETE FROM " . DB_PREFIX . "translation_text WHERE translation_id = '$translation[translation_id]' AND object_id = '" . (int)$object_id . "'");
		}
		
		$this->cache->delete('translate');
	}
	
	public function get_translation_id($table, $field, $create = true){
		$translation_id = $this->db->query_var("SELECT translation_id FROM " . DB_PREFIX . "translation WHERE `table` = '" . $this->db->escape($table) . "' AND `field` = '" . $this->db->escape($field) . "' LIMIT 1");
		
		if(!$translation_id && $create){
			$translation_id = $this->add($table, $field);
		}
		
		return $translation_id;
	}
	
	public function add($table, $field){
		$this->cache->delete('translate');
		
		$this->db->query("INSERT INTO " . DB_PREFIX . "translation SET `table` = '" . $this->db->escape($table) . "', `field` = '" . $this->db->escape($field) . "'");
		
		return $this->db->getLastId();
	}
	
	public function remove($table, $field){
		$translation_id = $this->get_translation_id($table, $field, false);
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "translation_text WHERE translation_id = '$translation_id'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "translation WHERE translation_id = '$translation_id'");
		
		$this->cache->delete('translate');
	}
}
