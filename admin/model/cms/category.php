<?php
class ModelCmsCategory extends Model {
	public function addCategory($data) {
		$this->query("INSERT INTO " . DB_PREFIX . "cms_category SET parent_id = '" . (int)$data['parent_id'] . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', date_modified = NOW(), date_added = NOW()");
	
		$cms_category_id = $this->db->getLastId();
		
		if (isset($data['image'])) {
			$this->query("UPDATE " . DB_PREFIX . "cms_category SET image = '" . $this->db->escape(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8')) . "' WHERE cms_category_id = '" . (int)$cms_category_id . "'");
		}
		
		foreach ($data['category_description'] as $language_id => $value) {
		   if(!$value['page_title'])
            $value['page_title'] = $value['name'];
			$this->query("INSERT INTO " . DB_PREFIX . "cms_category_description SET cms_category_id = '" . (int)$cms_category_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', page_title = '" . $this->db->escape($value['page_title']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', description = '" . $this->db->escape($value['description']) . "'");
		}
		
		if (isset($data['category_store'])) {
			foreach ($data['category_store'] as $store_id) {
				$this->query("INSERT INTO " . DB_PREFIX . "cms_category_to_store SET cms_category_id = '" . (int)$cms_category_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		if (isset($data['category_layout'])) {
			foreach ($data['category_layout'] as $store_id => $layout) {
				if ($layout['layout_id']) {
					$this->query("INSERT INTO " . DB_PREFIX . "cms_category_to_layout SET cms_category_id = '" . (int)$cms_category_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout['layout_id'] . "'");
				}
			}
		}
						
		if ($data['keyword']) {
         $this->model_setting_url_alias->addUrlAlias(array('query'=>'cms_category_id='.(int)$cms_category_id, 'keyword'=>$this->db->escape($data['keyword']), 'status'=>$data['status']));
		}
		
		$this->cache->delete('cms_category');
	}
	
	public function editCategory($cms_category_id, $data) {
		$this->query("UPDATE " . DB_PREFIX . "cms_category SET parent_id = '" . (int)$data['parent_id'] . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', date_modified = NOW() WHERE cms_category_id = '" . (int)$cms_category_id . "'");

		if (isset($data['image'])) {
			$this->query("UPDATE " . DB_PREFIX . "cms_category SET image = '" . $this->db->escape(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8')) . "' WHERE cms_category_id = '" . (int)$cms_category_id . "'");
		}

		$this->query("DELETE FROM " . DB_PREFIX . "cms_category_description WHERE cms_category_id = '" . (int)$cms_category_id . "'");

		foreach ($data['category_description'] as $language_id => $value) {
		   if(!$value['page_title'])
            $value['page_title'] = $value['name'];
			$this->query("INSERT INTO " . DB_PREFIX . "cms_category_description SET cms_category_id = '" . (int)$cms_category_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', page_title = '" . $this->db->escape($value['page_title']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', description = '" . $this->db->escape($value['description']) . "'");
		}
		
		$this->query("DELETE FROM " . DB_PREFIX . "cms_category_to_store WHERE cms_category_id = '" . (int)$cms_category_id . "'");
		
		if (isset($data['category_store'])) {		
			foreach ($data['category_store'] as $store_id) {
				$this->query("INSERT INTO " . DB_PREFIX . "cms_category_to_store SET cms_category_id = '" . (int)$cms_category_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		$this->query("DELETE FROM " . DB_PREFIX . "cms_category_to_layout WHERE cms_category_id = '" . (int)$cms_category_id . "'");

		if (isset($data['category_layout'])) {
			foreach ($data['category_layout'] as $store_id => $layout) {
				if ($layout['layout_id']) {
					$this->query("INSERT INTO " . DB_PREFIX . "cms_category_to_layout SET cms_category_id = '" . (int)$cms_category_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout['layout_id'] . "'");
				}
			}
		}
						
		$this->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'cms_category_id=" . (int)$cms_category_id. "'");
		
		if ($data['keyword']) {
		   $this->model_setting_url_alias->addUrlAlias(array('query'=>'cms_category_id='.(int)$cms_category_id, 'keyword'=>$this->db->escape($data['keyword']), 'status'=>$data['status']));
		}
		
		$this->cache->delete('cms_category');
	}
	
	public function deleteCategory($cms_category_id) {
		$this->query("DELETE FROM " . DB_PREFIX . "cms_category WHERE cms_category_id = '" . (int)$cms_category_id . "'");
		$this->query("DELETE FROM " . DB_PREFIX . "cms_category_description WHERE cms_category_id = '" . (int)$cms_category_id . "'");
		$this->query("DELETE FROM " . DB_PREFIX . "cms_category_to_store WHERE cms_category_id = '" . (int)$cms_category_id . "'");
		$this->query("DELETE FROM " . DB_PREFIX . "cms_category_to_layout WHERE cms_category_id = '" . (int)$cms_category_id . "'");
		$this->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'cms_category_id=" . (int)$cms_category_id . "'");
		
		$query = $this->query("SELECT cms_category_id FROM " . DB_PREFIX . "cms_category WHERE parent_id = '" . (int)$cms_category_id . "'");

		foreach ($query->rows as $result) {
			$this->deleteCategory($result['cms_category_id']);
		}
		
		$this->cache->delete('cms_category');
	} 

	public function getCategory($cms_category_id) {
		$query = $this->query("SELECT DISTINCT *, (SELECT keyword FROM " . DB_PREFIX . "url_alias WHERE query = 'cms_category_id=" . (int)$cms_category_id . "') AS keyword FROM " . DB_PREFIX . "cms_category WHERE cms_category_id = '" . (int)$cms_category_id . "'");
		
		return $query->row;
	} 
	
	public function getCategories($parent_id=null) {
		$category_data = $this->cache->get('cms_category.' . (int)$this->config->get('config_language_id') . '.' . (int)$parent_id);
	
		if (!$category_data) {
			$category_data = array();
		   $filter_parent = !is_null($parent_id)?"c.parent_id = '" . (int)$parent_id . "' AND":'';
			$query = $this->query("SELECT * FROM " . DB_PREFIX . "cms_category c LEFT JOIN " . DB_PREFIX . "cms_category_description cd ON (c.cms_category_id = cd.cms_category_id) WHERE $filter_parent cd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY c.sort_order, cd.name ASC");
		
			foreach ($query->rows as $result) {
				$category_data[] = array(
					'cms_category_id' => $result['cms_category_id'],
					'name'        => $this->getPath($result['cms_category_id'], $this->config->get('config_language_id')),
					'status'  	  => $result['status'],
					'sort_order'  => $result['sort_order']
				);
			
				$category_data = array_merge($category_data, $this->getCategories($result['cms_category_id']));
			}	
	
			$this->cache->set('cms_category.' . (int)$this->config->get('config_language_id') . '.' . (int)$parent_id, $category_data);
		}
		
		return $category_data;
	}
	
   public function generate_url($name){
      $url = $this->model_setting_url_alias->format_url($name);
      $orig = $url;
      $count = 2;
      $test = $this->model_setting_url_alias->getUrlAliasByKeyword($url);
      while(!empty($test)){
         $url = $orig . '-' . $count++;
         $test = $this->model_setting_url_alias->getUrlAliasByKeyword($url);
      }
      return $url;
   }
   
	public function getPath($cms_category_id) {
		$query = $this->query("SELECT name, parent_id FROM " . DB_PREFIX . "cms_category c LEFT JOIN " . DB_PREFIX . "cms_category_description cd ON (c.cms_category_id = cd.cms_category_id) WHERE c.cms_category_id = '" . (int)$cms_category_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY c.sort_order, cd.name ASC");
		
		if ($query->row['parent_id']) {
			return $this->getPath($query->row['parent_id'], $this->config->get('config_language_id')) . $this->_('text_separator') . $query->row['name'];
		} else {
			return $query->row['name'];
		}
	}
	
	public function getCategoryDescriptions($cms_category_id) {
		$category_description_data = array();
		
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "cms_category_description WHERE cms_category_id = '" . (int)$cms_category_id . "'");
		
		foreach ($query->rows as $result) {
			$category_description_data[$result['language_id']] = $result;
		}
		
		return $category_description_data;
	}	
	
	public function getCategoryStores($cms_category_id) {
		$category_store_data = array();
		
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "cms_category_to_store WHERE cms_category_id = '" . (int)$cms_category_id . "'");

		foreach ($query->rows as $result) {
			$category_store_data[] = $result['store_id'];
		}
		
		return $category_store_data;
	}

	public function getCategoryLayouts($cms_category_id) {
		$category_layout_data = array();
		
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "cms_category_to_layout WHERE cms_category_id = '" . (int)$cms_category_id . "'");
		
		foreach ($query->rows as $result) {
			$category_layout_data[$result['store_id']] = $result['layout_id'];
		}
		
		return $category_layout_data;
	}
		
	public function getTotalCategories() {
      	$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "cms_category");
		
		return $query->row['total'];
	}	
		
	public function getTotalCategoriesByImageId($image_id) {
      $query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "cms_category WHERE image_id = '" . (int)$image_id . "'");
		
		return $query->row['total'];
	}

	public function getTotalCategoriesByLayoutId($layout_id) {
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "cms_category_to_layout WHERE layout_id = '" . (int)$layout_id . "'");

		return $query->row['total'];
	}		
}