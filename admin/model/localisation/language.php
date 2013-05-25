<?php
class ModelLocalisationLanguage extends Model {
	public function addLanguage($data) {
		$language_id = $this->insert('language', $data);
		
		$this->cache->delete('language');
	}
	
	public function editLanguage($language_id, $data) {
		$this->update('language', $data, $language_id);
				
		$this->cache->delete('language');
	}
	
	public function deleteLanguage($language_id) {
		$this->delete('language', $language_id);
		
		$this->cache->delete('language');
	}
	
	public function getLanguage($language_id) {
		$query = $this->get('language', '*', (int)$language_id);
	
		return $query->row;
	}

	public function getLanguages($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "language";
	
			$sort_data = array(
				'name',
				'code',
				'sort_order'
			);	
			
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];	
			} else {
				$sql .= " ORDER BY sort_order, name";	
			}
			
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$sql .= " DESC";
			} else {
				$sql .= " ASC";
			}
			
			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}					

				if ($data['limit'] < 1) {
					$data['limit'] = 20;
				}	
			
				$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			}
			
			$query = $this->query($sql);
	
			return $query->rows;
		} else {
			$language_data = $this->cache->get('language');
		
			if (!$language_data) {
				$language_data = array();
				
				$query = $this->query("SELECT * FROM " . DB_PREFIX . "language ORDER BY sort_order, name");
	
				foreach ($query->rows as $result) {
						$language_data[$result['code']] = array(
							'language_id' => $result['language_id'],
							'name'		=> $result['name'],
							'code'		=> $result['code'],
							'locale'		=> $result['locale'],
							'image'		=> $result['image'],
							'directory'	=> $result['directory'],
							'filename'	=> $result['filename'],
							'sort_order'  => $result['sort_order'],
							'status'		=> $result['status']
						);
				}	
			
				$this->cache->set('language', $language_data);
			}
		
			return $language_data;			
		}
	}
	
	public function getLanguageList(){
		$languages = $this->cache->get('language.list');
		
		if(!$languages){
			$result = $this->query("SELECT language_id, name, code, image, sort_order FROM " . DB_PREFIX . "language WHERE status >= 0 ORDER BY sort_order");
			
			$languages = array();
			
			foreach($result->rows as $row){
				$languages[$row['language_id']] = $row;
			}
			
			$this->cache->set('language.list', $languages);
		}
		
		return $languages;
	}
	
	public function getTotalLanguages() {
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "language WHERE status >= 0");
		
		return $query->row['total'];
	}
}
