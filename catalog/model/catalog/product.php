<?php
class ModelCatalogProduct extends Model {
	public function updateViewed($product_id) {
	   $user_id = $this->customer->getId();
		$this->query("INSERT INTO " . DB_PREFIX . "product_views SET product_id = '" . (int)$product_id . "', user_id = '" . (int)$user_id . "', session_id = '" . session_id() . "', ip_address = '" . $_SERVER['REMOTE_ADDR'] . "', date = NOW()");
	}
	
	public function getProduct($product_id) {
	   $product_id = (int)$product_id;
	   $lang_id = (int)$this->config->get('config_language_id');
      $store_id = (int)$this->config->get('config_store_id');
      
		if ($this->customer->isLogged()) {
			$customer_group_id = (int)$this->customer->getCustomerGroupId();
		} else {
			$customer_group_id = (int)$this->config->get('config_customer_group_id');
		}
		
      $discount = "(SELECT price FROM " . DB_PREFIX . "product_discount pdc WHERE pdc.product_id = p.product_id AND pdc.customer_group_id ='$customer_group_id' AND pdc.quantity >= '0' AND ((pdc.date_start = '0000-00-00' OR pdc.date_start < NOW()) AND (pdc.date_end = '0000-00-00' OR pdc.date_end > NOW())) ORDER BY pdc.priority ASC, pdc.price ASC LIMIT 1) AS discount";
      $special = "(SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '$customer_group_id' AND ((ps.date_start = '" . DATETIME_ZERO . "' OR ps.date_start < NOW()) AND (ps.date_end = '". DATETIME_ZERO . "' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special";
      $reward = "(SELECT points FROM " . DB_PREFIX . "product_reward pr WHERE pr.product_id = p.product_id AND pr.customer_group_id = '$customer_group_id') AS reward";
      $stock_status = "(SELECT ss.name FROM " . DB_PREFIX . "stock_status ss WHERE ss.stock_status_id = p.stock_status_id AND ss.language_id = '$lang_id') AS stock_status";
      $weight_class = "(SELECT wcd.unit FROM " . DB_PREFIX . "weight_class_description wcd WHERE p.weight_class_id = wcd.weight_class_id AND wcd.language_id = '$lang_id') AS weight_class";
      $length_class = "(SELECT lcd.unit FROM " . DB_PREFIX . "length_class_description lcd WHERE p.length_class_id = lcd.length_class_id AND lcd.language_id = '$lang_id') AS length_class";
      $rating = "(SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating";
      $reviews = "(SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r2 WHERE r2.product_id = p.product_id AND r2.status = '1' GROUP BY r2.product_id) AS reviews";
      $template = "(SELECT template FROM " . DB_PREFIX . "product_template pt WHERE pt.product_id = p.product_id AND pt.theme = '" . $this->config->get('config_template') . "' AND store_id = '$store_id') as template";  
      
		$fs_table = "(SELECT ops.flashsale_id, ops.product_id, ops.date_end, ops.price as special FROM 
                        (SELECT fp.flashsale_id, fp.product_id, ps.product_special_id, MIN(ps.price) as special FROM " . DB_PREFIX . "flashsale_product fp 
                         LEFT JOIN " . DB_PREFIX . "product_special ps ON (fp.product_id=ps.product_id) WHERE ps.customer_group_id='$customer_group_id' AND ps.date_start < NOW() AND ps.date_end > NOW() GROUP BY flashsale_id, product_id) as low_price
                         INNER JOIN " . DB_PREFIX ."product_special ops ON(low_price.flashsale_id=ops.flashsale_id AND ops.product_special_id=low_price.product_special_id AND low_price.product_id=ops.product_id AND low_price.special=ops.price) GROUP BY product_id
                        ) as fs_table ON(fs_table.product_id=p.product_id)";
      
      $category = DB_PREFIX . "product_to_category p2c ON (p2c.product_id=p.product_id)";
      $description = DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id AND pd.language_id='$lang_id')";
      $store = DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id AND p2s.store_id='$store_id')";
      
		$result = $this->query("SELECT p.*, p2c.category_id, pd.*, p2s.*, $special, fs_table.flashsale_id,fs_table.date_end, pd.name AS name, p.image, m.name AS manufacturer, m.keyword, m.status as manufacturer_status, $discount, $reward, $stock_status, $weight_class, $length_class, $rating, $reviews, $template, p.sort_order " . 
		                           "FROM " . DB_PREFIX . "product p LEFT JOIN $category LEFT JOIN $description JOIN $store LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) LEFT JOIN $fs_table WHERE p.product_id='$product_id' AND p.status = '1' AND m.status='1' AND p.date_available <= NOW()");
      
		if ($result->num_rows) {
			$result->row['price'] = ($result->row['discount'] ? $result->row['discount'] : $result->row['price']);
			$result->row['rating'] = (int)$result->row['rating'];
			
			return $result->row;
		} else {
			return false;
		}
	}
   
   public function getProductName($product_id){
      $result = $this->query("SELECT name FROM " . DB_PREFIX . "product_description WHERE product_id='" . (int)$product_id . "'");
      
      if($result->num_rows){
         return $result->row['name'];
      }
      
      return '';
   }
	
	/**
    * Retreives products by the specified filter and limit
    */
   public function getFilteredProducts($filter, $cat_filter, $limit=8, $page=1){
      $where = "";
      $sort = "";
      $select_more = '';
      
      switch($filter){
         case 'featured':
            $where = " AND p.product_id IN (" . implode(', ', $this->config->get('featured_product')) . ")";
            $sort = "ORDER BY FIELD(p.product_id," . implode(', ', $this->config->get('featured_product')) . ") ASC";
            break;
         case 'highest_price':
            $sort = "ORDER BY if(special IS NULL,price,if(price < special,price,special)) DESC";
            break;
         case 'lowest_price':
            $sort = "ORDER BY if(special IS NULL,price,if(price < special,price,special)) ASC";
            break;
         case 'popular':
            $sort = "ORDER BY p.viewed DESC";
            break;
         case 'newest':
            $sort = "ORDER BY p.date_available DESC";
            break;
         case 'ending_soon':
            $sort = "ORDER BY if(date_end IS NULL, '9999-99-99 00:00:00',date_end) ASC";
            break;
         case 'suggested':
            //we use the $page for suggested products as a quick hack
            $where = " AND p.product_id != '$page' AND (p.date_expires > NOW() OR p.date_expires='" . DATETIME_ZERO . "') AND m.status='1'";
            $page = 1;
            break;
         default:
            break;
      }
      
      $customer_group_id = (int)($this->customer->isLogged()?$this->customer->getCustomerGroupId():$this->config->get('config_customer_group_id'));
      $store_id = (int)$this->config->get('config_store_id');
      $lang_id = (int)$this->config->get('config_language_id');
      
      
      //select fields
      $select = "p.product_id, p.model, p.price, pd.name, p.image, p.is_final, p.tax_class_id";
      
      $store = DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id AND p2s.store_id='$store_id')";
      $product_description = DB_PREFIX . "product_description pd ON (p.product_id=pd.product_id AND pd.language_id='$lang_id')";
      
      $special = "(SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '$customer_group_id' AND ((ps.date_start = '" . DATETIME_ZERO . "' OR ps.date_start < NOW()) AND (ps.date_end = '". DATETIME_ZERO . "' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special";
      
      $fs_table = "(SELECT ops.flashsale_id, ops.product_id, ops.date_end, ops.price as special FROM 
                        (SELECT fp.flashsale_id, fp.product_id, ps.product_special_id, MIN(ps.price) as special FROM " . DB_PREFIX . "flashsale_product fp 
                         LEFT JOIN " . DB_PREFIX . "product_special ps ON (fp.product_id=ps.product_id) WHERE ps.customer_group_id='$customer_group_id' AND ps.date_start < NOW() AND ps.date_end > NOW() GROUP BY flashsale_id, product_id) as low_price
                         INNER JOIN " . DB_PREFIX ."product_special ops ON(low_price.flashsale_id=ops.flashsale_id AND ops.product_special_id=low_price.product_special_id AND low_price.product_id=ops.product_id AND low_price.special=ops.price) GROUP BY product_id
                        ) as fs_table ON(fs_table.product_id=p.product_id)";
      
      $select .= ", fs_table.flashsale_id, $special, fs_table.date_end";
      //category filter
      $category = $cat_filter?"JOIN " . DB_PREFIX . "product_to_category p2c ON (p2c.product_id=p.product_id AND p2c.category_id='$cat_filter')":"";
      
      $manufacturer = "LEFT JOIN " . DB_PREFIX . "manufacturer m ON (m.manufacturer_id=p.manufacturer_id)";
      
      if($limit < 0){
         $limit = "";
         $total = true;
         $select = "COUNT(*) as total";
      }
      else{
         $total = false;
         $page = ((int)$page-1) * (int)$limit;
         $limit = $limit?"LIMIT $page, $limit":"";
      }
      
      $result = "SELECT DISTINCT $select FROM " . DB_PREFIX . "product p $category JOIN $store $manufacturer LEFT JOIN $product_description LEFT JOIN $fs_table WHERE p.status='1' AND m.status='1' AND p.date_available < NOW() $where $sort $limit";
      $result = $this->query($result);
      
      if ($result->num_rows) {
         if($total)
            return $result->row['total'];
         return $result->rows;
      }
      return array();
   }
   
   
	public function getProducts($data = array(), $total = false) {
		$language_id = (int)$this->config->get('config_language_id');
		$store_id = (int)$this->config->get('config_store_id');
		
		//unique cache string for this query
		$cache_id = "product.$language_id.$store_id." . md5(http_build_query($data) . ($total?'total':''));
		
		$product_data = $this->cache->get($cache_id);
		
		if (!$product_data || true) {
			
			//SELECT
			if($total){
				$select = 'COUNT(*) as total';
			}else{
				$select = 'p.product_id';
			}
			
			//JOIN Tables
			$tables =
				" LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)" .
				" LEFT JOIN " . DB_PREFIX . "manufacturer m ON (m.manufacturer_id=p.manufacturer_id)";
			
			//WHERE
			$where = 
				"p.status='1' AND p2s.store_id = '$store_id' AND m.status='1' AND p.date_available <= NOW()" . 
				" AND (p.date_expires > NOW() OR p.date_expires = '" . DATETIME_ZERO . "')";
			
			//Product Description if needed
			if( (!empty($data['sort']) && $data['sort'] == 'pd.name') || 
				  !empty($data['name']) || !empty($data['name_like']) || !empty($data['search']) ){
				$tables .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)";
				
				$where .= " AND pd.language_id='$language_id'";
			}
			
			//Product IDs
			if (!empty($data['product_ids'])){
			   $where .= " AND p.product_id IN (" . implode(',', $data['product_ids']) . ")";
		   }
			
			//Product Name
			if (!empty($data['name'])){
				$where .= " AND pd.name = '" . $this->db->escape($data['name']) . "'";
			}
			
			//Product Name Search
			if (!empty($data['name_like'])){
				$where .= " AND pd.name like '%" . $this->db->escape($data['name']) . "%'";
			}
			
			//Product Manufacturer
			if (!empty($data['manufacturer_id'])) {
				$where .= " AND p.manufacturer_id = '" . (int)$data['manufacturer_id'] . "'";
			}
			
			//Reviews / Ratings
			if($this->config->get('config_review_status') && (isset($data['rating_min']) || isset($data['rating_max'])) ){
				$select .= "(SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating";
				
				if(isset($data['rating_min'])){
					$where .= 'rating >= ' . (int)$data['rating_min'];
				}
				
				if(isset($data['rating_max'])){
					$where .= 'rating <= ' . (int)$data['rating_max'];
				}
			}
			
			//Product Tag
			if (!empty($data['product_tag'])) {
				$tables .= " LEFT JOIN " . DB_PREFIX . "product_tag pt ON (p.product_id = pt.product_id)";
				
				$where .= " AND pt.tag = '" . $this->db->escape($data['product_tag']) . "'";
			}
			
			//Product Categories
			if (!empty($data['category_ids'])) {
				$tables .= " LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)";
				
				$where .= " AND p2c.category_id IN (" . implode(',', $data['category_ids']) . ")";
			}
			
			if (!empty($data['search'])) {
				//TODO: How do we handle the search query?!
				$terms = explode(' ', str_replace(',',' ',$data['search']));
				
				$where .= ' AND (';
				
				$or = '';
				foreach($terms as $term){
					$where .= $or . "pd.description like '%$term%'";
					if(!$or) $or = ' OR ';
				}
				
				$where .= ')';
			}
			
			//GROUP BY
			$group_by = " GROUP BY p.product_id";
			
			//ORDER BY and LIMIT
			$order_by = '';
			$limit = '';
			
			if(!$total){
				if(!empty($data['sort'])){
					$order = (!empty($data['order']) && $data['order'] == 'DESC') ? 'DESC' : 'ASC'; 
					
					$order_by = "ORDER BY $data[sort] $order";
				}
				
				$start = !empty($data['start']) ? (int)$data['start'] : 0;
				$limit = !empty($data['limit']) ? (int)$data['limit'] : $this->config->get('config_catalog_limit');
				
				$limit = "LIMIT $start,$limit";
			}
			
			//The Query
			$query = "SELECT $select  FROM " . DB_PREFIX . "product p $tables WHERE $where $group_by $order_by $limit";
			
			$result = $this->query($query);
			
			//Process Results
			if($total){
				$product_data = $result->row['total'];
			}
			else{
				$product_data = array();
				
				foreach ($result->rows as $row) {
					$product_data[$row['product_id']] = $this->getProduct($row['product_id']);
				}
			}
		
			$this->cache->set($cache_id, $product_data);
		}
		
		return $product_data;
	}
   
	public function getProductSuggestions($product, $limit){
	   if(!is_array($product))
	      $product = $this->getProduct($product);
	   $suggestions = $this->getFilteredProducts('suggested',$product['category_id'],$limit,$product['product_id']);
	   if(!$suggestions)
         $suggestions = $this->getFilteredProducts('featured','',$limit);
      return $suggestions;
   }
   
   public function getProductFlashsale($product_id){
      $result = $this->query("SELECT * FROM " . DB_PREFIX . "flashsale_product fp LEFT JOIN " . DB_PREFIX . "flashsale f ON(f.flashsale_id=fp.flashsale_id) WHERE fp.product_id='$product_id' AND f.status='1' AND f.date_start < NOW() AND f.date_end < NOW() ORDER BY f.flashsale_id DESC LIMIT 1");
      
      return $result->row;
   }
   
	public function getProductSpecials($data = array()) {
		if ($this->customer->isLogged()) {
			$customer_group_id = $this->customer->getCustomerGroupId();
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}	
				
		$sql = "SELECT DISTINCT ps.product_id, (SELECT AVG(rating) FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = ps.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating FROM " . DB_PREFIX . "product_special ps LEFT JOIN " . DB_PREFIX . "product p ON (ps.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND ps.customer_group_id = '" . (int)$customer_group_id . "' AND ((ps.date_start = '" . DATETIME_ZERO . "' OR ps.date_start < NOW()) AND (ps.date_end = '" . DATETIME_ZERO . "' OR ps.date_end > NOW())) GROUP BY ps.product_id";

		$sort_data = array(
			'pd.name',
			'p.model',
			'ps.price',
			'rating',
			'p.sort_order'
		);
		
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
				$sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
			} else {
				$sql .= " ORDER BY " . $data['sort'];
			}
		} else {
			$sql .= " ORDER BY p.sort_order";	
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

		$product_data = array();
		
		$result = $this->query($sql);
		
		foreach ($result->rows as $row) { 		
			$product_data[$row['product_id']] = $this->getProduct($row['product_id']);
		}
		
		return $product_data;
	}

	public function getProductsByManufacturer($manufacturer_id){
		if ($this->customer->isLogged()) {
			$customer_group_id = $this->customer->getCustomerGroupId();
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}
		$lang_id = (int)$this->config->get('config_language_id');
		$result = $this->query("SELECT DISTINCT *, pd.name AS name, p.image, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$customer_group_id . "' AND ((ps.date_start = '" . DATETIME_ZERO . "' OR ps.date_start < NOW()) AND (ps.date_end = '" . DATETIME_ZERO . "' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special, p.sort_order FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.manufacturer_id = '$manufacturer_id' AND pd.language_id = '$lang_id' AND p.status = '1' AND m.status='1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");
		
		if ($result->num_rows)
			return $result->rows;
		return false;
	}
	
	public function getLatestProducts($limit) {
		if ($this->customer->isLogged()) {
			$customer_group_id = $this->customer->getCustomerGroupId();
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}	
				
		$product_data = $this->cache->get('product.latest.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $customer_group_id . '.' . (int)$limit);

		if (!$product_data) { 
			$result = $this->query("SELECT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY p.date_added DESC LIMIT " . (int)$limit);
		 	 
			foreach ($result->rows as $row) {
				$product_data[$row['product_id']] = $this->getProduct($row['product_id']);
			}
			
			$this->cache->set('product.latest.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'). '.' . $customer_group_id . '.' . (int)$limit, $product_data);
		}
		
		return $product_data;
	}
	
	public function getPopularProducts($limit) {
		$product_data = array();
		
		$result = $this->query("SELECT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY p.viewed, p.date_added DESC LIMIT " . (int)$limit);
		
		foreach ($result->rows as $row) { 		
			$product_data[$row['product_id']] = $this->getProduct($row['product_id']);
		}
					 	 		
		return $product_data;
	}
   
	public function getBestSellerProducts($limit) {
		if ($this->customer->isLogged()) {
			$customer_group_id = $this->customer->getCustomerGroupId();
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}	
				
		$product_data = $this->cache->get('product.bestseller.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'). '.' . $customer_group_id . '.' . (int)$limit);

		if (!$product_data) { 
			$product_data = array();
			
			$result = $this->query("SELECT op.product_id, COUNT(*) AS total FROM " . DB_PREFIX . "order_product op LEFT JOIN `" . DB_PREFIX . "order` o ON (op.order_id = o.order_id) LEFT JOIN `" . DB_PREFIX . "product` p ON (op.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE o.order_status_id > '0' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' GROUP BY op.product_id ORDER BY total DESC LIMIT " . (int)$limit);
			
			foreach ($result->rows as $row) { 		
				$product_data[$row['product_id']] = $this->getProduct($row['product_id']);
			}
			
			$this->cache->set('product.bestseller.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'). '.' . $customer_group_id . '.' . (int)$limit, $product_data);
		}
		
		return $product_data;
	}
	
	public function getProductAttributes($product_id) {
		$product_attribute_group_data = array();
		
		$product_attribute_group_query = $this->query("SELECT ag.attribute_group_id, agd.name FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN " . DB_PREFIX . "attribute_group ag ON (a.attribute_group_id = ag.attribute_group_id) LEFT JOIN " . DB_PREFIX . "attribute_group_description agd ON (ag.attribute_group_id = agd.attribute_group_id) WHERE pa.product_id = '" . (int)$product_id . "' AND agd.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY ag.attribute_group_id ORDER BY ag.sort_order, agd.name");
		
		foreach ($product_attribute_group_query->rows as $product_attribute_group) {
			$product_attribute_data = array();
			
			$product_attribute_query = $this->query("SELECT a.attribute_id, ad.name, pa.text FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id) WHERE pa.product_id = '" . (int)$product_id . "' AND a.attribute_group_id = '" . (int)$product_attribute_group['attribute_group_id'] . "' AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "' AND pa.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY a.sort_order, ad.name");
			
			foreach ($product_attribute_query->rows as $product_attribute) {
				$product_attribute_data[] = array(
					'attribute_id' => $product_attribute['attribute_id'],
					'name'         => $product_attribute['name'],
					'text'         => $product_attribute['text']		 	
				);
			}
			
			$product_attribute_group_data[] = array(
				'attribute_group_id' => $product_attribute_group['attribute_group_id'],
				'name'               => $product_attribute_group['name'],
				'attribute'          => $product_attribute_data
			);			
		}
		
		return $product_attribute_group_data;
	}
			
	public function getProductOptions($product_id) {
         
      $result = $this->query("SELECT * FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY po.sort_order ASC, o.sort_order ASC");
      
      foreach ($result->rows as &$product_option) {
         $pov_result = $this->query("SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_id = '" . (int)$product_option['product_option_id'] . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY ov.sort_order");
         
         $product_option['product_option_value'] = $pov_result->rows;
      }  
      
      return $result->rows;
   }
   
   public function getProductOptionValueRestrictions($product_id){
      $language_id = $this->config->get('config_language_id');
      
      $result = $this->query("SELECT * FROM " . DB_PREFIX . "product_option_value_restriction WHERE product_id='" . (int)$product_id . "' AND quantity < '1'");
      
      $restrictions = array();
      
      foreach($result->rows as $value){
         $restrictions[$value['option_value_id']][] = $value['restrict_option_value_id'];
         $restrictions[$value['restrict_option_value_id']][] = $value['option_value_id'];
      }
      
      foreach($restrictions as &$r){
         $r = array_unique($r);
      }
      
      return $restrictions;
   }
	
	public function getProductDiscounts($product_id) {
		if ($this->customer->isLogged()) {
			$customer_group_id = $this->customer->getCustomerGroupId();
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}	
		
		$result = $this->query("SELECT * FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$customer_group_id . "' AND quantity > 0 AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY quantity ASC, priority ASC, price ASC");

		return $result->rows;		
	}
		
	public function getProductImages($product_id) {
		$result = $this->query("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "' ORDER BY sort_order ASC");

		return $result->rows;
	}
	
	public function getProductRelated($product_id) {
		$product_data = array();

		$result = $this->query("SELECT * FROM " . DB_PREFIX . "product_related pr LEFT JOIN " . DB_PREFIX . "product p ON (pr.related_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pr.product_id = '" . (int)$product_id . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");
		
		foreach ($result->rows as $row) { 
			$product_data[$row['related_id']] = $this->getProduct($row['related_id']);
		}
		
		return $product_data;
	}
   
   public function getProductsManufacturers($product_ids){
      if(empty($product_ids))return array();
      foreach($product_ids as $id)
         $where .= "OR p.product_id='$id' ";
      $result = $this->query("SELECT * FROM " . DB_PREFIX . "manufacturer m LEFT JOIN product p ON (m.manufacturer_id=p.manufacturer_id) WHERE TRUE AND ($where)");
      return $result->rows;
   }
   
	public function getProductTags($product_id) {
		$result = $this->query("SELECT * FROM " . DB_PREFIX . "product_tag WHERE product_id = '" . (int)$product_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $result->rows;
	}
		
	public function getProductLayoutId($product_id) {
		$result = $this->query("SELECT * FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");
		
		if ($result->num_rows) {
			return $result->row['layout_id'];
		} else {
			return  $this->config->get('config_layout_product');
		}
	}
	
	public function getCategories($product_id) {
		$result = $this->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
		
		return $result->rows;
	}	
	
	public function getAttributeList($attr_id){
		$attrs = $this->cache->get("attribute.group_id.$attr_id");
		if(!$attrs){
			$lang_id = $this->config->get('config_language_id');
			$result = $this->query("SELECT a.attribute_id, name FROM oc_attribute a LEFT JOIN oc_attribute_description ad ON (a.attribute_id=ad.attribute_id AND ad.language_id='$lang_id') WHERE a.attribute_group_id='$attr_id' AND a.sort_order >= 0 ORDER BY ad.name");
			$attrs = array();
			if($result->num_rows)
				foreach($result->rows as $cat)
					$attrs[$cat['attribute_id']] = $cat['name'];
			$this->cache->set("attribute.group_id.$attr_id",$attrs);
		}
		return $attrs;
	}
	
	public function getOptionList($option_id){
		$opts = $this->cache->get("option.option_list.$option_id");
		if(!$opts){
			$lang_id = $this->config->get('config_language_id');
			$result = $this->query("SELECT ovd.option_value_id, ovd.name FROM oc_option_value_description ovd JOIN oc_option_value ov ON (ovd.option_value_id=ov.option_value_id AND ov.sort_order >= 0) WHERE ovd.option_id='$option_id' AND ovd.language_id='$lang_id' ORDER BY ovd.name");
			$opts = array();
			if($result->num_rows)
				foreach($result->rows as $cat)
					$opts[$cat['option_value_id']] = $cat['name'];
			$this->cache->set("option.option_list.$option_id",$opts);
		}
		return $opts;
	}
	
	public function getTotalProducts($data = array()) {
		$sql = "SELECT COUNT(DISTINCT p.product_id) AS total FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)";

		if (!empty($data['filter_category_id'])) {
			$sql .= " LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)";			
		}
		
		if (!empty($data['filter_tag'])) {
			$sql .= " LEFT JOIN " . DB_PREFIX . "product_tag pt ON (p.product_id = pt.product_id)";			
		}
					
		$sql .= " WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";
		
		if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
			$sql .= " AND (";
								
			if (!empty($data['filter_name'])) {
				$implode = array();
				
				$words = explode(' ', $data['filter_name']);
				
				foreach ($words as $word) {
					if (!empty($data['filter_description'])) {
						$implode[] = "LCASE(pd.name) LIKE '%" . $this->db->escape(strtolower($word)) . "%' OR LCASE(pd.description) LIKE '%" . $this->db->escape(strtolower($word)) . "%'";
					} else {
						$implode[] = "LCASE(pd.name) LIKE '%" . $this->db->escape(strtolower($word)) . "%'";
					}				
				}
				
				if ($implode) {
					$sql .= " " . implode(" OR ", $implode) . "";
				}
			}
			
			if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
			}
			
			if (!empty($data['filter_tag'])) {
				$implode = array();
				
				$words = explode(' ', $data['filter_tag']);
				
				foreach ($words as $word) {
					$implode[] = "LCASE(pt.tag) LIKE '%" . $this->db->escape(strtolower($word)) . "%' AND pt.language_id = '" . (int)$this->config->get('config_language_id') . "'";
				}
				
				if ($implode) {
					$sql .= " " . implode(" OR ", $implode) . "";
				}
			}
		
			$sql .= ")";
		}
		
		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$implode_data = array();
				
				$implode_data[] = "p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
				
				$categories = $this->model_catalog_category->getCategoriesByParentId($data['filter_category_id']);
					
				foreach ($categories as $category_id) {
					$implode_data[] = "p2c.category_id = '" . (int)$category_id . "'";
				}
							
				$sql .= " AND (" . implode(' OR ', $implode_data) . ")";			
			} else {
				$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
			}
		}		
		
		if (!empty($data['filter_manufacturer_id'])) {
			$sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
		}
		
		$result = $this->query($sql);
		
		return $result->row['total'];
	}
			
	public function getTotalProductSpecials() {
		if ($this->customer->isLogged()) {
			$customer_group_id = $this->customer->getCustomerGroupId();
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}		
		
		$result = $this->query("SELECT COUNT(DISTINCT ps.product_id) AS total FROM " . DB_PREFIX . "product_special ps LEFT JOIN " . DB_PREFIX . "product p ON (ps.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND ps.customer_group_id = '" . (int)$customer_group_id . "' AND ((ps.date_start = '" . DATETIME_ZERO . "' OR ps.date_start < NOW()) AND (ps.date_end = '" . DATETIME_ZERO . "' OR ps.date_end > NOW()))");
		
		if (isset($result->row['total'])) {
			return $result->row['total'];
		} else {
			return 0;	
		}
	}	
}
