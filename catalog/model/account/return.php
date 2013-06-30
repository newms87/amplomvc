<?php
class Catalog_Model_Account_Return extends Model 
{
	public function addReturn($data)
	{
		$data['customer_id'] = $this->customer->getId();
		$data['return_status_id'] = $this->config->get('config_return_status_id');
		$data['date_added'] = $this->date->format();
		$data['date_modified'] = $this->date->format();
		
		$return_id = $this->insert('return', $data);
		
		return $return_id;
	}
	
	public function generateRma($data)
	{
		$rma = $data['product_id'] . '-' . $data['order_id'];
		
		$count = $this->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "return WHERE rma like '$rma%'");
		
		$rma .= '-' . $data['return_reason_id'];
		
		if ($count) {
			$rma .= "-" . ($count+1);
		}
		
		return $rma;
	}
	
	public function getReturn($return_id)
	{
		$query = "SELECT * FROM " . DB_PREFIX . "return" .
		" WHERE return_id = '" . (int)$return_id . "' AND customer_id = '" . $this->customer->getId() . "'";
		
		$return = $this->queryRow($query);
		
		if ($return) {
			$return['product'] = $this->Model_Catalog_Product->getProduct($return['product_id'], true);
			
			$this->config->loadGroup('product_return');
				
			$return_statuses = $this->config->get('product_return_statuses');
			$return_reasons = $this->config->get('product_return_reasons');
			$return_actions = $this->config->get('product_return_actions');
			
			$return['status'] = $return_statuses[$return['return_status_id']];
			$return['reason'] = $return_reasons[$return['return_reason_id']];
			$return['action'] = $return_actions[$return['return_action_id']];
		}
		
		return $return;
	}
	
	public function getReturns($data = array(), $select = '', $total = false)
	{
		//Select
		if ($total) {
			$select = "COUNT(*) as total";
		} elseif (empty($select)) {
			$select = "*";
		}
		
		//From
		$from = DB_PREFIX . "return r";
		
		//Where
		$where = "1";
		
		//Order and Limit
		if (!$total) {
			$order = $this->extract_order($data);
			$limit = $this->extract_limit($data);
		} else {
			$order = '';
			$limit = '';
		}
		
		//The Query
		$query = "SELECT $select FROM $from WHERE $where $order $limit";
		
		$result = $this->query($query);
		
		if ($total) {
			return $result->row['total'];
		}
		
		$this->config->loadGroup('product_return');
			
		$return_statuses = $this->config->get('product_return_statuses');
		$return_reasons = $this->config->get('product_return_reasons');
		$return_actions = $this->config->get('product_return_actions');
		
		foreach ($result->rows as &$row) {
			$row['status'] = $return_statuses[$row['return_status_id']];
			$row['reason'] = $return_reasons[$row['return_reason_id']];
			$row['action'] = $return_actions[$row['return_action_id']];
		}
		
		return $result->rows;
	}
			
	public function getTotalReturns()
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "return`WHERE customer_id = '" . $this->customer->getId() . "'");
		
		return $query->row['total'];
	}
	
	public function getReturnHistories($return_id)
	{
		$query = $this->query("SELECT rh.date_added, rs.name AS status, rh.comment, rh.notify FROM " . DB_PREFIX . "return_history rh LEFT JOIN " . DB_PREFIX . "return_status rs ON rh.return_status_id = rs.return_status_id WHERE rh.return_id = '" . (int)$return_id . "' AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY rh.date_added ASC");

		return $query->rows;
	}
}