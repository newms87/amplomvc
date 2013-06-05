<?php
class Catalog_Model_Catalog_Category extends Model 
{
	public function getCategory($category_id)
	{
		$category = $this->query_row(
			"SELECT * FROM " . DB_PREFIX . "category c" .
			" LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id)" .
			" WHERE c.category_id = '" . (int)$category_id . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = '1'"
		);
		
		if ($category) {
			$this->translation->translate('category', $category_id, $category);
		}
		
		return $category;
	}
	
	public function getCategories($parent_id = 0)
	{
		$parent = '';
		
		if ($parent_id >= 0) {
			$parent = "c.parent_id = '" . (int)$parent_id . "' AND";
		}
		
		//TODO: Need vastly improved API
		$categories = $this->query_rows(
			"SELECT * FROM " . DB_PREFIX . "category c " .
			"LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) " .
			"WHERE $parent AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'  AND c.status = '1' ORDER BY c.sort_order, LCASE(name)"
		);
		
		foreach ($categories as &$category) {
			$this->translation->translate('category', $category['category_id'], $category);
		}
		
		return $query->rows;
	}
	
	public function getParents($category_id)
	{
		$language_id = $this->config->get('config_language_id');
		
		$parents = $this->cache->get("category.parents.$category_id.$language_id");
		
		if (!$parents) {
			$parents = array();
			
			$parent_id = $category_id;
			
			while ($parent_id > 0) {
				$parent = $this->query_row("SELECT * FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$parent_id . "' LIMIT 1");
				
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
	
	public function hasAttributeGroup($category_id, $attribute_group_id)
	{
		$query = 
			"SELECT COUNT(*) FROM " . DB_PREFIX . "product_to_category pc" .
			" LEFT JOIN " . DB_PREFIX . "product_attribute pa ON (pc.product_id=pa.product_id)" .
			" LEFT JOIN " . DB_PREFIX . "attribute a ON (a.attribute_id=pa.attribute_id)" .
			" WHERE a.attribute_group_id = '" . (int)$attribute_group_id . "' AND pc.category_id = '" . (int)$category_id . "' LIMIT 1";
			
		return $this->query_var($query);
	}
	
	public function getCategoryName($category_id)
	{
		$category = $this->query_row("SELECT name FROM " . DB_PREFIX . "category WHERE category_id='" . (int)$category_id . "'");
		
		$this->translation->translate('category', $category_id, $category);
		
		return $category['name'];
	}
	
	public function getCategoryLayoutId($category_id)
	{
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "category_to_layout WHERE category_id = '" . (int)$category_id . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");
		
		if ($query->num_rows) {
			return $query->row['layout_id'];
		} else {
			return $this->config->get('config_layout_category');
		}
	}
					
	public function getTotalCategoriesByCategoryId($parent_id = 0)
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = '1'");
		
		return $query->row['total'];
	}
}