<?php
class App_Model_Account_Return extends Model
{
	public function addReturn($data)
	{
		$data['customer_id']      = $this->customer->getId();
		$data['return_status_id'] = option('config_return_status_id');
		$data['date_added']       = $this->date->now();
		$data['date_modified']    = $this->date->now();

		$return_id = $this->insert('return', $data);

		return $return_id;
	}

	public function generateRma($data)
	{
		$rma = $data['product_id'] . '-' . $data['order_id'];

		$count = $this->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "return WHERE rma like '$rma%'");

		$rma .= '-' . $data['return_reason_id'];

		if ($count) {
			$rma .= "-" . ($count + 1);
		}

		return $rma;
	}

	public function getReturn($return_id)
	{
		$query = "SELECT * FROM " . DB_PREFIX . "return" .
			" WHERE return_id = '" . (int)$return_id . "' AND customer_id = '" . $this->customer->getId() . "'";

		$return = $this->queryRow($query);

		if ($return) {
			$return['product'] = $this->Model_Catalog_Product->getProduct($return['product_id']);

			$return['status'] = $this->order->getReturnStatus($return['return_status_id']);
			$return['reason'] = $this->order->getReturnReason($return['return_reason_id']);
			$return['action'] = $this->order->getReturnAction($return['return_action_id']);
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
			$order = $this->extractOrder($data);
			$limit = $this->extractLimit($data);
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

		foreach ($result->rows as &$row) {
			$row['status'] = $this->order->getReturnStatus($row['return_status_id']);
			$row['reason'] = $this->order->getReturnReason($row['return_reason_id']);
			$row['action'] = $this->order->getReturnAction($row['return_action_id']);
		}

		return $result->rows;
	}

	public function getTotalReturns()
	{
		return $this->queryVar("SELECT COUNT(*) FROM `" . DB_PREFIX . "return` WHERE customer_id = '" . (int)$this->customer->getId() . "'");
	}

	public function getReturnHistories($return_id)
	{
		return $this->queryRows("SELECT * FROM " . DB_PREFIX . "return_history WHERE return_id = " . (int)$return_id . " ORDER BY date_added ASC");
	}
}
