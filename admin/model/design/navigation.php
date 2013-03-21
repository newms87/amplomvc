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
            $row['links'] = $this->getNavigationgroupLinks($row['navigation_group_id']);
				$row['store_ids'] = $this->getNavigationgroupStores($row['navigation_group_id']);
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
}
