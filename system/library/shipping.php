<?php

class Shipping extends Library
{
	const PENDING  = 'Pending';
	const SENT     = 'Sent';
	const COMPLETE = 'Complete';

	public function add($type, $shipping = array())
	{
		if (!isset($data['address_id'])) {
			trigger_error(__METHOD__ . "(): address not specified");
			return;
		}

		$shipping['type'] = $type;

		//Always add Pending entry for Shipping
		$status_update = !empty($shipping['status']) ? $shipping['status'] : false;

		$shipping['status'] = self::PENDING;

		$shipping['date_added']    = $this->date->now();
		$shipping['date_modified'] = $shipping['date_added'];

		$shipping_id = $this->insert('shipping', $shipping);

		$history_data = array(
			'type'    => 'add',
			'status'  => $shipping['status'],
			'comment' => _l("Initiated Shipping"),
		);

		$this->addHistory($shipping_id, $history_data);

		if ($status_update) {
			$this->updateStatus($shipping_id, $status_update);
		}

		return $shipping_id;
	}

	public function edit($shipping_id, $data)
	{
		$shipping = $this->get($shipping_id);

		if ($shipping['status'] === self::COMPLETE) {
			$this->error_log->write(__METHOD__ . "(): Attempted to edit a Complete Shipping entry. Aborted action.");
			return false;
		}

		unset($data['status']);
		unset($data['date_added']);

		$this->update('shipping', $data, $shipping_id);

		$history_data = array(
			'type'    => 'edit',
			'status'  => $shipping['status'],
			'comment' => _l("Edited shipping Details"),
		);

		$this->addHistory($shipping_id, $history_data);

		return true;
	}

	public function confirm($shipping_id)
	{
		return $this->updateStatus($shipping_id, self::COMPLETE);
	}

	public function updateStatus($shipping_id, $status)
	{
		$shipping = $this->get($shipping_id);

		if ($shipping['status'] === $status) {
			return false;
		}

		$this->update('shipping', array('status' => $status), $shipping_id);

		//Permanently save shipping address
		if (!empty($shipping['address_id'])) {
			$this->address->lock($shipping['address_id']);
		}

		$history_data = array(
			'type'    => 'update',
			'status'  => $status,
			'comment' => _l("Status Updated"),
		);

		$this->addHistory($shipping_id, $history_data);

		return true;
	}

	public function get($shipping_id)
	{
		return $this->queryRow("SELECT * FROM " . DB_PREFIX . "shipping WHERE shipping_id = " . (int)$shipping_id);
	}

	private function addHistory($shipping_id, $data)
	{
		$data['shipping_id'] = $shipping_id;
		$data['date_added']  = $this->date->now();

		return $this->insert('shipping_history', $data);
	}
}
