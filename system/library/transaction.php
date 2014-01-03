<?php

class Transaction extends Library
{
	const PENDING    = 'Pending';
	const AUTHORIZED = 'Authorized';
	const REVERSED   = 'Reversed';
	const FAILED     = 'Failed';
	const VOID       = 'Void';
	const COMPLETE   = 'Complete';

	public function add($type, $transaction = array())
	{
		if (!isset($transaction['amount'])) {
			trigger_error(__METHOD__ . "(): amount not specified");
			return;
		}

		$transaction['type'] = $type;

		//Always add Pending entry for transactions
		$status_update = !empty($transaction['status']) ? $transaction['status'] : false;

		$transaction['status'] = self::PENDING;

		if (!isset($transaction['retries'])) {
			$transaction['retries'] = 0;
		}

		$transaction['date_added']    = $this->date->now();
		$transaction['date_modified'] = $transaction['date_added'];

		$transaction_id = $this->insert('transaction', $transaction);

		//Permanently save transaction address
		if (!empty($transaction['address_id'])) {
			$this->address->lock($transaction['address_id']);
		}

		$history_data = array(
			'type'    => 'add',
			'status'  => $transaction['status'],
			'comment' => _l("Initiated Transaction"),
		);

		$this->addHistory($transaction_id, $history_data);

		if ($status_update) {
			$this->updateStatus($transaction_id, $status_update);
		}

		return $transaction_id;
	}

	public function edit($transaction_id, $data)
	{
		$transaction = $this->get($transaction_id);

		if ($transaction['status'] === self::COMPLETE) {
			$this->error_log->write("Attempted to edit a Complete transaction. Aborted action.");
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
		$transaction = $this->get($transaction_id);

		if ($transaction['status'] === self::FAILED && $transaction['retries'] > 0) {
			//Retry Confirming Transaction.
			$transaction['retries']--;
		}
		elseif ($transaction['status'] !== self::PENDING) {
			$this->error['status'] = _l("Transaction status is %s and cannot be confirmed!", $transaction['status']);
			return false;
		}

		$payment = $this->System_Extension_Payment->get($transaction['payment_method']);

		if (!$payment || !$payment->charge($transaction)) {
			$data = array(
				'retries' => $transaction['retries'],
				'status'  => self::FAILED,
			);

			$this->update('transaction', $data, $transaction_id);

			$history_data = array(
				'type'    => 'confirm',
				'comment' => _l("Payment Failed. %s attempts remaining.", $transaction['retries']),
				'status'  => self::FAILED,
			);

			$this->addHistory($transaction_id, $history_data);

			return false;
		}

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

	public function retryFailedTransactions()
	{
		$failed = self::FAILED;

		$transactions = $this->queryColumn("SELECT transaction_id FROM " . DB_PREFIX . "transaction WHERE status = '$failed' AND retries > 0");

		foreach ($transactions as $transaction_id) {
			$this->confirm($transaction_id);
		}
	}

	private function addHistory($transaction_id, $data)
	{
		$data['transaction_id'] = $transaction_id;
		$data['date_added']     = $this->date->now();

		return $this->insert('transaction_history', $data);
	}
}
