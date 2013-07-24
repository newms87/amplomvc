<?php
class Admin_Model_Setting_Store extends Model
{
	public function addStore($data)
	{
		$store_id = $this->insert('store', $data);
		
		$this->cache->delete('store');
		$this->cache->delete('theme');
		
		return $store_id;
	}
	
	public function editStore($store_id, $data)
	{
		$this->update('store', $data, $store_id);
						
		$this->cache->delete('store');
		$this->cache->delete('theme');
	}
	
	public function deleteStore($store_id)
	{
		$this->delete('store', $store_id);
			
		$this->cache->delete('store');
		$this->cache->delete('theme');
	}
	
	public function getStore($store_id)
	{
		return $this->queryRow("SELECT * FROM " . DB_PREFIX . "store WHERE store_id = '" . (int)$store_id . "'");
	}
	
	public function getStoreName($store_id)
	{
		return $this->queryVar("SELECT name FROM " . DB_PREFIX . "store WHERE store_id = '" . (int)$store_id . "'");
	}
	
	public function getStoreNames()
	{
		return $this->queryRows("SELECT store_id, name FROM " . DB_PREFIX . "store");
	}
	
	public function getStores($data = array(), $total = false) {
		if ($total) {
			$select = "COUNT(*) as total";
		}
		else {
			$select = "*";
		}
		
		$from = DB_PREFIX . "store s";
		
		$where = "store_id > 0";
		
		//Order By & Limit
		if (!$total) {
			$order = $this->extract_order($data);
			$limit = $this->extract_limit($data);
		} else {
			$order = '';
			$limit = '';
		}
		
		$query = "SELECT $select FROM $from WHERE $where $order $limit";
		
		$result = $this->query($query);
		
		if ($total) {
			return $result['total'];
		}
		
		return $result->rows;
	}

	public function getTotalStores()
	{
			$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "store");
		
		return $query->row['total'];
	}
	
	public function getTotalStoresByLanguage($language)
	{
			$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_language' AND `value` = '" . $this->db->escape($language) . "' AND store_id != '0'");
		
		return $query->row['total'];
	}
	
	public function getTotalStoresByCurrency($currency)
	{
			$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_currency' AND `value` = '" . $this->db->escape($currency) . "' AND store_id != '0'");
		
		return $query->row['total'];
	}
	
	public function getTotalStoresByCountryId($country_id)
	{
			$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_country_id' AND `value` = '" . (int)$country_id . "' AND store_id != '0'");
		
		return $query->row['total'];
	}
	
	public function getTotalStoresByZoneId($zone_id)
	{
			$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_zone_id' AND `value` = '" . (int)$zone_id . "' AND store_id != '0'");
		
		return $query->row['total'];
	}
	
	public function getTotalStoresByCustomerGroupId($customer_group_id)
	{
			$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_customer_group_id' AND `value` = '" . (int)$customer_group_id . "' AND store_id != '0'");
		
		return $query->row['total'];
	}
	
	public function getTotalStoresByInformationId($information_id)
	{
			$account_query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_account_id' AND `value` = '" . (int)$information_id . "' AND store_id != '0'");
			
		$checkout_query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_checkout_id' AND `value` = '" . (int)$information_id . "' AND store_id != '0'");
		
		return ($account_query->row['total'] + $checkout_query->row['total']);
	}
	
	public function getTotalStoresByOrderStatusId($order_status_id)
	{
			$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_order_complete_status_id' AND `value` = '" . (int)$order_status_id . "' AND store_id != '0'");
		
		return $query->row['total'];
	}
}