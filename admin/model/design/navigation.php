<?php
class ModelDesignNavigation extends Model {
	public function addNavigationGroup($data) {
		$navigation_group_id = $this->insert("navigation_group", $data);
		
		//Add Stores
		foreach($data['store_ids'] as $store_id){
			$store_data = array(
				'navigation_group_id' => $navigation_group_id,
				'store_id' => $store_id
			);
			
			$this->insert("navigation_store", $store_data);
		}
		
		//Add Links
		$parent = array();
		$sort_index = 0;
		
		html_dump($data['links'], 'links');
		foreach($data['links'] as $link_id => $link){
			$link['navigation_group_id'] = $navigation_group_id;
			$link['sort_order'] = $sort_index++;
			
			if($link['parent_id']){
				if(!isset($parent[$link['parent_id']])){
					$msg = "ModelDesignNavigation::addNavigationGroup(): There was an error resolving the parent_id, $link[parent_id]!";
					trigger_error($msg);
					$this->mail->send_error_email($msg);
					
					$this->add_message('error', "There was an error saving Navigation group to the database! The Web Admin has been notified. Please try again later");
				}
				else{
					$link['parent_id'] = $parent[$link['parent_id']];
				}
			}
			
			$nav_id = $this->insert("navigation", $link);
			
			$parent[$link_id] = $nav_id;
		}
		
		$this->cache->delete('navigation');
	}
	
	public function editNavigationGroup($navigation_group_id, $data) {
		$this->update("navigation_group", $data, $navigation_group_id);
		
		//Update Stores
		if(isset($data['store_ids'])){
			$this->delete("navigation_store", array("navigation_group_id" => $navigation_group_id));
			
			foreach($data['store_ids'] as $store_id){
				$store_data = array(
					'navigation_group_id' => $navigation_group_id,
					'store_id' => $store_id
				);
				
				$this->insert("navigation_store", $store_data);
			}
		}
		
		
		//Update Links
		if(isset($data['links'])){
			$this->delete("navigation", array("navigation_group_id" => $navigation_group_id));
			
			$parent = array();
			$sort_index = 0;
			
			foreach($data['links'] as $link_id => $link){
				$link['navigation_group_id'] = $navigation_group_id;
				$link['sort_order'] = $sort_index++;

				if($link['parent_id']){
					if(!isset($parent[$link['parent_id']])){
						$msg = "ModelDesignNavigation::addNavigationGroup(): There was an error resolving the parent_id!";
						trigger_error($msg);
						$this->mail->send_error_email($msg);
						
						$this->add_message('error', "There was an error saving Navigation group to the database! The Web Admin has been notified. Please try again later");
					}
					else{
						$link['parent_id'] = $parent[$link['parent_id']];
					}
				}
				
				$nav_id = $this->insert("navigation", $link);
				
				$parent[$link_id] = $nav_id;
			}
		}
		
		$this->cache->delete('navigation');
	}
	
   public function deleteNavigationGroup($navigation_group_id) {
   	$this->delete("navigation_group", $navigation_group_id);
		
		$this->delete("navigation_store", array("navigation_group_id" => $navigation_group_id));
		$this->delete("navigation", array("navigation_group_id" => $navigation_group_id));
		
		$this->cache->delete('navigation');
	}
	
	public function addNavigationLink($navigation_group_id, $link){
		$link['navigation_group_id'] = $navigation_group_id;
		
		$this->insert("navigation", $link);
		
		$this->cache->delete('navigation');
	}
	
	public function deleteNavigationLink($navigation_id){
		$this->delete("navigation", $navigation_id);
		
		$this->cache->delete('navigation');
	}
	
	public function getNavigationGroup($navigation_group_id) {
		$query = $this->get("navigation_group", '*', $navigation_group_id);
		
		$nav_group = $query->row;
		
		$nav_group['store_ids'] = $this->getNavigationGroupStores($navigation_group_id);
		
		$nav_group['links'] = $this->getNavigationGroupLinks($navigation_group_id);
		
		return $nav_group;
	}
	
	public function getNavigationGroups($data = array(), $select = '*', $total = false) {
		//Select
		if($total){
         $select = 'COUNT(*) as total';
      }
      elseif(!$select){
         $select = '*';
      }
      
		//Join Tables
		$join = array();
		
		if(isset($data['store_id'])){
			$join['LEFT JOIN']['navigation_store ns'] = "ns.navigation_group_id = ng.navigation_group_id";
		}
		
		
		//WHERE statement
      $where = array();
      
      if(isset($data['name'])){
         $where['AND'][] = "ng.name like '%" . $this->db->escape($data['name']) . "%'";
      }
      
		if(isset($data['store_id'])){
         $where['AND'][] = "ns.store_id = '" . (int)$data['store_id'] . "'";
      }
      
      if(isset($data['status'])){
         $where['AND'][] = "ng.status = '" . ($data['status'] ? 1 : 0) . "'";
      }
      
      $query = $this->execute('navigation_group ng', $select, $join, $where, $data);
      
      if($total){
         return $query->row['total'];
      }
      else{
         foreach($query->rows as $key => &$row){
            $row['links'] = $this->getNavigationGroupLinks($row['navigation_group_id']);
				$row['store_ids'] = $this->getNavigationGroupStores($row['navigation_group_id']);
         }
         
         return $query->rows;
      }
	}
	
	public function getNavigationLinks() {
		$nav_groups = $this->cache->get('navigation_groups.admin');
		
		if(!$nav_groups){
			$query = $this->query("SELECT ng.* FROM " . DB_PREFIX . "navigation_group ng" . 
				" LEFT JOIN " . DB_PREFIX . "navigation_store ns ON (ng.navigation_group_id=ns.navigation_group_id)" .
				" WHERE ng.status='1' AND ns.store_id='-1'");
			
			$nav_groups = array();
			
			foreach($query->rows as &$group){
				$nav_group_links = $this->getNavigationGroupLinks($group['navigation_group_id']);
				
				$parent_ref = array();
				
				foreach($nav_group_links as $key => &$link){
					$link['children'] = array();
					$parent_ref[$link['navigation_id']] = &$link;
					
					if($link['parent_id']){
						$parent_ref[$link['parent_id']]['children'][] = &$link;
						unset($nav_group_links[$key]);
					}
				}
				
				$nav_groups[$group['name']] = $nav_group_links;
			}

			$this->cache->set('navigation_groups.admin', $nav_groups);
		}
		
		return $nav_groups;
	}
	
	public function getNavigationGroupLinks($navigation_group_id){
		$query = $this->get("navigation", '*', array("navigation_group_id" => $navigation_group_id));
		
		return $query->rows;
	}
	
	public function getNavigationGroupStores($navigation_group_id){
		$query = $this->get("navigation_store", '*', array("navigation_group_id" => $navigation_group_id));
		
		$stores = array();
		
		foreach($query->rows as $row){
			$stores[] = $row['store_id'];
		}
		
		return $stores;
	}
	
	public function getTotalNavigationGroups($data) {
   	return $this->getNavigationGroups($data, '', true);
	}

	public function reset_admin_navigation_group(){
		$links = array(
			'home' => array(
				'display_name'	=> '',
				'name'			=> 'home',
				'title'			=> '',
				'href'			=> 'common/home',
				'query'			=> '',
				'is_route'		=> 1,
				'parent_id'		=> '0',
				'sort_order'	=> 1,
				'status'			=> 1,
			),
	
			'magazine_content' => array(
				'display_name'	=> '',
				'name'			=> 'magazine_content',
				'title'			=> '',
				'href'			=> '',
				'query'			=> '',
				'is_route'		=> 0,
				'parent_id'		=> '0',
				'sort_order'	=> 2,
				'status'			=> 1,
			),
	
				'articles' => array(
					'display_name'	=> '',
					'name'			=> 'articles',
					'title'			=> '',
					'href'			=> 'cms/article',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'magazine_content',
					'sort_order'	=> 3,
					'status'			=> 1,
				),
	
				'rss_articles' => array(
					'display_name'	=> '',
					'name'			=> 'rss_articles',
					'title'			=> '',
					'href'			=> 'module/rss_article',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'magazine_content',
					'sort_order'	=> 4,
					'status'			=> 1,
				),
	
				'categories' => array(
					'display_name'	=> '',
					'name'			=> 'categories',
					'title'			=> '',
					'href'			=> 'cms/category',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'magazine_content',
					'sort_order'	=> 5,
					'status'			=> 1,
				),
	
			'flashsales' => array(
				'display_name'	=> '',
				'name'			=> 'flashsales',
				'title'			=> '',
				'href'			=> '',
				'query'			=> '',
				'is_route'		=> 0,
				'parent_id'		=> '0',
				'sort_order'	=> 6,
				'status'			=> 1,
			),
	
				'new_flashsales' => array(
					'display_name'	=> '',
					'name'			=> 'new_flashsales',
					'title'			=> '',
					'href'			=> 'catalog/flashsale/insert',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'flashsales',
					'sort_order'	=> 7,
					'status'			=> 1,
				),
	
				'flashsales_link' => array(
					'display_name'	=> '',
					'name'			=> 'flashsales_link',
					'title'			=> '',
					'href'			=> 'catalog/flashsale',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'flashsales',
					'sort_order'	=> 8,
					'status'			=> 1,
				),
	
				'featured_flashsales' => array(
					'display_name'	=> '',
					'name'			=> 'featured_flashsales',
					'title'			=> '',
					'href'			=> 'module/featured_flashsale',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'flashsales',
					'sort_order'	=> 9,
					'status'			=> 1,
				),
	
				'flashsale_sidebar' => array(
					'display_name'	=> '',
					'name'			=> 'flashsale_sidebar',
					'title'			=> '',
					'href'			=> 'module/flashsale_sidebar',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'flashsales',
					'sort_order'	=> 10,
					'status'			=> 1,
				),
	
			'content' => array(
				'display_name'	=> '',
				'name'			=> 'content',
				'title'			=> '',
				'href'			=> '',
				'query'			=> '',
				'is_route'		=> 0,
				'parent_id'		=> '0',
				'sort_order'	=> 11,
				'status'			=> 1,
			),
	
				'blocks' => array(
					'display_name'	=> '',
					'name'			=> 'blocks',
					'title'			=> '',
					'href'			=> 'block/block',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'content',
					'sort_order'	=> 12,
					'status'			=> 1,
				),
	
				'featured_products' => array(
					'display_name'	=> '',
					'name'			=> 'featured_products',
					'title'			=> '',
					'href'			=> 'module/featured',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'content',
					'sort_order'	=> 13,
					'status'			=> 1,
				),
	
				'leaderboard' => array(
					'display_name'	=> '',
					'name'			=> 'leaderboard',
					'title'			=> '',
					'href'			=> 'module/page_headers',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'content',
					'sort_order'	=> 14,
					'status'			=> 1,
				),
	
				'bestsellers_list' => array(
					'display_name'	=> '',
					'name'			=> 'bestsellers_list',
					'title'			=> '',
					'href'			=> 'module/bestseller',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'content',
					'sort_order'	=> 15,
					'status'			=> 1,
				),
	
				'newsletter' => array(
					'display_name'	=> '',
					'name'			=> 'newsletter',
					'title'			=> '',
					'href'			=> 'mail/newsletter',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'content',
					'sort_order'	=> 16,
					'status'			=> 1,
				),
	
				'featured_carousel' => array(
					'display_name'	=> '',
					'name'			=> 'featured_carousel',
					'title'			=> '',
					'href'			=> 'module/featured_carousel',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'content',
					'sort_order'	=> 17,
					'status'			=> 1,
				),
	
			'catalog' => array(
				'display_name'	=> '',
				'name'			=> 'catalog',
				'title'			=> '',
				'href'			=> '',
				'query'			=> '',
				'is_route'		=> 0,
				'parent_id'		=> '0',
				'sort_order'	=> 18,
				'status'			=> 1,
			),
	
				'attributes' => array(
					'display_name'	=> '',
					'name'			=> 'attributes',
					'title'			=> '',
					'href'			=> '',
					'query'			=> '',
					'is_route'		=> 0,
					'parent_id'		=> 'catalog',
					'sort_order'	=> 19,
					'status'			=> 1,
				),
	
					'attributes_link' => array(
						'display_name'	=> '',
						'name'			=> 'attributes_link',
						'title'			=> '',
						'href'			=> 'catalog/attribute',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'attributes',
						'sort_order'	=> 20,
						'status'			=> 1,
					),
	
					'attribute_groups' => array(
						'display_name'	=> '',
						'name'			=> 'attribute_groups',
						'title'			=> '',
						'href'			=> 'catalog/attribute_group',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'attributes',
						'sort_order'	=> 21,
						'status'			=> 1,
					),
	
				'options' => array(
					'display_name'	=> '',
					'name'			=> 'options',
					'title'			=> '',
					'href'			=> 'catalog/option',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'catalog',
					'sort_order'	=> 22,
					'status'			=> 1,
				),
	
				'categories' => array(
					'display_name'	=> '',
					'name'			=> 'categories',
					'title'			=> '',
					'href'			=> 'catalog/category',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'catalog',
					'sort_order'	=> 23,
					'status'			=> 1,
				),
	
				'collections' => array(
					'display_name'	=> '',
					'name'			=> 'collections',
					'title'			=> '',
					'href'			=> 'catalog/collection',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'catalog',
					'sort_order'	=> 24,
					'status'			=> 1,
				),
	
				'products' => array(
					'display_name'	=> '',
					'name'			=> 'products',
					'title'			=> '',
					'href'			=> 'catalog/product',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'catalog',
					'sort_order'	=> 25,
					'status'			=> 1,
				),
	
				'designers' => array(
					'display_name'	=> '',
					'name'			=> 'designers',
					'title'			=> '',
					'href'			=> 'catalog/manufacturer',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'catalog',
					'sort_order'	=> 26,
					'status'			=> 1,
				),
	
				'downloads' => array(
					'display_name'	=> '',
					'name'			=> 'downloads',
					'title'			=> '',
					'href'			=> 'catalog/download',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'catalog',
					'sort_order'	=> 27,
					'status'			=> 1,
				),
	
				'reviews' => array(
					'display_name'	=> '',
					'name'			=> 'reviews',
					'title'			=> '',
					'href'			=> 'catalog/review',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'catalog',
					'sort_order'	=> 28,
					'status'			=> 1,
				),
	
				'information' => array(
					'display_name'	=> '',
					'name'			=> 'information',
					'title'			=> '',
					'href'			=> 'catalog/information',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'catalog',
					'sort_order'	=> 29,
					'status'			=> 1,
				),
	
			'sales' => array(
				'display_name'	=> '',
				'name'			=> 'sales',
				'title'			=> '',
				'href'			=> '',
				'query'			=> '',
				'is_route'		=> 0,
				'parent_id'		=> '0',
				'sort_order'	=> 30,
				'status'			=> 1,
			),
	
				'affiliates' => array(
					'display_name'	=> '',
					'name'			=> 'affiliates',
					'title'			=> '',
					'href'			=> 'sale/affiliate',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'sales',
					'sort_order'	=> 31,
					'status'			=> 1,
				),
	
				'coupons' => array(
					'display_name'	=> '',
					'name'			=> 'coupons',
					'title'			=> '',
					'href'			=> 'sale/coupon',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'sales',
					'sort_order'	=> 32,
					'status'			=> 1,
				),
	
				'customers' => array(
					'display_name'	=> '',
					'name'			=> 'customers',
					'title'			=> '',
					'href'			=> '',
					'query'			=> '',
					'is_route'		=> 0,
					'parent_id'		=> 'sales',
					'sort_order'	=> 33,
					'status'			=> 1,
				),
	
					'customers_link' => array(
						'display_name'	=> '',
						'name'			=> 'customers_link',
						'title'			=> '',
						'href'			=> 'sale/customer',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'customers',
						'sort_order'	=> 34,
						'status'			=> 1,
					),
	
					'customer_groups' => array(
						'display_name'	=> '',
						'name'			=> 'customer_groups',
						'title'			=> '',
						'href'			=> 'sale/customer_group',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'customers',
						'sort_order'	=> 35,
						'status'			=> 1,
					),
	
					'ip_blacklist' => array(
						'display_name'	=> '',
						'name'			=> 'ip_blacklist',
						'title'			=> '',
						'href'			=> 'sale/customer_blacklist',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'customers',
						'sort_order'	=> 36,
						'status'			=> 1,
					),
	
				'orders' => array(
					'display_name'	=> '',
					'name'			=> 'orders',
					'title'			=> '',
					'href'			=> 'sale/order',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'sales',
					'sort_order'	=> 37,
					'status'			=> 1,
				),
	
				'gift_vouchers' => array(
					'display_name'	=> '',
					'name'			=> 'gift_vouchers',
					'title'			=> '',
					'href'			=> '',
					'query'			=> '',
					'is_route'		=> 0,
					'parent_id'		=> 'sales',
					'sort_order'	=> 38,
					'status'			=> 1,
				),
	
					'voucher_themes' => array(
						'display_name'	=> '',
						'name'			=> 'voucher_themes',
						'title'			=> '',
						'href'			=> 'sale/voucher_theme',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'gift_vouchers',
						'sort_order'	=> 39,
						'status'			=> 1,
					),
	
					'gift_vouchers_link' => array(
						'display_name'	=> '',
						'name'			=> 'gift_vouchers_link',
						'title'			=> '',
						'href'			=> 'sale/voucher',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'gift_vouchers',
						'sort_order'	=> 40,
						'status'			=> 1,
					),
	
				'returns' => array(
					'display_name'	=> '',
					'name'			=> 'returns',
					'title'			=> '',
					'href'			=> 'sale/return',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'sales',
					'sort_order'	=> 41,
					'status'			=> 1,
				),
	
			'extensions' => array(
				'display_name'	=> '',
				'name'			=> 'extensions',
				'title'			=> '',
				'href'			=> '',
				'query'			=> '',
				'is_route'		=> 0,
				'parent_id'		=> '0',
				'sort_order'	=> 42,
				'status'			=> 1,
			),
	
				'plugins' => array(
					'display_name'	=> '',
					'name'			=> 'plugins',
					'title'			=> '',
					'href'			=> 'extension/plugin',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'extensions',
					'sort_order'	=> 43,
					'status'			=> 1,
				),
	
				'payments' => array(
					'display_name'	=> '',
					'name'			=> 'payments',
					'title'			=> '',
					'href'			=> 'extension/payment',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'extensions',
					'sort_order'	=> 44,
					'status'			=> 1,
				),
	
				'modules' => array(
					'display_name'	=> '',
					'name'			=> 'modules',
					'title'			=> '',
					'href'			=> 'extension/module',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'extensions',
					'sort_order'	=> 45,
					'status'			=> 1,
				),
	
				'product_feeds' => array(
					'display_name'	=> '',
					'name'			=> 'product_feeds',
					'title'			=> '',
					'href'			=> 'extension/feed',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'extensions',
					'sort_order'	=> 46,
					'status'			=> 1,
				),
	
				'order_totals' => array(
					'display_name'	=> '',
					'name'			=> 'order_totals',
					'title'			=> '',
					'href'			=> 'extension/total',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'extensions',
					'sort_order'	=> 47,
					'status'			=> 1,
				),
	
				'shipping' => array(
					'display_name'	=> '',
					'name'			=> 'shipping',
					'title'			=> '',
					'href'			=> 'extension/shipping',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'extensions',
					'sort_order'	=> 48,
					'status'			=> 1,
				),
	
			'users' => array(
				'display_name'	=> '',
				'name'			=> 'users',
				'title'			=> '',
				'href'			=> '',
				'query'			=> '',
				'is_route'		=> 0,
				'parent_id'		=> '0',
				'sort_order'	=> 49,
				'status'			=> 1,
			),
	
				'users_link' => array(
					'display_name'	=> '',
					'name'			=> 'users_link',
					'title'			=> '',
					'href'			=> 'user/user',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'users',
					'sort_order'	=> 50,
					'status'			=> 1,
				),
	
				'user_groups' => array(
					'display_name'	=> '',
					'name'			=> 'user_groups',
					'title'			=> '',
					'href'			=> 'user/user_permission',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'users',
					'sort_order'	=> 51,
					'status'			=> 1,
				),
	
			'reports' => array(
				'display_name'	=> '',
				'name'			=> 'reports',
				'title'			=> '',
				'href'			=> '',
				'query'			=> '',
				'is_route'		=> 0,
				'parent_id'		=> '0',
				'sort_order'	=> 52,
				'status'			=> 1,
			),
	
				'affiliates' => array(
					'display_name'	=> '',
					'name'			=> 'affiliates',
					'title'			=> '',
					'href'			=> '',
					'query'			=> '',
					'is_route'		=> 0,
					'parent_id'		=> 'reports',
					'sort_order'	=> 53,
					'status'			=> 1,
				),
	
					'commission' => array(
						'display_name'	=> '',
						'name'			=> 'commission',
						'title'			=> '',
						'href'			=> 'report/affiliate_commission',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'affiliates',
						'sort_order'	=> 54,
						'status'			=> 1,
					),
	
				'affiliates_customers' => array(
					'display_name'	=> '',
					'name'			=> 'affiliates_customers',
					'title'			=> '',
					'href'			=> '',
					'query'			=> '',
					'is_route'		=> 0,
					'parent_id'		=> 'reports',
					'sort_order'	=> 55,
					'status'			=> 1,
				),
	
					'credit' => array(
						'display_name'	=> '',
						'name'			=> 'credit',
						'title'			=> '',
						'href'			=> 'report/customer_credit',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'customers',
						'sort_order'	=> 56,
						'status'			=> 1,
					),
	
					'reward_points' => array(
						'display_name'	=> '',
						'name'			=> 'reward_points',
						'title'			=> '',
						'href'			=> 'report/customer_reward',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'customers',
						'sort_order'	=> 57,
						'status'			=> 1,
					),
	
					'affiliates_customer_orders' => array(
						'display_name'	=> '',
						'name'			=> 'affiliates_customer_orders',
						'title'			=> '',
						'href'			=> 'report/customer_order',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'customers',
						'sort_order'	=> 58,
						'status'			=> 1,
					),
	
				'affiliates_products' => array(
					'display_name'	=> '',
					'name'			=> 'affiliates_products',
					'title'			=> '',
					'href'			=> '',
					'query'			=> '',
					'is_route'		=> 0,
					'parent_id'		=> 'reports',
					'sort_order'	=> 59,
					'status'			=> 1,
				),
	
					'purchased' => array(
						'display_name'	=> '',
						'name'			=> 'purchased',
						'title'			=> '',
						'href'			=> 'report/product_purchased',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'products',
						'sort_order'	=> 60,
						'status'			=> 1,
					),
	
					'viewed' => array(
						'display_name'	=> '',
						'name'			=> 'viewed',
						'title'			=> '',
						'href'			=> 'report/product_viewed',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'products',
						'sort_order'	=> 61,
						'status'			=> 1,
					),
	
				'sales' => array(
					'display_name'	=> '',
					'name'			=> 'sales',
					'title'			=> '',
					'href'			=> '',
					'query'			=> '',
					'is_route'		=> 0,
					'parent_id'		=> 'reports',
					'sort_order'	=> 62,
					'status'			=> 1,
				),
	
					'orders' => array(
						'display_name'	=> '',
						'name'			=> 'orders',
						'title'			=> '',
						'href'			=> 'report/sale_order',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'sales',
						'sort_order'	=> 63,
						'status'			=> 1,
					),
	
					'tax' => array(
						'display_name'	=> '',
						'name'			=> 'tax',
						'title'			=> '',
						'href'			=> 'report/sale_tax',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'sales',
						'sort_order'	=> 64,
						'status'			=> 1,
					),
	
					'coupons' => array(
						'display_name'	=> '',
						'name'			=> 'coupons',
						'title'			=> '',
						'href'			=> 'report/sale_coupon',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'sales',
						'sort_order'	=> 65,
						'status'			=> 1,
					),
	
					'shipping' => array(
						'display_name'	=> '',
						'name'			=> 'shipping',
						'title'			=> '',
						'href'			=> 'report/sale_shipping',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'sales',
						'sort_order'	=> 66,
						'status'			=> 1,
					),
	
					'returns' => array(
						'display_name'	=> '',
						'name'			=> 'returns',
						'title'			=> '',
						'href'			=> 'report/sale_return',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'sales',
						'sort_order'	=> 67,
						'status'			=> 1,
					),
	
			'system' => array(
				'display_name'	=> '',
				'name'			=> 'system',
				'title'			=> '',
				'href'			=> '',
				'query'			=> '',
				'is_route'		=> 0,
				'parent_id'		=> '0',
				'sort_order'	=> 68,
				'status'			=> 1,
			),
	
				'settings' => array(
					'display_name'	=> '',
					'name'			=> 'settings',
					'title'			=> '',
					'href'			=> 'setting/store',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'system',
					'sort_order'	=> 69,
					'status'			=> 1,
				),
	
				'mail' => array(
					'display_name'	=> '',
					'name'			=> 'mail',
					'title'			=> '',
					'href'			=> '',
					'query'			=> '',
					'is_route'		=> 0,
					'parent_id'		=> 'system',
					'sort_order'	=> 70,
					'status'			=> 1,
				),
	
					'send_email' => array(
						'display_name'	=> '',
						'name'			=> 'send_email',
						'title'			=> '',
						'href'			=> 'mail/send_email',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'mail',
						'sort_order'	=> 71,
						'status'			=> 1,
					),
	
					'mail_messages' => array(
						'display_name'	=> '',
						'name'			=> 'mail_messages',
						'title'			=> '',
						'href'			=> 'mail/messages',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'mail',
						'sort_order'	=> 72,
						'status'			=> 1,
					),
	
				'url_alias' => array(
					'display_name'	=> '',
					'name'			=> 'url_alias',
					'title'			=> '',
					'href'			=> 'setting/url_alias',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'system',
					'sort_order'	=> 73,
					'status'			=> 1,
				),
	
				'db_rules' => array(
					'display_name'	=> '',
					'name'			=> 'db_rules',
					'title'			=> '',
					'href'			=> 'setting/db_rules',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'system',
					'sort_order'	=> 74,
					'status'			=> 1,
				),
	
				'cron' => array(
					'display_name'	=> '',
					'name'			=> 'cron',
					'title'			=> '',
					'href'			=> 'module/cron',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'system',
					'sort_order'	=> 75,
					'status'			=> 1,
				),
	
				'design' => array(
					'display_name'	=> '',
					'name'			=> 'design',
					'title'			=> '',
					'href'			=> '',
					'query'			=> '',
					'is_route'		=> 0,
					'parent_id'		=> 'system',
					'sort_order'	=> 76,
					'status'			=> 1,
				),
	
					'banners' => array(
						'display_name'	=> '',
						'name'			=> 'banners',
						'title'			=> '',
						'href'			=> 'design/banner',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'design',
						'sort_order'	=> 77,
						'status'			=> 1,
					),
	
					'navigation' => array(
						'display_name'	=> '',
						'name'			=> 'navigation',
						'title'			=> '',
						'href'			=> 'design/navigation',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'design',
						'sort_order'	=> 78,
						'status'			=> 1,
					),
	
					'layouts' => array(
						'display_name'	=> '',
						'name'			=> 'layouts',
						'title'			=> '',
						'href'			=> 'design/layout',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'design',
						'sort_order'	=> 79,
						'status'			=> 1,
					),
	
				'backup__restore' => array(
					'display_name'	=> '',
					'name'			=> 'backup__restore',
					'title'			=> '',
					'href'			=> 'tool/backup',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'system',
					'sort_order'	=> 80,
					'status'			=> 1,
				),
	
				'system_tools' => array(
					'display_name'	=> '',
					'name'			=> 'system_tools',
					'title'			=> '',
					'href'			=> 'tool/tool',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'system',
					'sort_order'	=> 81,
					'status'			=> 1,
				),
	
				'error_logs' => array(
					'display_name'	=> '',
					'name'			=> 'error_logs',
					'title'			=> '',
					'href'			=> 'tool/error_log',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'system',
					'sort_order'	=> 82,
					'status'			=> 1,
				),
	
				'localisation' => array(
					'display_name'	=> '',
					'name'			=> 'localisation',
					'title'			=> '',
					'href'			=> '',
					'query'			=> '',
					'is_route'		=> 0,
					'parent_id'		=> 'system',
					'sort_order'	=> 83,
					'status'			=> 1,
				),
	
					'currencies' => array(
						'display_name'	=> '',
						'name'			=> 'currencies',
						'title'			=> '',
						'href'			=> 'localisation/currency',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'localisation',
						'sort_order'	=> 84,
						'status'			=> 1,
					),
	
					'languages' => array(
						'display_name'	=> '',
						'name'			=> 'languages',
						'title'			=> '',
						'href'			=> 'localisation/language',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'localisation',
						'sort_order'	=> 85,
						'status'			=> 1,
					),
	
					'returns' => array(
						'display_name'	=> '',
						'name'			=> 'returns',
						'title'			=> '',
						'href'			=> '',
						'query'			=> '',
						'is_route'		=> 0,
						'parent_id'		=> 'localisation',
						'sort_order'	=> 86,
						'status'			=> 1,
					),
	
						'return_reasons' => array(
							'display_name'	=> '',
							'name'			=> 'return_reasons',
							'title'			=> '',
							'href'			=> 'localisation/return_reason',
							'query'			=> '',
							'is_route'		=> 1,
							'parent_id'		=> 'returns',
							'sort_order'	=> 87,
							'status'			=> 1,
						),
	
						'return_actions' => array(
							'display_name'	=> '',
							'name'			=> 'return_actions',
							'title'			=> '',
							'href'			=> 'localisation/return_action',
							'query'			=> '',
							'is_route'		=> 1,
							'parent_id'		=> 'returns',
							'sort_order'	=> 88,
							'status'			=> 1,
						),
	
						'return_statuses' => array(
							'display_name'	=> '',
							'name'			=> 'return_statuses',
							'title'			=> '',
							'href'			=> 'localisation/return_status',
							'query'			=> '',
							'is_route'		=> 1,
							'parent_id'		=> 'returns',
							'sort_order'	=> 89,
							'status'			=> 1,
						),
	
					'taxes' => array(
						'display_name'	=> '',
						'name'			=> 'taxes',
						'title'			=> '',
						'href'			=> '',
						'query'			=> '',
						'is_route'		=> 0,
						'parent_id'		=> 'localisation',
						'sort_order'	=> 90,
						'status'			=> 1,
					),
	
						'tax_classes' => array(
							'display_name'	=> '',
							'name'			=> 'tax_classes',
							'title'			=> '',
							'href'			=> 'localisation/tax_class',
							'query'			=> '',
							'is_route'		=> 1,
							'parent_id'		=> 'taxes',
							'sort_order'	=> 91,
							'status'			=> 1,
						),
	
						'tax_rates' => array(
							'display_name'	=> '',
							'name'			=> 'tax_rates',
							'title'			=> '',
							'href'			=> 'localisation/tax_rate',
							'query'			=> '',
							'is_route'		=> 1,
							'parent_id'		=> 'taxes',
							'sort_order'	=> 92,
							'status'			=> 1,
						),
	
					'zones' => array(
						'display_name'	=> '',
						'name'			=> 'zones',
						'title'			=> '',
						'href'			=> 'localisation/zone',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'localisation',
						'sort_order'	=> 93,
						'status'			=> 1,
					),
	
					'geo_zones' => array(
						'display_name'	=> '',
						'name'			=> 'geo_zones',
						'title'			=> '',
						'href'			=> 'localisation/geo_zone',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'localisation',
						'sort_order'	=> 94,
						'status'			=> 1,
					),
	
					'stock_statuses' => array(
						'display_name'	=> '',
						'name'			=> 'stock_statuses',
						'title'			=> '',
						'href'			=> 'localisation/stock_status',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'localisation',
						'sort_order'	=> 95,
						'status'			=> 1,
					),
	
					'order_statuses' => array(
						'display_name'	=> '',
						'name'			=> 'order_statuses',
						'title'			=> '',
						'href'			=> 'localisation/order_status',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'localisation',
						'sort_order'	=> 96,
						'status'			=> 1,
					),
	
					'length_classes' => array(
						'display_name'	=> '',
						'name'			=> 'length_classes',
						'title'			=> '',
						'href'			=> 'localisation/length_class',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'localisation',
						'sort_order'	=> 97,
						'status'			=> 1,
					),
	
					'weight_classes' => array(
						'display_name'	=> '',
						'name'			=> 'weight_classes',
						'title'			=> '',
						'href'			=> 'localisation/weight_class',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'localisation',
						'sort_order'	=> 98,
						'status'			=> 1,
					),
	
				'countries' => array(
					'display_name'	=> '',
					'name'			=> 'countries',
					'title'			=> '',
					'href'			=> 'localisation/country',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'system',
					'sort_order'	=> 99,
					'status'			=> 1,
				),
	
			'help' => array(
				'display_name'	=> '',
				'name'			=> 'help',
				'title'			=> '',
				'href'			=> '',
				'query'			=> '',
				'is_route'		=> 0,
				'parent_id'		=> '0',
				'sort_order'	=> 100,
				'status'			=> 1,
			),
	
				'documentation' => array(
					'display_name'	=> '',
					'name'			=> 'documentation',
					'title'			=> '',
					'href'			=> 'help/documentation',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'help',
					'sort_order'	=> 101,
					'status'			=> 1,
				),
		);
		
		$result = $this->query("SELECT navigation_group_id FROM " . DB_PREFIX . "navigation_group WHERE name = 'admin'");
		
		if($result->num_rows){
			$this->deleteNavigationGroup($result->row['navigation_group_id']);
		}
		
		$data = array(
			'name' => 'admin',
			'status' => 1,
			'store_ids' => -1,
			'links' => $links 
		);
		
		$this->addNavigationGroup($data);
	}
}
