<?php
class Admin_Model_Localisation_StockStatus extends Model
{
	public function addStockStatus($data)
	{
		foreach ($data['stock_status'] as $language_id => $value) {
			if (isset($stock_status_id)) {
				$this->query("INSERT INTO " . DB_PREFIX . "stock_status SET stock_status_id = '" . (int)$stock_status_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->escape($value['name']) . "'");
			} else {
				$this->query("INSERT INTO " . DB_PREFIX . "stock_status SET language_id = '" . (int)$language_id . "', name = '" . $this->escape($value['name']) . "'");

				$stock_status_id = $this->db->getLastId();
			}
		}

		$this->cache->delete('stock_status');
	}

	public function editStockStatus($stock_status_id, $data)
	{
		$this->query("DELETE FROM " . DB_PREFIX . "stock_status WHERE stock_status_id = '" . (int)$stock_status_id . "'");

		foreach ($data['stock_status'] as $language_id => $value) {
			$this->query("INSERT INTO " . DB_PREFIX . "stock_status SET stock_status_id = '" . (int)$stock_status_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->escape($value['name']) . "'");
		}

		$this->cache->delete('stock_status');
	}

	public function deleteStockStatus($stock_status_id)
	{
		$this->query("DELETE FROM " . DB_PREFIX . "stock_status WHERE stock_status_id = '" . (int)$stock_status_id . "'");

		$this->cache->delete('stock_status');
	}

	public function getStockStatus($stock_status_id)
	{
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "stock_status WHERE stock_status_id = '" . (int)$stock_status_id . "' AND language_id = '" . (int)option('config_language_id') . "'");

		return $query->row;
	}

	public function getStockStatuses($data = array())
	{
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "stock_status WHERE language_id = '" . (int)option('config_language_id') . "'";

			$sql .= " ORDER BY name";

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
		} else {
			$stock_status_data = $this->cache->get('stock_status.' . (int)option('config_language_id'));

			if (!$stock_status_data) {
				$query = $this->query("SELECT stock_status_id, name FROM " . DB_PREFIX . "stock_status WHERE language_id = '" . (int)option('config_language_id') . "' ORDER BY name");

				$stock_status_data = $query->rows;

				$this->cache->set('stock_status.' . (int)option('config_language_id'), $stock_status_data);
			}

			return $stock_status_data;
		}
	}

	public function getStockStatusDescriptions($stock_status_id)
	{
		$stock_status_data = array();

		$query = $this->query("SELECT * FROM " . DB_PREFIX . "stock_status WHERE stock_status_id = '" . (int)$stock_status_id . "'");

		foreach ($query->rows as $result) {
			$stock_status_data[$result['language_id']] = array('name' => $result['name']);
		}

		return $stock_status_data;
	}

	public function getTotalStockStatuses()
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "stock_status WHERE language_id = '" . (int)option('config_language_id') . "'");

		return $query->row['total'];
	}
}
