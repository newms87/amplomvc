<?php
class ModelCatalogCategory extends Model {
	public function getCategory($category_id) {
		$query = $this->query("SELECT DISTINCT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.category_id = '" . (int)$category_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = '1'");
		
		return $query->row;
	}
	
	public function getCategories($parent_id = 0) {
	   $parent = '';
      if($parent_id >= 0){
         $parent = "c.parent_id = '" . (int)$parent_id . "' AND";
      }
      
		$query = $this->query(
		       "SELECT * FROM " . DB_PREFIX . "category c " . 
		       "LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) " . 
		       "LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) " .
		       "WHERE $parent cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'  AND c.status = '1' ORDER BY c.sort_order, LCASE(cd.name)");
		
		return $query->rows;
	}

	public function getCategoriesByParentId($category_id) {
		$category_data = array();
		
		$category_query = $this->query("SELECT category_id FROM " . DB_PREFIX . "category WHERE parent_id = '" . (int)$category_id . "'");
		
		foreach ($category_query->rows as $category) {
			$category_data[] = $category['category_id'];
			
			$children = $this->getCategoriesByParentId($category['category_id']);
			
			if ($children) {
				$category_data = array_merge($children, $category_data);
			}			
		}
		
		return $category_data;
	}
	
	/**
	 * This retrieves all the catgegories into an array tree
	 * It does not include categories that have no products in it, however
	 * if a product was deleted (and was the last in the category) the cache will
	 * first have to expire before getting the updated category list.
	 */
	public function getAllCategories(){
		$lang_id = (int)$this->config->get('config_language_id');
		$store_id = (int)$this->config->get('config_store_id');
		$categories = $this->cache->get("category.all.$store_id.$lang_id");
		
		if(!$categories || true){
		   $dt_zero = DATETIME_ZERO;
		   $product_check = "(SELECT COUNT(*) FROM " . DB_PREFIX . "product p" . 
		                    " JOIN " . DB_PREFIX . "product_to_category p2c ON (p2c.product_id=p.product_id)" .
		                    " JOIN " . DB_PREFIX . "manufacturer m ON(m.manufacturer_id=p.manufacturer_id AND m.status='1' AND (m.date_expires='$dt_zero' OR m.date_expires > NOW()))" .
		                    " WHERE p.status='1' AND p2c.category_id = c.category_id AND (p.date_available='$dt_zero' OR p.date_available < NOW()) AND (p.date_expires='$dt_zero' OR p.date_expires > NOW())) as num_products";
         
         $query = $this->query("SELECT c.*, cd.*, $product_check FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id)" .
		                             " WHERE c2s.store_id = '$store_id' AND cd.language_id = '$lang_id' AND c.status = '1' ORDER BY c.parent_id, LCASE(cd.name)");
			
			if(!isset($query->rows))return null;
         
			//Disclude any empty
         foreach($query->rows as $key=>$qr){
            if($qr['num_products'] == 0) unset($query->rows[$key]);
			}
			
         $categories = $query->rows;
			foreach ($categories as &$cat){
				if($cat['parent_id'] > 0){
				   foreach($categories as &$cat_parent){
				      if($cat_parent['category_id'] == $cat['parent_id']){
				        $cat_parent['children'][] = $cat;
				      }
				   }
			   }
         }
			
         foreach($categories as $key=>$cat){
            if($cat['parent_id'] != 0){
               unset($categories[$key]);
            }
         }
         
			$this->cache->set("category.all.$store_id.$lang_id", $categories);
		}
		return $categories;
	}
	
	public function getCategoryLayoutId($category_id) {
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "category_to_layout WHERE category_id = '" . (int)$category_id . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");
		
		if ($query->num_rows) {
			return $query->row['layout_id'];
		} else {
			return $this->config->get('config_layout_category');
		}
	}
					
	public function getTotalCategoriesByCategoryId($parent_id = 0) {
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = '1'");
		
		return $query->row['total'];
	}
}