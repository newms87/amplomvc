<?php
class App_Model_Setting_Store extends Model
{
	public function save($store_id, $store)
	{
		if (!validate('text', $store['name'], 1, 64)) {
			$this->error['name'] = _l("Store Name must be between 1 and 64 characters!");
		}

		if (!validate('url', $store['url'])) {
			$this->error['url'] = _l("Store URL invalid! Please provide a properly formatted URL (eg: http://yourstore.com)");
		}

		if (!validate('url', $store['ssl'])) {
			$this->error['ssl'] = _l("Store SSL invalid!  Please provide a properly formatted URL (eg: http://yourstore.com). NOTE: you may set this to the same value as URL, does not have to be HTTPS protocol.");
		}

		if (!validate('text', $store['config_owner'], 3, 64)) {
			$this->error['config_owner'] = _l("Store Owner must be between 3 and 64 characters!");
		}

		if (!validate('text', $store['config_address'], 3, 256)) {
			$this->error['config_address'] = _l("Store Address must be between 3 and 256 characters!");
		}

		if (!validate('email', $store['config_email'])) {
			$this->error['config_email'] = _l("E-Mail Address does not appear to be valid!");
		}

		if (!validate('phone', $store['config_telephone'])) {
			$this->error['config_telephone'] = $this->validate->getError();
		}

		if (!validate('text', $store['config_title'], 3, 32)) {
			$this->error['config_title'] = _l("Title must be between 3 and 32 characters!");
		}

		$image_sizes = array(
			'image_category' => "Category List",
			'image_thumb'    => "Product Thumb",
			'image_popup'    => "Product Popup",
		);

		foreach ($image_sizes as $image_key => $image_size) {
			$image_width  = 'config_' . $image_key . '_width';
			$image_height = 'config_' . $image_key . '_height';

			if ((int)$store[$image_width] <= 0 || (int)$store[$image_height] <= 0) {
				$this->error[$image_height] = _l("%s image dimensions are required.", $image_size);
			}
		}

		if ((int)$store['config_catalog_limit'] <= 0) {
			$this->error['config_catalog_limit'] = _l("Limit required!");
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
		if (!$total) {
			$order = $this->extractOrder($data);
			$limit = $this->extractLimit($data);
		} else {
			$order = '';
			$limit = '';
		}

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

	public function getTotalStoresByOrderStatusId($order_status_id)
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_order_complete_status_id' AND `value` = '" . (int)$order_status_id . "' AND store_id != '0'");

		return $query->row['total'];
	}
}
