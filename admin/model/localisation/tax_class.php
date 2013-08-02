<?php
class Admin_Model_Localisation_TaxClass extends Model
{
	public function addTaxclass($data)
	{
		$this->query("INSERT INTO " . DB_PREFIX . "tax_class SET title = '" . $this->escape($data['title']) . "', description = '" . $this->escape($data['description']) . "', date_added = NOW()");
		
		$tax_class_id = $this->db->getLastId();
		
		if (isset($data['tax_rule'])) {
			foreach ($data['tax_rule'] as $tax_rule) {
				$this->query("INSERT INTO " . DB_PREFIX . "tax_rule SET tax_class_id = '" . (int)$tax_class_id . "', tax_rate_id = '" . (int)$tax_rule['tax_rate_id'] . "', based = '" . $this->escape($tax_rule['based']) . "', priority = '" . (int)$tax_rule['priority'] . "'");
			}
		}
		
		$this->cache->delete('tax_class');
	}
	
	public function editTaxClass($tax_class_id, $data)
	{
		$this->query("UPDATE " . DB_PREFIX . "tax_class SET title = '" . $this->escape($data['title']) . "', description = '" . $this->escape($data['description']) . "', date_modified = NOW() WHERE tax_class_id = '" . (int)$tax_class_id . "'");
		
		$this->query("DELETE FROM " . DB_PREFIX . "tax_rule WHERE tax_class_id = '" . (int)$tax_class_id . "'");

		if (isset($data['tax_rule'])) {
			foreach ($data['tax_rule'] as $tax_rule) {
				$this->query("INSERT INTO " . DB_PREFIX . "tax_rule SET tax_class_id = '" . (int)$tax_class_id . "', tax_rate_id = '" . (int)$tax_rule['tax_rate_id'] . "', based = '" . $this->escape($tax_rule['based']) . "', priority = '" . (int)$tax_rule['priority'] . "'");
			}
		}
		
		$this->cache->delete('tax_class');
	}
	
	public function deleteTaxClass($tax_class_id)
	{
		$this->query("DELETE FROM " . DB_PREFIX . "tax_class WHERE tax_class_id = '" . (int)$tax_class_id . "'");
		$this->query("DELETE FROM " . DB_PREFIX . "tax_rule WHERE tax_class_id = '" . (int)$tax_class_id . "'");
		
		$this->cache->delete('tax_class');
	}
	
	public function getTaxClass($tax_class_id)
	{
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "tax_class WHERE tax_class_id = '" . (int)$tax_class_id . "'");
		
		return $query->row;
	}

	public function getTaxClasses($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "tax_class ";

			$sql .= " ORDER BY title";
			
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
		} else {
			$tax_class_data = $this->cache->get('tax_class');

			if (!$tax_class_data) {
				$query = $this->query("SELECT * FROM " . DB_PREFIX . "tax_class ");
	
				$tax_class_data = $query->rows;
			
				$this->cache->set('tax_class', $tax_class_data);
			}
			
			return $tax_class_data;
		}
	}
					
	public function getTotalTaxClasses()
	{
			$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "tax_class ");
		
		return $query->row['total'];
	}
	
	public function getTaxRules($tax_class_id)
	{
			$query = $this->query("SELECT * FROM " . DB_PREFIX . "tax_rule WHERE tax_class_id = '" . (int)$tax_class_id . "'");
		
		return $query->rows;
	}
	
	public function getTotalTaxRulesByTaxRateId($tax_rate_id)
	{
			$query = $this->query("SELECT COUNT(DISTINCT tax_class_id) AS total FROM " . DB_PREFIX . "tax_rule WHERE tax_rate_id = '" . (int)$tax_rate_id . "'");
		
		return $query->row['total'];
	}
}