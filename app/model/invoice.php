<?php

class App_Model_Invoice extends App_Model_Table
{
	protected $table = 'invoice', $primary_key = 'invoice_id';

	const
		STATUS_PENDING = 1,
		STATUS_PAID = 2,
		STATUS_LATE = 3,
		STATUS_CANCELLED = 4;

	static $statuses = array(
		self::STATUS_PENDING   => 'Pending',
		self::STATUS_PAID      => 'Paid',
		self::STATUS_LATE      => 'Past Due',
		self::STATUS_CANCELLED => 'Cancelled',
	);

	public function save($invoice_id, $invoice, $meta_type = null)
	{
		if (!isset($invoice['data'])) {
			if (isset($invoice['batch'])) {
				$invoice['data']['line_items'] = $invoice['batch'];
			}
		}

		if (isset($invoice['data'])) {
			$invoice['data'] = serialize($invoice['data']);
		}

		$invoice['date_updated'] = $this->date->now();

		if ($invoice_id) {
			$invoice_id = $this->update($this->table, $invoice, $invoice_id);
		} else {
			$invoice['date_created'] = $this->date->now();

			if (!isset($invoice['status'])) {
				$invoice['status'] = self::STATUS_PENDING;
			}

			if (empty($invoice['date_due'])) {
				$invoice['date_due'] = $this->date->add(null, option('invoice_due_date', '14 days'));
			}

			if (!isset($invoice['number'])) {
				$invoice['number'] = $this->generateNumber();
			}

			$invoice_id = $this->insert($this->table, $invoice);
		}

		if ($invoice_id && $meta_type) {
			if (!empty($invoice['batch'])) {
				foreach ($invoice['batch'] as $line_item) {
					$this->Model_Meta->set($meta_type, $line_item, 'invoiced', $invoice_id);
				}
			}
		}

		return $invoice_id;
	}

	public function getRecord($invoice_id, $select = '*')
	{
		$invoice = parent::getRecord($invoice_id, $select);

		if (!empty($invoice['data'])) {
			$invoice['data'] = unserialize($invoice['data']);
		}

		return $invoice;
	}

	public function generateNumber()
	{
		$number = (int)option('invoice_counter', 219203);

		$number++;

		save_option('invoice_counter', $number);

		return $number;
	}

	public function getColumns($filter = array())
	{
		$columns = array(
			'status' => array(
				'type'         => 'select',
				'display_name' => _l("Status"),
				'build'        => array(
					'type' => 'multiselect',
					'data' => self::$statuses,
				),
			),
		);

		return parent::getTableColumns($this->table, $columns, $filter);
	}
}
