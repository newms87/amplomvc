<?php
class ModelSettingStore extends Model {
	public function addStore($data) {
		$this->query("INSERT INTO " . DB_PREFIX . "store SET name = '" . $this->db->escape($data['config_name']) . "', `url` = '" . $this->db->escape($data['config_url']) . "', `ssl` = '" . $this->db->escape($data['config_ssl']) . "'");
		
		$this->cache->delete('store');
      $this->cache->delete('template');
		
		return $this->db->getLastId();
	}
	
	public function editStore($store_id, $data) {
		$this->query("UPDATE " . DB_PREFIX . "store SET name = '" . $this->db->escape($data['config_name']) . "', `url` = '" . $this->db->escape($data['config_url']) . "', `ssl` = '" . $this->db->escape($data['config_ssl']) . "' WHERE store_id = '" . (int)$store_id . "'");
						
		$this->cache->delete('store');
      $this->cache->delete('template');
	}
	
	public function deleteStore($store_id) {
		$this->query("DELETE FROM " . DB_PREFIX . "store WHERE store_id = '" . (int)$store_id . "'");
			
		$this->cache->delete('store');
      $this->cache->delete('template');
	}	
	
   
	public function getStore($store_id) {
		$query = $this->query("SELECT DISTINCT * FROM " . DB_PREFIX . "store WHERE store_id = '" . (int)$store_id . "'");
		
		return $query->row;
	}
	
	public function getStoreName($store_id){
      $query = $this->query("SELECT value FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `key`='config_name'");
      
      if($query->num_rows){
         return $query->row['value'];
      }
      else{
         return '';
      }
   }
   public function getStoreNames(){
      $query = $this->query("SELECT store_id, value as name FROM " . DB_PREFIX . "setting WHERE `key`='config_name'");
      
      return $query->rows;
   }
   
	public function getStores($data = array()) {
		$store_data = $this->cache->get('store');
	
		if (!$store_data) {
			$query = $this->get('store', '*');

			$default_store = array(
				'store_id' => 0,
				'name' => $this->config->get('config_name'),
				'url' => HTTP_SERVER,
				'ssl' => HTTPS_SERVER,
			);
			
			$store_data = array($default_store) + $query->rows;
		
			$this->cache->set('store', $store_data);
		}
	 
		return $store_data;
	}

	public function getTotalStores() {
      	$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "store");
		
		return $query->row['total'];
	}	
	
	public function getTotalStoresByLayoutId($layout_id) {
      	$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_default_layout_id' AND `value` = '" . (int)$layout_id . "' AND store_id != '0'");
		
		return $query->row['total'];		
	}
	
	public function getTotalStoresByLanguage($language) {
      	$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_language' AND `value` = '" . $this->db->escape($language) . "' AND store_id != '0'");
		
		return $query->row['total'];		
	}
	
	public function getTotalStoresByCurrency($currency) {
      	$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_currency' AND `value` = '" . $this->db->escape($currency) . "' AND store_id != '0'");
		
		return $query->row['total'];		
	}
	
	public function getTotalStoresByCountryId($country_id) {
      	$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_country_id' AND `value` = '" . (int)$country_id . "' AND store_id != '0'");
		
		return $query->row['total'];		
	}
	
	public function getTotalStoresByZoneId($zone_id) {
      	$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_zone_id' AND `value` = '" . (int)$zone_id . "' AND store_id != '0'");
		
		return $query->row['total'];		
	}
	
	public function getTotalStoresByCustomerGroupId($customer_group_id) {
      	$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_customer_group_id' AND `value` = '" . (int)$customer_group_id . "' AND store_id != '0'");
		
		return $query->row['total'];		
	}	
	
	public function getTotalStoresByInformationId($information_id) {
      	$account_query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_account_id' AND `value` = '" . (int)$information_id . "' AND store_id != '0'");
      	
		$checkout_query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_checkout_id' AND `value` = '" . (int)$information_id . "' AND store_id != '0'");
		
		return ($account_query->row['total'] + $checkout_query->row['total']);
	}
	
	public function getTotalStoresByOrderStatusId($order_status_id) {
      	$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_order_status_id' AND `value` = '" . (int)$order_status_id . "' AND store_id != '0'");
		
		return $query->row['total'];		
	}	
}