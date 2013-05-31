<?php
class Catalog_Model_Design_Navigation extends Model 
{
	public function getNavigationGroup($name)
	{
		$query  = "SELECT ng.* FROM " . DB_PREFIX . "navigation_group ng";
		$query .= " LEFT JOIN " . DB_PREFIX . "navigation_store ns ON (ng.navigation_group_id=ns.navigation_group_id)";
		$query .= " WHERE ng.name = '" . $this->db->escape($name) . "' AND ng.status='1' AND ns.store_id='" . $this->config->get('config_store_id') . "'";
		
		$result = $this->query($query);
		
		$nav_group = $result->row;
		
		$nav_group['stores'] = $this->getNavigationGroupStores($navigation_group_id);
		
		$nav_group['links'] = $this->getNavigationGroupLinks($navigation_group_id);
		
		return $nav_group;
	}
	
	public function getNavigationLinks()
	{
		$store_id = $this->config->get("config_store_id");
		$nav_groups = $this->cache->get("navigation_groups.store.$store_id");
		
		if (!$nav_groups || true) {
			$query = "SELECT ng.* FROM " . DB_PREFIX . "navigation_group ng";
			$query .= " LEFT JOIN " . DB_PREFIX . "navigation_store ns ON (ng.navigation_group_id=ns.navigation_group_id)";
			$query .= " WHERE ng.status='1' AND ns.store_id='$store_id'";
			
			$result = $this->query($query);
			
			$nav_groups = array();
			
			foreach ($result->rows as &$group) {
				$nav_group_links = $this->getNavigationGroupLinks($group['navigation_group_id']);
				
				$parent_ref = array();
				
				foreach ($nav_group_links as $key => &$link) {
					$link['children'] = array();
					$parent_ref[$link['navigation_id']] = &$link;
					
					if ($link['parent_id']) {
						$parent_ref[$link['parent_id']]['children'][] = &$link;
						unset($nav_group_links[$key]);
					}
				}
				
				$nav_groups[$group['name']] = $nav_group_links;
			}

			$this->cache->set("navigation_groups.store.$store_id", $nav_groups);
		}
		
		return $nav_groups;
	}

	public function getNavigationGroupLinks($navigation_group_id)
	{
		$result = $this->query("SELECT * FROM " . DB_PREFIX . "navigation WHERE status='1' AND navigation_group_id='" . (int)$navigation_group_id . "' ORDER BY parent_id ASC, sort_order ASC");

		return $result->rows;
	}
}
