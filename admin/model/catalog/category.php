<?php
class ModelCatalogCategory extends Model {
	public function addCategory($data) {
		$data['date_added'] = $this->tool->format_datetime();
		$data['date_modified'] = $data['date_added'];
		
		$category_id = $this->insert('category', $data);
		
		foreach ($data['category_description'] as $language_id => $value) {
			$value['category_id'] = $category_id;
			$value['language_id'] = $language_id;
			
			$this->insert('category_description', $value);
		}
		
		if (!empty($data['category_store'])) {
			foreach ($data['category_store'] as $store_id) {
				$store = array(
					'category_id' => $category_id,
					'store_id' => $store_id,
				);
				
				$this->insert('category_to_store', $store);
			}
		}

		if (isset($data['category_layout'])) {
			foreach ($data['category_layout'] as $store_id => $layout) {
				if ($layout['layout_id']) {
					$layout['category_id'] = $category_id;
					$layout['store_id'] = $store_id;
					
					$this->insert('category_to_layout', $layout);
				}
			}
		}
		
		if ($data['keyword']) {
			$this->url->set_alias($data['keyword'], 'product/category', 'category_id=' . (int)$category_id);
		}
		
		$this->cache->delete('category');
	}
	
	public function editCategory($category_id, $data) {
		$data['date_modified'] = $this->tool->format_datetime();
		
		$this->update('category', $data, $category_id);

		$this->delete('category_description', array('category_id' => $category_id));
		
		foreach ($data['category_description'] as $language_id => $value) {
			$value['category_id'] = $category_id;
			$value['language_id'] = $language_id;
			
			$this->insert('category_description', $value);
		}
		
		$this->delete('category_to_store', array('category_id' => $category_id));
		
		if (!empty($data['category_store'])) {
			foreach ($data['category_store'] as $store_id) {
				$store = array(
					'category_id' => $category_id,
					'store_id' => $store_id,
				);
				
				$this->insert('category_to_store', $store);
			}
		}
		
		$this->delete('category_to_layout', array('category_id' => $category_id));

		if (isset($data['category_layout'])) {
			foreach ($data['category_layout'] as $store_id => $layout) {
				if ($layout['layout_id']) {
					$layout['category_id'] = $category_id;
					$layout['store_id'] = $store_id;
					
					$this->insert('category_to_layout', $layout);
				}
			}
		}
		
		if ($data['keyword']) {
			$this->url->set_alias($data['keyword'], 'product/category', 'category_id=' . (int)$category_id);
		}
		
		$this->cache->delete('category');
	}
	
	public function deleteCategory($category_id) {
		$this->delete('category', $category_id);
		$this->delete('category_description', array('category_id'=>$category_id));
		$this->delete('category_to_store', array('category_id'=>$category_id));
		$this->delete('category_to_layout', array('category_id'=>$category_id));
		
		$this->model_setting_url_alias->deleteUrlAliasByRouteQuery('product/category', "category_id=" . (int)$category_id . "'");
		
		$this->delete('product_to_category', array('category_id'=>$category_id));
		
		$query = $this->get('category', 'category_id', array('parent_id'=>$category_id));

		foreach ($query->rows as $result) {
			$this->deleteCategory($result['category_id']);
		}
		
		$this->url->remove_alias('product/category', 'category_id=' . $category_id);
		
		$this->cache->delete('category');
	} 

	public function getCategory($category_id) {
		$result = $this->query_row("SELECT * FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$category_id . "'");
		
		$result['keyword'] = $this->url->get_alias('product/category', 'category_id=' . $category_id);
		
		return $result;
	} 
	
	public function getCategories($parent_id = 0) {
		$category_data = $this->cache->get('category.' . (int)$this->config->get('config_language_id') . '.' . (int)$parent_id);
	
		if (!$category_data) {
			$category_data = array();
			$filter_parent = !is_null($parent_id) ? "c.parent_id = '" . (int)$parent_id . "' AND" : '';
			$query = $this->query("SELECT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) WHERE $filter_parent cd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY c.sort_order, cd.name ASC");
			
			foreach ($query->rows as $result) {
				$category_data[] = array(
					'category_id' => $result['category_id'],
					'name'		=> $this->getPath($result['category_id'], $this->config->get('config_language_id')),
					'status'  	=> $result['status'],
					'sort_order'  => $result['sort_order']
				);
			
				$category_data = array_merge($category_data, $this->getCategories($result['category_id']));
			}	
	
			$this->cache->set('category.' . (int)$this->config->get('config_language_id') . '.' . (int)$parent_id, $category_data);
		}
		
		return $category_data;
	}
	
	//TODO: need to rethink this
	public function generate_url($category_id, $name){
		$url = $this->model_setting_url_alias->format_url($name);
		$orig = $url;
		$count = 2;
		
		$url_alias = $category_id?$this->model_setting_url_alias->getUrlAliasByRouteQuery('product/category', "category_id=$category_id"):null;
		
		$test = $this->model_setting_url_alias->getUrlAliasByKeyword($url);
		while(!empty($test) && $test['url_alias_id'] != $url_alias['url_alias_id']){
			$url = $orig . '-' . $count++;
			$test = $this->model_setting_url_alias->getUrlAliasByKeyword($url);
		}
		return $url;
	}
	
	public function getPath($category_id) {
		$query = $this->query("SELECT name, parent_id FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) WHERE c.category_id = '" . (int)$category_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY c.sort_order, cd.name ASC");
		
		if ($query->row['parent_id']) {
			return $this->getPath($query->row['parent_id'], $this->config->get('config_language_id')) . $this->_('text_separator') . $query->row['name'];
		} else {
			return $query->row['name'];
		}
	}
	
	public function getCategoryDescriptions($category_id) {
		$category_description_data = array();
		
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "category_description WHERE category_id = '" . (int)$category_id . "'");
		
		foreach ($query->rows as $result) {
			$category_description_data[$result['language_id']] = array(
				'name'				=> $result['name'],
				'meta_keyword'	=> $result['meta_keyword'],
				'meta_description' => $result['meta_description'],
				'description'		=> $result['description']
			);
		}
		
		return $category_description_data;
	}	
	
	public function getCategoryStores($category_id) {
		$category_store_data = array();
		
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "category_to_store WHERE category_id = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$category_store_data[] = $result['store_id'];
		}
		
		return $category_store_data;
	}

	public function getCategoryLayouts($category_id) {
		$category_layout_data = array();
		
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "category_to_layout WHERE category_id = '" . (int)$category_id . "'");
		
		foreach ($query->rows as $result) {
			$category_layout_data[$result['store_id']] = $result['layout_id'];
		}
		
		return $category_layout_data;
	}
		
	public function getTotalCategories() {
			$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "category");
		
		return $query->row['total'];
	}	
	
	public function getTotalCategoriesByLayoutId($layout_id) {
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "category_to_layout WHERE layout_id = '" . (int)$layout_id . "'");

		return $query->row['total'];
	}		
}