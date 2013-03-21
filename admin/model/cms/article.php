<?php
class ModelCmsArticle extends Model {
	public function addArticle($data) {
      if(!$data['date_expires'])
         $data['date_expires'] = DATETIME_ZERO;
      
      if(!$data['date_active'])
         $data['date_active'] = DATETIME_ZERO;
      
      $data['user_created'] = $this->user->getUserName();
      
      $data['user_modified'] = $this->user->getUserName();
      
      $this->query("INSERT INTO " . DB_PREFIX . "cms_article SET aid='', author = '" . $this->db->escape($data['author']) . "', syndicator = '" . $this->db->escape($data['syndicator']) . "', members_only = '" . (int)$data['members_only'] . "', gads = '" . (int)$data['gads'] . "', pagination_type = '" . $this->db->escape($data['pagination_type']) . "', date_active='" . $this->tool->format_datetime($data['date_active']) . "', date_expires='" . $this->tool->format_datetime($data['date_expires']) . "', date_created=NOW(), date_modified=NOW(), user_created = '" . $data['user_created'] . "', user_modified = '" . $data['user_modified'] . "', status='" . (int)$data['status'] . "'");
		
		$article_id = $this->db->getLastId();
      
      $aid = $this->generate_article_id(array('article_id'=>$article_id));
      $this->query("UPDATE " . DB_PREFIX . "cms_article SET aid='$aid' WHERE article_id='$article_id'");
      
		foreach ($data['article_description'] as $language_id => $value) {
			$this->query("INSERT INTO " . DB_PREFIX . "cms_article_description SET article_id = '" . (int)$article_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', description = '" . $this->db->escape($value['description']) . "', intro = '" . $this->db->escape($value['intro']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
			$this->cache->delete("article_$article_id.$language_id");
		}
		
		if (isset($data['image'])) {
			$this->query("UPDATE " . DB_PREFIX . "cms_article SET image = '" . $this->db->escape(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8')) . "' WHERE article_id = '" . (int)$article_id . "'");
		}
		
      if(isset($data['article_category'])){
         foreach($data['article_category'] as $cat){
            $this->query("INSERT INTO " . DB_PREFIX . "cms_article_to_category SET article_id='" . (int)$article_id . "', cms_category_id='" . (int)$cat . "'");
         }
      }
      
		if (isset($data['article_store'])) {
			foreach ($data['article_store'] as $store_id) {
				$this->query("INSERT INTO " . DB_PREFIX . "cms_article_to_store SET article_id = '" . (int)$article_id . "', store_id = '" . (int)$store_id . "'");
			}
		}
      
      if (isset($data['article_layout'])) {
         foreach ($data['article_layout'] as $store_id=>$layout_id) {
            $this->query("INSERT INTO " . DB_PREFIX . "cms_article_to_layout SET article_id = '" . (int)$article_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
         }
      }
		
      if(isset($data['related_articles'])){
         foreach($data['related_articles'] as $a){
            $this->query("INSERT INTO " . DB_PREFIX . "cms_article_related SET article_id='" . (int)$article_id ."', related_id = '" . (int)$a . "'");
         }
      }
      
      if($data['article_tag']){
         foreach($data['article_tag'] as $language_id=>$tags){
            foreach(explode(',',$tags) as $tag)
               $this->query("INSERT INTO " . DB_PREFIX . "cms_article_tag SET article_id='" . (int)$article_id . "', tag='" . $this->db->escape(trim($tag)) . "', language_id = '" . $language_id . "'");
         }
      }
      
		if ($data['keyword']) {
		   $urla = array('keyword'=>$data['keyword'],'query'=>'article_id='.$article_id,'status'=>$data['status']);
			$this->model_setting_url_alias->addUrlAlias($urla);
		}
		
		$this->cache->delete('article');
	}
	
	public function editArticle($article_id, $data) {
	   if(!$data['date_expires'])
         $data['date_expires'] = DATETIME_ZERO;
      
      if(!$data['date_active'])
         $data['date_active'] = DATETIME_ZERO;
   
      $data['user_modified'] = $this->user->getUserName();
      
	   $this->query("UPDATE " . DB_PREFIX . "cms_article SET author = '" . $this->db->escape($data['author']) . "', syndicator = '" . $this->db->escape($data['syndicator']) . "', members_only = '" . (int)$data['members_only'] . "', gads = '" . (int)$data['gads'] . "', pagination_type = '" . $this->db->escape($data['pagination_type']) . "', date_active='" . $this->tool->format_datetime($data['date_active']) . "', date_expires='" . $this->tool->format_datetime($data['date_expires']) . "', date_modified=NOW(), user_modified = '" . $data['user_modified'] . "', status='" . (int)$data['status'] . "' WHERE article_id='" . (int)$article_id . "'");
		
		$this->query("DELETE FROM " . DB_PREFIX . "cms_article_description WHERE article_id = '" . (int)$article_id . "'");

		foreach ($data['article_description'] as $language_id => $value) {
			$this->query("INSERT INTO " . DB_PREFIX . "cms_article_description SET article_id = '" . (int)$article_id . "', language_id = '" . (int)$language_id . "',  title = '" . $this->db->escape($value['title']) . "', description = '" . $this->db->escape($value['description']) . "', intro = '" . $this->db->escape($value['intro']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
			$this->cache->delete("article_$article_id.$language_id");
		}

		if (isset($data['image'])) {
			$this->query("UPDATE " . DB_PREFIX . "cms_article SET image = '" . $this->db->escape(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8')) . "' WHERE article_id = '" . (int)$article_id . "'");
		}
		
		$this->query("DELETE FROM " . DB_PREFIX . "cms_article_to_category WHERE article_id = '" . (int)$article_id . "'");
		if(isset($data['article_category'])){
         foreach($data['article_category'] as $cat){
            $this->query("INSERT INTO " . DB_PREFIX . "cms_article_to_category SET article_id='" . (int)$article_id . "', cms_category_id='" . (int)$cat . "'");
         }
      }
		
		$this->query("DELETE FROM " . DB_PREFIX . "cms_article_to_store WHERE article_id = '" . (int)$article_id . "'");
		if (isset($data['article_store'])) {
			foreach ($data['article_store'] as $store_id) {
				$this->query("INSERT INTO " . DB_PREFIX . "cms_article_to_store SET article_id = '" . (int)$article_id . "', store_id = '" . (int)$store_id . "'");
			}
		}
      
      $this->query("DELETE FROM " . DB_PREFIX . "cms_article_to_layout WHERE article_id = '" . (int)$article_id . "'");
      if (isset($data['article_layout'])) {
         foreach ($data['article_layout'] as $store_id=>$layout_id) {         
            $this->query("INSERT INTO " . DB_PREFIX . "cms_article_to_layout SET article_id = '" . (int)$article_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
         }
      }
      
      $this->query("DELETE FROM " . DB_PREFIX . "cms_article_related WHERE article_id='" . (int)$article_id . "'");
      if(isset($data['related_articles'])){
         foreach($data['related_articles'] as $a)
            $this->query("INSERT INTO " . DB_PREFIX . "cms_article_related SET article_id='" . (int)$article_id ."', related_id = '" . (int)$a . "'");
      }
      
      $this->query("DELETE FROM " . DB_PREFIX . "cms_article_tag WHERE article_id='" . (int)$article_id . "'");
      if($data['article_tag']){
         foreach($data['article_tag'] as $language_id=>$tags){
            foreach(explode(',',$tags) as $tag)
               $this->query("INSERT INTO " . DB_PREFIX . "cms_article_tag SET article_id='" . (int)$article_id . "', tag='" . $this->db->escape(trim($tag)) . "', language_id = '" . $language_id . "'");
         }
      }
		
      $this->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'article_id=" . (int)$article_id. "'");
		if ($data['keyword']) {
         $urla = array('keyword'=>$data['keyword'],'query'=>'article_id='.$article_id,'status'=>$data['status']);
         $this->model_setting_url_alias->addUrlAlias($urla);
      }

		$this->cache->delete('article');
	}
	
	public function deleteArticle($article_id) {
		$this->query("DELETE FROM " . DB_PREFIX . "cms_article WHERE article_id = '" . (int)$article_id . "'");
		$this->query("DELETE FROM " . DB_PREFIX . "cms_article_description WHERE article_id = '" . (int)$article_id . "'");
		$this->query("DELETE FROM " . DB_PREFIX . "cms_article_to_store WHERE article_id = '" . (int)$article_id . "'");
		$this->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'article_id=" . (int)$article_id . "'");
	   $this->query("DELETE FROM " . DB_PREFIX . "cms_article_related WHERE article_id='" . (int)$article_id . "' OR related_id='" . (int)$article_id . "'");
      
		$this->cache->delete('article');
	}	
	
   public function generate_article_id($data){
      return 'a' . ((int)$data['article_id']< 10000?sprintf('%04d',$data['article_id']):$data['article_id']);
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
   
   public function updateArticleValue($article_id, $key, $value){
      $this->query("UPDATE " . DB_PREFIX . "cms_article SET `$key`='" . $this->db->escape($value) . "' WHERE article_id='" . (int)$article_id . "'");
   }
   
   public function updateArticleCategory($article_id, $op, $cms_category_id){
      $this->query("DELETE FROM " . DB_PREFIX . "cms_article_to_category WHERE article_id='$article_id' AND cms_category_id='$cms_category_id'");
      if($op == 'add')
         $this->query("INSERT INTO " . DB_PREFIX . "cms_article_to_category SET article_id='$article_id', cms_category_id='$cms_category_id'");
   }

	public function getArticle($article_id) {
		$query = $this->query("SELECT DISTINCT * FROM " . DB_PREFIX . "cms_article WHERE article_id = '" . (int)$article_id . "'");
		
		return $query->row;
	}
	
	public function getArticles($data = array(),$total=false) {
		if ($data) {
		   $select = "a.*, ad.title";
         
         $description = "LEFT JOIN " . DB_PREFIX . "cms_article_description ad ON (a.article_id=ad.article_id)"; 
         
		   $where = '';
			if(isset($data['article_ids'])){
            $where = "WHERE article_id IN(" . implode(',', $data['article_ids']) . ")"; 
         }
			
         $order = isset($data['order'])?$data['order']:'';
         $sort = isset($data['sort'])?$data['sort']:'';
         $order_by =  $sort?"ORDER BY $sort $order":'';
			
         $limit = '';
			if (isset($data['limit'])) {
				$start = !isset($data['start']) || $data['start'] < 0?0:$data['start'];
            $limit = $data['limit']<1?20:$data['limit'];
				$limit = "LIMIT $start, $limit";
			}
         
         if($total)
            $select = "COUNT(*) as total";
         
         $sql = "SELECT $select FROM " . DB_PREFIX . "cms_article a $description $where " . ($total?'':"$order_by $limit");
			$query = $this->query($sql);
		
			return $total?$query->row['total']:$query->rows;
		} else {
		   if($total){
            $query = $this->query("SELECT COUNT(*) as total FROM " . DB_PREFIX . "cms_article");
            return $query->row['total'];
         }
         
			$article_data = $this->cache->get('article');
         
			if (!$article_data) {
				$query = $this->query("SELECT * FROM " . DB_PREFIX . "cms_article ORDER BY title");
	
				$article_data = $query->rows;
			
				$this->cache->set('article', $article_data);
			}
		 
			return $article_data;
		}
	}
   
   public function getArticleKeyword($article_id){
      $query = $this->model_setting_url_alias->getUrlAliasByRouteQuery('article_id='.$article_id);
      return isset($query['keyword'])?$query['keyword']:'';
   }
   public function getArticleDescriptions($article_id){
		$descriptions = $this->cache->get("article_$article_id." . (int)$this->config->get('config_language_id'));
		if(!$descriptions){
			$query = $this->query("SELECT * FROM " . DB_PREFIX . "cms_article_description WHERE article_id='" . (int)$article_id . "'");
			
			$descriptions = array();
			foreach($query->rows as $result){
				$descriptions[$result['language_id']] = $result;
         }
			$this->cache->set("article_$article_id", $descriptions);
		}
      
		return $descriptions;
	}
   
   public function getArticleCategories($article_id){
      $query = $this->query("SELECT cms_category_id FROM " . DB_PREFIX . "cms_article_to_category WHERE article_id='$article_id'");
      $categories = array();
      foreach($query->rows as $row)
         $categories[] = $row['cms_category_id'];
      return $categories;
   }
   
   public function getArticleTags($article_id){
      $query = $this->query("SELECT * FROM " . DB_PREFIX . "cms_article_tag WHERE article_id='$article_id'");
      $tags = array();
      foreach($query->rows as $row)
         $tags[$row['language_id']][] = $row['tag'];
      return $tags;
   }
	
   public function getArticleRelated($article_id){
      $query = $this->query("SELECT * FROM " . DB_PREFIX . "cms_article_related WHERE article_id='$article_id'");
      return $query->rows;
   }
   
	public function getArticleStores($article_id) {
		$query = $this->query("SELECT store_id FROM " . DB_PREFIX . "cms_article_to_store WHERE article_id = '" . (int)$article_id . "'");
      $stores = array();
		foreach ($query->rows as $row)
			$stores[] = $row['store_id'];
		return $stores;
	}
   
   public function getArticleLayouts($article_id){
      $query = $this->query("SELECT * FROM " . DB_PREFIX . "cms_article_to_layout WHERE article_id = '" . (int)$article_id . "'");
      $article_layout = array();
      foreach($query->rows as $row)
         $article_layout[$row['store_id']] = $row;
      return $article_layout;
   }

	public function getTotalArticles($data=array()) {
		return $this->getArticles($data,true);
	}	
}
