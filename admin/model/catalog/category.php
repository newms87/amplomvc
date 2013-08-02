<?php
class Admin_Model_Catalog_Category extends Model
{
	public function addCategory($data)
	{
		$data['date_added'] = $this->date->now();
		$data['date_modified'] = $data['date_added'];
		
		$category_id = $this->insert('category', $data);
		
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
		
		if (!empty($data['keyword'])) {
			$this->url->setAlias($data['keyword'], 'product/category', 'category_id=' . (int)$category_id);
		}
		
		if (!empty($data['translations'])) {
			$this->translation->setTranslations('category', $category_id, $data['translations']);
		}
		
		$this->cache->delete('category');
		
		return $category_id;
	}
	
	public function editCategory($category_id, $data)
	{
		$data['date_modified'] = $this->date->now();
		
		$this->update('category', $data, $category_id);
		
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
		
		if (!empty($data['keyword'])) {
			$this->url->setAlias($data['keyword'], 'product/category', 'category_id=' . (int)$category_id);
		} else {
			$this->url->removeAlias('product/category', 'category_id=' . (int)$category_id);
		}
		
		if (!empty($data['translations'])) {
			$this->translation->setTranslations('category', $category_id, $data['translations']);
		}
		
		$this->cache->delete('category');
	}
	
	public function deleteCategory($category_id)
	{
		$this->delete('category', $category_id);
		$this->delete('category_to_store', array('category_id'=>$category_id));
		$this->delete('category_to_layout', array('category_id'=>$category_id));
		
		$this->url->removeAlias('product/category', 'category_id=' . (int)$category_id);
		
		$this->translation->delete('category', $category_id);
		
		$this->delete('product_to_category', array('category_id'=>$category_id));
		
		$children = $this->queryRows("SELECT category_id FROM " . DB_PREFIX . "category WHERE parent_id = '" . (int)$category_id . "'");

		foreach ($children as $category) {
			$this->deleteCategory($category['category_id']);
		}
		
		$this->cache->delete('category');
	}

	public function getCategory($category_id)
	{
		$result = $this->queryRow("SELECT * FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$category_id . "'");
		
		$result['keyword'] = $this->url->getAlias('product/category', 'category_id=' . $category_id);
		
		return $result;
	}
	
	public function getCategories($data = array(), $select = '*', $total = false)
	{
		//Select
		if ($total) {
			$select = "COUNT(*) as total";
		} elseif (empty($select)) {
			$select = '*';
		}
		
		//From
		$from = DB_PREFIX . "category c";
		
		//Where
		$where = "1";
		
		if (!empty($data['category_ids'])) {
			$where .= " AND category_id IN (" . implode(',', $data['category_ids']) . ")";
		}

		if (!empty($data['parent_ids'])) {
			$where .= " AND parent_id IN (" . implode(',', $data['parent_ids']) . ")";
		}
		
		if (!empty($data['layouts'])) {
			$from .= " LEFT JOIN " . DB_PREFIX . "category_to_layout c2l ON (c.category_id=c2l.category_id)";
			
			$where .= " AND c2l.layout_id IN (" . implode(',', $data['layouts']) . ")";
		}
		
		//Order By and Limit
		if (!$total) {
			if (!empty($data['sort']) && strpos($data['sort'], '__image_sort__') === 0) {
				if (!$this->db->hasColumn('category', $data['sort'])) {
					$this->extend->enable_image_sorting('category', str_replace('__image_sort__', '', $data['sort']));
				}
			}
			
			$order = $this->extract_order($data);
			$limit = $this->extract_limit($data);
		} else {
			$order = '';
			$limit = '';
		}
		
		//The Query
		$query = "SELECT $select FROM $from WHERE $where $order $limit";
		
		$result = $this->query($query);
		
		if($total) {
			return $result->row['total'];
		}
		
		return $result->rows;
	}
	
	public function getCategoryTranslations($category_id)
	{
		$translate_fields = array(
			'name',
			'meta_keywords',
			'meta_description',
			'description',
		);
		
		return $this->translation->getTranslations('category', $category_id, $translate_fields);
	}
		
	public function update_field($category_id, $data)
	{
		$this->update('category', $data, $category_id);
	}

	//TODO: need to rethink this
	public function generate_url($category_id, $name)
	{
		$url = $this->Model_Setting_UrlAlias->format_url($name);
		$orig = $url;
		$count = 2;
		
		$url_alias = $category_id?$this->Model_Setting_UrlAlias->getUrlAliasByRouteQuery('product/category', "category_id=$category_id"):null;
		
		$test = $this->Model_Setting_UrlAlias->getUrlAliasByKeyword($url);
		while (!empty($test) && $test['url_alias_id'] != $url_alias['url_alias_id']) {
			$url = $orig . '-' . $count++;
			$test = $this->Model_Setting_UrlAlias->getUrlAliasByKeyword($url);
		}
		return $url;
	}
	
	public function getCategoriesWithParents($data = array(), $select = '', $delimeter = ' > ')
	{
		$categories = $this->getCategories($data, $select);
	
		foreach ($categories as &$category) {
			if ($category['parent_id'] > 0) {
				$parents = $this->Model_Catalog_Category->getParents($category['category_id']);
				
				if (!empty($parents)) {
					$category['name'] = implode($delimeter, array_column($parents, 'name')) . $delimeter . $category['name'];
				}
			}
		}
		
		return $categories;
	}
	
	public function getParents($category_id)
	{
		$language_id = $this->config->get('config_language_id');
		
		$parents = $this->cache->get("category.parents.$category_id.$language_id");
		
		if (!$parents) {
			$parents = array();
			
			$parent_id = $category_id;
			
			while ($parent_id > 0) {
				$parent = $this->queryRow("SELECT * FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$parent_id . "' LIMIT 1");
				
				if (!$parent) {
					break;
				}
				
				$parent_id = (int)$parent['parent_id'];
				
				if ($parent['category_id'] == $category_id) {
					continue;
				}
				
				if (isset($parents[$parent_id])) {
					trigger_error("There is a circular reference for parent categories for $category_id!");
					exit();
				}
				
				$this->translation->translate('category', $parent_id, $parent);
				
				$parents[$parent_id] = $parent;
			}
			
			$this->cache->set("category.parents.$category_id.$language_id", $parents);
		}
		
		return $parents;
	}
	
	public function getCategoryStores($category_id)
	{
		$category_store_data = array();
		
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "category_to_store WHERE category_id = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$category_store_data[] = $result['store_id'];
		}
		
		return $category_store_data;
	}

	public function getCategoryLayouts($category_id)
	{
		$category_layout_data = array();
		
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "category_to_layout WHERE category_id = '" . (int)$category_id . "'");
		
		foreach ($query->rows as $result) {
			$category_layout_data[$result['store_id']] = $result['layout_id'];
		}
		
		return $category_layout_data;
	}
		
	public function getTotalCategories($data = array())
	{
		return $this->getCategories($data, '', true);
	}
}