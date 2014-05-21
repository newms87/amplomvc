<?php
class App_Model_Report_Product extends Model
{
	public function getProductViews()
	{
		return $this->queryRows("SELECT * FROM " . DB_PREFIX . "product_views");
	}

	public function getProductsViewed($data = array())
	{
		//Select
		$select = "p.name, p.model, pv.product_id, pv.user_id, pv.ip_address, pv.session_id, COUNT(pv.product_id) as views";

		//From
		$from = DB_PREFIX . "product p JOIN " .
			DB_PREFIX . "product_views pv ON(pv.product_id=p.product_id)";

		$order = $this->extractOrder();
		$limit = $this->extractLimit();

		$query = "SELECT $select FROM $from GROUP BY pv.product_id ORDER BY views DESC $limit";

		$products = $this->queryRows($query);

		$this->translation->translate('product', 'product_id', $products);

		return $products;
	}

	public function getTotalProductsViewed()
	{
		return $this->queryVar("SELECT COUNT(DISTINCT product_id) as total FROM " . DB_PREFIX . "product_views");
	}

	public function getTotalProductViews()
	{
		return $this->queryVar("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_views");
	}

	public function reset()
	{
		$this->query("TRUNCATE " . DB_PREFIX . "product_views");
	}

	public function getPurchased($data = array())
	{
		$sql = "SELECT op.name, op.model, SUM(op.quantity) AS quantity, SUM(op.total + op.total * op.tax / 100) AS total FROM " . DB_PREFIX . "order_product op LEFT JOIN `" . DB_PREFIX . "order` o ON (op.order_id = o.order_id)";

		if (!is_null($data['filter_order_status_id'])) {
			$sql .= " WHERE o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " WHERE o.order_status_id > '0'";
		}

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o.date_added) >= '" . $this->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o.date_added) <= '" . $this->escape($data['filter_date_end']) . "'";
		}

		$sql .= " GROUP BY op.model ORDER BY total DESC";

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
	}

	public function getTotalPurchased($data)
	{
		$sql = "SELECT COUNT(DISTINCT op.model) AS total FROM `" . DB_PREFIX . "order_product` op LEFT JOIN `" . DB_PREFIX . "order` o ON (op.order_id = o.order_id)";

		if (!is_null($data['filter_order_status_id'])) {
			$sql .= " WHERE o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " WHERE o.order_status_id > '0'";
		}

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o.date_added) >= '" . $this->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o.date_added) <= '" . $this->escape($data['filter_date_end']) . "'";
		}

		$query = $this->query($sql);

		return $query->row['total'];
	}
}
