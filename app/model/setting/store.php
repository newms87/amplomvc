<?php
class App_Model_Setting_Store extends Model
{
	public function save($store_id, $store)
	{
		if (!validate('text', $store['name'], 1, 64)) {
			$this->error['name'] = _l("Store Name must be between 1 and 64 characters!");
		}

		if (!validate('url', $store['url'])) {
			$store['url'] = 'http://' . DOMAIN . '/' . SITE_BASE;
		}

		if (!validate('url', $store['ssl'])) {
			$store['ssl'] = 'https://' . DOMAIN . '/' . SITE_BASE;
		}

		if (!validate('text', $store['config_owner'], 2, 127)) {
			$this->error['config_owner'] = _l("Store Owner must be between 2 and 127 characters!");
		}

		if (!validate('email', $store['config_email'])) {
			$this->error['config_email'] = _l("E-Mail Address does not appear to be valid!");
		}

		if (!validate('text', $store['config_title'], 2, 127)) {
			$this->error['config_title'] = _l("Title must be between 2 and 127 characters!");
		}

		$image_sizes = array(
			'image_category' => "Category List",
			'image_thumb'    => "Product Thumb",
			'image_popup'    => "Product Popup",
		);

		foreach ($image_sizes as $image_key => $image_size) {
			$image_width  = 'config_' . $image_key . '_width';
			$image_height = 'config_' . $image_key . '_height';

			if ((int)$store[$image_width] <= 0) {
				$store[$image_width] = 0;
			}

			if ((int)$store[$image_height] <= 0) {
				$store[$image_height] = 0;
			}
		}

		if ((int)$store['config_catalog_limit'] <= 0) {
			$store['config_catalog_limit'] = 20;
		}

		if ($this->error) {
			return false;
		}

		$this->cache->delete('store');
		$this->cache->delete('theme');

		if ($store_id) {
			return $this->update('store', $store, $store_id);
		} else {
			return $this->insert('store', $store);
		}
	}

	public function deleteStore($store_id)
	{
		$this->delete('store', $store_id);

		$this->cache->delete('store');
		$this->cache->delete('theme');
	}

	public function getStore($store_id)
	{
		return $this->queryRow("SELECT * FROM " . DB_PREFIX . "store WHERE store_id = '" . (int)$store_id . "'");
	}

	public function getStoreName($store_id)
	{
		return $this->queryVar("SELECT name FROM " . DB_PREFIX . "store WHERE store_id = '" . (int)$store_id . "'");
	}

	public function getStoreNames()
	{
		return $this->queryRows("SELECT store_id, name FROM " . DB_PREFIX . "store");
	}

	public function getStores($data = array(), $select = '', $total = false)
	{
		//Select
		if ($total) {
			$select = "COUNT(*) as total";
		} elseif (empty($select)) {
			$select = "*";
		}

		//From
		$from = DB_PREFIX . "store s";

		//Where
		$where = "store_id > 0";

		//Order By & Limit
		list($order, $limit) = $this->extractOrderLimit($data);

		//The Query
		$query = "SELECT $select FROM $from WHERE $where $order $limit";

		//Results
		$result = $this->query($query);

		if ($total) {
			return $result->row['total'];
		}

		return $result->rows;
	}

	public function getTotalStores($data = array())
	{
		return $this->getStores($data, '', true);
	}

	public function getTotalStoresByLanguage($language)
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_language' AND `value` = '" . $this->escape($language) . "' AND store_id != '0'");

		return $query->row['total'];
	}

	public function getTotalStoresByCurrency($currency)
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_currency' AND `value` = '" . $this->escape($currency) . "' AND store_id != '0'");

		return $query->row['total'];
	}

	public function getTotalStoresByCountryId($country_id)
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_country_id' AND `value` = '" . (int)$country_id . "' AND store_id != '0'");

		return $query->row['total'];
	}

	public function getTotalStoresByZoneId($zone_id)
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_zone_id' AND `value` = '" . (int)$zone_id . "' AND store_id != '0'");

		return $query->row['total'];
	}

	public function getTotalStoresByCustomerGroupId($customer_group_id)
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_customer_group_id' AND `value` = '" . (int)$customer_group_id . "' AND store_id != '0'");

		return $query->row['total'];
	}
}
