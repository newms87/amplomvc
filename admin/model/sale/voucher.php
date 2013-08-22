<?php
class Admin_Model_Sale_Voucher extends Model
{
	public function addVoucher($data)
	{
		$data['date_added'] = $this->date->now();

		return $this->insert('voucher', $data);
	}

	public function editVoucher($voucher_id, $data)
	{
		$this->update('voucher', $data, $voucher_id);
	}

	public function copyVoucher($voucher_id)
	{
		$voucher = $this->getVoucher($voucher_id);

		$code_count = $this->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "voucher WHERE code like '" . $this->escape($voucher['code']) . "%'");

		$voucher['code'] .= $code_count + 1;

		$this->addVoucher($voucher);
	}

	public function deleteVoucher($voucher_id)
	{
		$this->delete('voucher', $voucher_id);
		$this->delete('voucher_history', array('voucher_id' => $voucher_id));
	}

	public function getVoucher($voucher_id)
	{
		return $this->queryRow("SELECT * FROM " . DB_PREFIX . "voucher WHERE voucher_id = '" . (int)$voucher_id . "'");
	}

	public function getVoucherByCode($code)
	{
		return $this->queryRow("SELECT * FROM " . DB_PREFIX . "voucher WHERE code = '" . $this->escape($code) . "' LIMIT 1");
	}

	public function getVouchers($data = array(), $select = '', $total = false)
	{
		//Select
		if ($total) {
			$select = "COUNT(*) as total";
		} elseif (empty($select)) {
			$select = "v.*, vt.*, vt.name as theme";
		}

		//From
		$from =
			DB_PREFIX . "voucher v" .
			" LEFT JOIN " . DB_PREFIX . "voucher_theme vt ON (vt.voucher_theme_id=v.voucher_theme_id)";

		//Where
		$where = '1';

		if (isset($data['code'])) {
			$where .= " AND LCASE(v.code) like '%" . $this->escape(strtolower($data['code'])) . "%'";
		}

		if (isset($data['theme'])) {
			$where .= " AND vt.voucher_theme_id = " . (int)$data['theme'];
		}

		if (isset($data['to_name'])) {
			$where .= " AND LCASE(v.to_name) like '%" . $this->escape(strtolower($data['to_name'])) . "%'";
		}

		if (isset($data['to_email'])) {
			$where .= " AND LCASE(v.to_email) like '%" . $this->escape(strtolower($data['to_email'])) . "%'";
		}

		if (isset($data['from_name'])) {
			$where .= " AND LCASE(v.from_name) like '%" . $this->escape(strtolower($data['from_name'])) . "%'";
		}

		if (isset($data['from_email'])) {
			$where .= " AND LCASE(v.from_email) like '%" . $this->escape(strtolower($data['to_email'])) . "%'";
		}

		if (isset($data['amount'])) {
			if (strpos($data['amount'], ',')) {
				list($low, $high) = explode(',', $data['amount'], 2);
			} else {
				$low  = $data['amount'];
				$high = false;
			}

			if ($low) {
				$where .= " AND v.amount >= " . (int)$low;
			}

			if ($high) {
				$where .= " AND v.amount < " . (int)$high;
			}
		}

		if (isset($data['date_added'])) {
			if (!empty($data['date_added']['start'])) {
				$where .= " AND v.date_added >= '" . $this->date->format($data['date_added']['start']) . "'";
			}

			if (!empty($data['date_added']['end'])) {
				$where .= " AND v.date_added < '" . $this->date->add($data['date_added']['end'], '1 day') . "'";
			}
		}

		if (isset($data['status'])) {
			$where .= " AND v.status = " . ($data['status'] ? 1 : 0);
		}

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

		return $result->rows;
	}

	public function getTotalVouchers($data = array())
	{
		return $this->getVouchers($data, '', true);
	}

	public function getTotalVouchersByVoucherThemeId($voucher_theme_id)
	{
		return $this->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "voucher WHERE voucher_theme_id = '" . (int)$voucher_theme_id . "'");
	}

	public function getVoucherHistories($voucher_id, $start = 0, $limit = 10)
	{
		return $this->queryRows("SELECT vh.order_id, CONCAT(o.firstname, ' ', o.lastname) AS customer, vh.amount, vh.date_added FROM " . DB_PREFIX . "voucher_history vh LEFT JOIN `" . DB_PREFIX . "order` o ON (vh.order_id = o.order_id) WHERE vh.voucher_id = '" . (int)$voucher_id . "' ORDER BY vh.date_added ASC LIMIT " . (int)$start . "," . (int)$limit);
	}

	public function getTotalVoucherHistories($voucher_id)
	{
		return $this->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "voucher_history WHERE voucher_id = '" . (int)$voucher_id . "'");
	}
}
