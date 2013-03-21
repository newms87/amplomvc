<?php
class ModelCmsCategory extends Model {
	public function getCategory($cms_category_id) {
		$query = $this->query("SELECT DISTINCT c.*, cd.* FROM " . DB_PREFIX . "cms_category c LEFT JOIN " . DB_PREFIX . "cms_category_description cd ON (c.cms_category_id = cd.cms_category_id) LEFT JOIN " . DB_PREFIX . "cms_category_to_store c2s ON (c.cms_category_id = c2s.cms_category_id) WHERE c.cms_category_id = '" . (int)$cms_category_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = '1'");
		
		return $query->row;
	}
	
	public function getCategories($parent_id = 0) {
		$query = $this->query("SELECT c.*, cd.* FROM " . DB_PREFIX . "cms_category c LEFT JOIN " . DB_PREFIX . "cms_category_description cd ON (c.cms_category_id = cd.cms_category_id) LEFT JOIN " . DB_PREFIX . "cms_category_to_store c2s ON (c.cms_category_id = c2s.cms_category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'  AND c.status = '1' ORDER BY c.sort_order, LCASE(cd.name)");
		
		return $query->rows;
	}

	public function getCategoriesByParentId($cms_category_id) {
		$category_data = array();
		
		$category_query = $this->query("SELECT cms_category_id FROM " . DB_PREFIX . "cms_category WHERE parent_id = '" . (int)$cms_category_id . "'");
		
		foreach ($category_query->rows as $category) {
			$category_data[] = $category['cms_category_id'];
			
			$children = $this->getCategoriesByParentId($category['cms_category_id']);
			
			if ($children) {
				$category_data = array_merge($children, $category_data);
			}			
		}
		
		return $category_data;
	}
	
	
	public function getAllCategories(){
		$lang_id = (int)$this->config->get('config_language_id');
		$store_id = (int)$this->config->get('config_store_id');
		$categories = $this->cache->get("cms_category.all.$store_id.$lang_id");
		
		if(!$categories){
		   $query = $this->query("SELECT * FROM " . DB_PREFIX . "cms_category c LEFT JOIN " . DB_PREFIX . "cms_category_description cd ON (c.cms_category_id = cd.cms_category_id) LEFT JOIN " . DB_PREFIX . "cms_category_to_store c2s ON (c.category_id = c2s.cms_category_id) WHERE c2s.store_id = '$store_id' AND cd.language_id = '$lang_id' AND c.status = '1' ORDER BY c.parent_id, LCASE(cd.name)");
			
			if(!isset($query->rows))return null;
         $categories = $query->rows;
			foreach ($categories as &$cat)
				if($cat['parent_id'] > 0)
				   foreach($categories as &$cat_parent)
				      if($cat_parent['cms_category_id'] == $cat['parent_id'])
				        $cat_parent['children'][] = $cat;
			
         foreach($categories as $key=>$cat)
            if($cat['parent_id'] != 0)
               unset($categories[$key]);
         
			$this->cache->set("cms_category.all.$store_id.$lang_id", $categories);
		}
		return $categories;
	}
	
   public function getCategoryPath($cms_category_id){
      $path = array();
      if($cms_category_id){
         $path = $this->cache->get("cms_category_path.$cms_category_id");
         if(!$path){
            $next_id = 1;
            $path = array();
            while($next_id){
               $query = $this->query("SELECT c.parent_id as category_id, cd.name, c2.cms_category_id as parent_id FROM " . DB_PREFIX . "cms_category c LEFT JOIN " . DB_PREFIX . "cms_category_description cd ON (cd.cms_category_id=c.parent_id) LEFT JOIN " . DB_PREFIX . "cms_category c2 ON (c2.cms_category_id=c.parent_id) WHERE c.cms_category_id='$cms_category_id'");
               $path[$query->row['category_id']] = $query->row['name'];
               $next_id = $query->row['parent_id'];
            }
            $this->cache->set("cms_category_path.$cms_category_id", $path);
         }
      }
      return $path;
   }
   
   public function getCategoryArticles($cms_category_id, $data){
      $limit = '';
      if(isset($data['limit'])){
         $start = (isset($data['start']) && $data['start'] > 0)?$data['start']:0;
         $limit = $data['limit'] > 0?$data['limit']:20;
         $limit = "LIMIT $start, $limit";
      }
      $query = $this->query("SELECT * FROM " . DB_PREFIX . "cms_article_to_category a2c LEFT JOIN " . DB_PREFIX . "cms_article a ON (a.article_id=a2c.article_id) WHERE a2c.cms_category_id='" . (int)$cms_category_id . "' $limit");
      return $query->rows;
   }
   
	public function getCategoryLayoutId($cms_category_id) {
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "cms_category_to_layout WHERE cms_category_id = '" . (int)$cms_category_id . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");
		
		if ($query->num_rows) {
			return $query->row['layout_id'];
		} else {
			return $this->config->get('config_layout_category');
		}
	}
					
	public function getTotalCategoriesByCategoryId($parent_id = 0) {
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "cms_category c LEFT JOIN " . DB_PREFIX . "cms_category_to_store c2s ON (c.cms_category_id = c2s.cms_category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = '1'");
		
		return $query->row['total'];
	}
}
