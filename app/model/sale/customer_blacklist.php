<?php
class App_Model_Sale_CustomerBlacklist extends Model
{
	public function addCustomerBlacklist($data)
	{
		$this->query("INSERT INTO `" . DB_PREFIX . "customer_ip_blacklist` SET `ip` = '" . $this->escape($data['ip']) . "'");
	}

	public function editCustomerBlacklist($customer_ip_blacklist_id, $data)
	{
		$this->query("UPDATE `" . DB_PREFIX . "customer_ip_blacklist` SET `ip` = '" . $this->escape($data['ip']) . "' WHERE customer_ip_blacklist_id = '" . (int)$customer_ip_blacklist_id . "'");
	}

	public function deleteCustomerBlacklist($customer_ip_blacklist_id)
	{
		$this->query("DELETE FROM `" . DB_PREFIX . "customer_ip_blacklist` WHERE customer_ip_blacklist_id = '" . (int)$customer_ip_blacklist_id . "'");
	}

	public function getCustomerBlacklist($customer_ip_blacklist_id)
	{
		$query = $this->query("SELECT * FROM `" . DB_PREFIX . "customer_ip_blacklist` WHERE customer_ip_blacklist_id = '" . (int)$customer_ip_blacklist_id . "'");

		return $query->row;
	}

	public function getCustomerBlacklists($data = array())
	{
		$sql = "SELECT *, (SELECT COUNT(DISTINCT customer_id) FROM `" . DB_PREFIX . "customer_ip` ci WHERE ci.ip = cib.ip) AS total FROM `" . DB_PREFIX . "customer_ip_blacklist` cib";

		$sql .= " ORDER BY `ip`";

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

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

	public function getTotalCustomerBlacklists($data = array())
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "customer_ip_blacklist`");

		return $query->row['total'];
	}
}
