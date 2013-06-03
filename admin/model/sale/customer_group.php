<?php
class Admin_Model_Sale_CustomerGroup extends Model 
{
	public function addCustomerGroup($data)
	{
		$this->query("INSERT INTO " . DB_PREFIX . "customer_group SET name = '" . $this->db->escape($data['name']) . "'");
	}
	
	public function editCustomerGroup($customer_group_id, $data)
	{
		$this->query("UPDATE " . DB_PREFIX . "customer_group SET name = '" . $this->db->escape($data['name']) . "' WHERE customer_group_id = '" . (int)$customer_group_id . "'");
	}
	
	public function deleteCustomerGroup($customer_group_id)
	{
		$this->query("DELETE FROM " . DB_PREFIX . "customer_group WHERE customer_group_id = '" . (int)$customer_group_id . "'");
		$this->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE customer_group_id = '" . (int)$customer_group_id . "'");
		$this->query("DELETE FROM " . DB_PREFIX . "product_special WHERE customer_group_id = '" . (int)$customer_group_id . "'");
		$this->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE customer_group_id = '" . (int)$customer_group_id . "'");
	}
	
	public function getCustomerGroup($customer_group_id)
	{
		$query = $this->query("SELECT DISTINCT * FROM " . DB_PREFIX . "customer_group WHERE customer_group_id = '" . (int)$customer_group_id . "'");
		
		return $query->row;
	}
	
	public function getCustomerGroups($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "customer_group";
		
		$sql .= " ORDER BY name";
			
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
			
		$query = $this->query($sql);
		
		return $query->rows;
	}
	
	public function getTotalCustomerGroups()
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_group");
		
		return $query->row['total'];
	}
}