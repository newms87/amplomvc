<?php
class Transaction extends Library
{
	const PENDING    = 'Pending';
	const AUTHORIZED = 'Authorized';
	const REVERSED   = 'Reversed';
	const COMPLETE   = 'Complete';

	public function add($type, $transaction = array())
	{
		if (!isset($data['amount'])) {
			trigger_error(_l(__METHOD__ . "(): amount not specified"));
			return;
		}

		$transaction['type'] = $type;

		if (!$transaction['status']) {
			$transaction['status'] = self::PENDING;
		}

		$transaction['date_added']    = $this->date->now();
		$transaction['date_modified'] = $transaction['date_added'];

		$transaction_id = $this->insert('transaction', $transaction);

		if (!empty($transaction['address_id'])) {
			$this->address->lock($transaction['address_id']);
		}

		$history_data = array(
			'type'    => 'add',
			'status'  => $transaction['status'],
			'comment' => _l("Initiated Transaction"),
		);

		$this->addHistory($transaction_id, $history_data);

		return $transaction_id;
	}

	public function edit($transaction_id, $data)
	{
		$transaction = $this->get($transaction_id);

		if ($transaction['status'] === self::COMPLETE) {
			$this->error_log->write(_l("Attempted to edit a Complete transaction. Aborted action."));
			return false;
		}

		unset($data['status']);
		unset($data['date_added']);

		$this->update('transaction', $data, $transaction_id);

		$history_data = array(
			'type'    => 'edit',
			'status'  => $transaction['status'],
			'comment' => _l("Edited Transaction Details"),
		);

		$this->addHistory($transaction_id, $history_data);

		return true;
	}

	public function confirm($transaction_id)
	{
		return $this->updateStatus($transaction_id, self::COMPLETE);
	}

	public function updateStatus($transaction_id, $status)
	{
		$transaction = $this->get($transaction_id);

		if ($transaction['status'] === $status) {
			return false;
		}

		$this->update('transaction', array('status' => $status), $transaction_id);

		$history_data = array(
			'type'    => 'update',
			'status'  => $status,
			'comment' => _l("Status Updated"),
		);

		$this->addHistory($transaction_id, $history_data);

		return true;
	}

	public function get($transaction_id)
	{
		return $this->queryRow("SELECT * FROM " . DB_PREFIX . "transaction WHERE transaction_id = " . (int)$transaction_id);
	}

	private function addHistory($transaction_id, $data)
	{
		$data['transaction_id'] = $transaction_id;
		$data['date_added']     = $this->date->now();

		return $this->insert('transaction_history', $data);
	}
}
