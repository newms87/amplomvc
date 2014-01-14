<?php
class System_Model_Fraud extends Model
{
	public function addOrderFraud($data)
	{
		$data['date_added'] = $this->date->now();

		$this->insert("order_fraud", $data);
	}

	public function getOrderFraudRiskScore($order_id)
	{
		return (float)$this->queryVar("SELECT riskScore FROM `" . DB_PREFIX . "order_fraud` WHERE order_id = '" . (int)$order_id . "'");
	}
}
