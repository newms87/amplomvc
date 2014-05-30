<?php

class App_Model_Sale_Voucher extends Model
{
	public function add($voucher)
	{
		if (!validate('text', $voucher['to_name'])) {
			$this->error['to_name'] = _l("You must provide the recipient's name");
		}

		if (!validate('text', $voucher['to_email'])) {
			$this->error['to_email'] = _l("You must provide the recipient's email");
		}

		if ($this->error) {
			return false;
		}

		$voucher += array(
			'from_name'  => 'Anonymous',
			'from_email' => 'anonymous@' . DOMAIN,
			'message'    => _l("Someone has sent you a gift for %s!", option('config_name')),
		);

		$data['date_added'] = $this->date->now();

		return $this->insert('voucher', $voucher);
	}

	public function remove($voucher_id)
	{
		$this->delete('voucher', $voucher_id);
	}

	public function getVoucher($voucher_id)
	{
		$voucher = $this->queryRow("SELECT * FROM " . DB_PREFIX . "voucher WHERE voucher_id = " . (int)$voucher_id);

		if ($voucher) {
			$spent = $this->queryVar("SELECT SUM(amount) FROM " . DB_PREFIX . "order_voucher WHERE voucher_id = " . (int)$voucher_id);
			$voucher['remaining'] = $voucher['amount'] - $spent;
		}

		return $voucher;
	}

	public function getVoucherByCode($code)
	{
		$voucher_id = $this->queryVar("SELECT voucher_id FROM " . DB_PREFIX . "voucher WHERE code = '" . $this->escape($code) . "' LIMIT 1");

		return $this->getVoucher($voucher_id);
	}

	public function getVouchers($filter = array(), $select = '*', $index = null)
	{
		//Select
		if ($index === false) {
			$select = "COUNT(*)";
		}

		//From
		$from = DB_PREFIX . "voucher";

		//Where
		$where = '1';

		if (isset($data['code'])) {
			$where .= " AND LCASE(code) like '%" . $this->escape(strtolower($data['code'])) . "%'";
		}

		if (isset($data['to_name'])) {
			$where .= " AND LCASE(to_name) like '%" . $this->escape(strtolower($data['to_name'])) . "%'";
		}

		if (isset($data['to_email'])) {
			$where .= " AND LCASE(to_email) like '%" . $this->escape(strtolower($data['to_email'])) . "%'";
		}

		if (isset($data['from_name'])) {
			$where .= " AND LCASE(from_name) like '%" . $this->escape(strtolower($data['from_name'])) . "%'";
		}

		if (isset($data['from_email'])) {
			$where .= " AND LCASE(from_email) like '%" . $this->escape(strtolower($data['to_email'])) . "%'";
		}

		if (isset($data['amount'])) {
			if (strpos($data['amount'], ',')) {
				list($low, $high) = explode(',', $data['amount'], 2);
			} else {
				$low  = $data['amount'];
				$high = false;
			}

			if ($low) {
				$where .= " AND amount >= " . (int)$low;
			}

			if ($high) {
				$where .= " AND amount < " . (int)$high;
			}
		}

		if (!empty($data['date_added']['start'])) {
			$where .= " AND date_added >= '" . $this->date->format($data['date_added']['start']) . "'";
		}

		if (!empty($data['date_added']['end'])) {
			$where .= " AND date_added < '" . $this->date->add($data['date_added']['end'], '1 day') . "'";
		}

		//Order and Limit
		if ($index !== false) {
			$order = $this->extractOrder($data);
			$limit = $this->extractLimit($data);
		} else {
			$order = '';
			$limit = '';
		}

		//The Query
		$query = "SELECT $select FROM $from WHERE $where $order $limit";

		if ($index === false) {
			return $this->queryVar($query);
		}

		return $this->queryRows($query, $index);
	}

	public function getTotalVouchers($filter = array())
	{
		return $this->getVouchers($filter, '', false);
	}

	public function redeem($voucher_id, $order_id, $amount)
	{
		$set = array(
			'amount' => $amount,
		);

		$where = array(
			'order_id' => $order_id,
		   'voucher_id' => $voucher_id,
		);

		if (!$this->update('order_voucher', $set, $where)) {
			$this->error['voucher_id'] = _l("The Voucher Ordered did not exist");
			return false;
		}

		call('mail/voucher', $voucher_id);

		return true;
	}
}
