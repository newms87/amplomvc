<?php
class App_Model_Catalog_Manufacturer extends Model
{
	public function addManufacturer($data)
	{
		if (empty($data['date_active'])) {
			$data['date_active'] = DATETIME_ZERO;
		}

		if (empty($data['date_expires'])) {
			$data['date_expires'] = DATETIME_ZERO;
		}

		$manufacturer_id = $this->insert('manufacturer', $data);

		$vendor_id = $this->generate_vendor_id(array(
			'id'   => $manufacturer_id,
			'name' => $data['name']
		));
		$this->update('manufacturer', array('vendor_id' => $vendor_id), array('manufacturer_id' => $manufacturer_id));

		if (isset($data['stores'])) {
			foreach ($data['stores'] as $store_id) {
				$store_data = array(
					'manufacturer_id' => $manufacturer_id,
					'store_id'        => $store_id
				);

				$this->insert('manufacturer_to_store', $store_data);
			}
		}

		if (!empty($data['alias'])) {
			$this->url->setAlias($data['alias'], 'product/manufacturer', 'manufacturer_id=' . (int)$manufacturer_id);
		}

		if (!empty($data['translations'])) {
			$this->translation->setTranslations('manufacturer', $manufacturer_id, $data['translations']);
		}

		$this->cache->delete('manufacturer');
	}

	public function editManufacturer($manufacturer_id, $data)
	{
		if (!$data['date_active']) {
			$data['date_active'] = DATETIME_ZERO;
		}

		if (!$data['date_expires']) {
			$data['date_expires'] = DATETIME_ZERO;
		}

		$this->update('manufacturer', $data, array('manufacturer_id' => $manufacturer_id));

		$this->delete('manufacturer_to_store', array('manufacturer_id' => $manufacturer_id));

		if (isset($data['stores'])) {
			foreach ($data['stores'] as $store_id) {
				$values = array(
					'manufacturer_id' => $manufacturer_id,
					'store_id'        => $store_id
				);

				$this->insert('manufacturer_to_store', $values);
			}
		}

		if (isset($data['alias'])) {
			$this->url->setAlias($data['alias'], 'product/manufacturer', 'manufacturer_id=' . (int)$manufacturer_id);
		}

		if (!empty($data['translations'])) {
			$this->translation->setTranslations('manufacturer', $manufacturer_id, $data['translations']);
		}

		$this->cache->delete('manufacturer');
	}

	public function updateField($manufacturer_id, $data)
	{
		$this->insert('manufacturer', $data, $manufacturer_id);
	}

	public function copyManufacturer($manufacturer_id)
	{
		$manufacturer = $this->getManufacturer($manufacturer_id);

		$manufacturer['alias'] = '';

		$manufacturer['stores'] = $this->getManufacturerStores($manufacturer_id);

		$manufacturer['translations'] = $this->translation->getTranslations('manufacturer', $manufacturer_id);

		$this->addManufacturer($manufacturer);
	}

	public function deleteManufacturer($manufacturer_id)
	{
		$this->delete('manufacturer', array('manufacturer_id' => $manufacturer_id));
		$this->delete('manufacturer_to_store', array('manufacturer_id' => $manufacturer_id));

		$this->url->removeAlias('product/manufacturer', 'manufacturer_id=' . (int)$manufacturer_id);

		$this->translation->deleteTranslation('manufacturer', $manufacturer_id);

		$this->cache->delete('manufacturer');
	}

	public function generate_vendor_id($data)
	{
		$n = explode(' ', strtolower($data['name']), 2);
		$f = $n[0];
		$l = count($n) > 1 ? $n[1][0] : $f[1];
		return sprintf('%04d', $data['id']) . '-' . (sprintf('%02d', ord($f) - 96)) . (sprintf('%02d', ord($l) - 96));
	}

	public function getManufacturer($manufacturer_id)
	{
		return $this->queryRow("SELECT * FROM " . DB_PREFIX . "manufacturer WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
	}

	public function getActiveManufacturer($manufacturer_id)
	{
		return $this->queryRow("SELECT * FROM " . DB_PREFIX . "manufacturer m LEFT JOIN " . DB_PREFIX . "manufacturer_to_store m2s ON (m.manufacturer_id = m2s.manufacturer_id) WHERE m.status = 1 AND m.manufacturer_id = " . (int)$manufacturer_id . " AND m2s.store_id = " . (int)option('store_id'));
	}

	public function getManufacturerAndTeaser($manufacturer_id)
	{
		$query = $this->query("SELECT m.*, md.teaser FROM " . DB_PREFIX . "manufacturer m LEFT JOIN " . DB_PREFIX . "manufacturer_description md ON(m.manufacturer_id = md.manufacturer_id) LEFT JOIN " . DB_PREFIX . "manufacturer_to_store m2s ON (m.manufacturer_id = m2s.manufacturer_id) WHERE m.status='1' AND m.manufacturer_id = '" . (int)$manufacturer_id . "' AND m2s.store_id = '" . (int)option('store_id') . "'");
		return $query->row;
	}

	public function getManufacturerURL($manufacturer_id)
	{
		$query = $this->query("SELECT keyword FROM " . DB_PREFIX . "manufacturer m WHERE manufacturer_id='$manufacturer_id'");
		return isset($query->row) ? $this->url->site($query->row['keyword']) : null;
	}

	public function getManufacturerByKeyword($keyword)
	{
		$query = $this->query("SELECT manufacturer_id FROM " . DB_PREFIX . "manufacturer WHERE keyword='$keyword'");
		return isset($query->row) ? $query->row['manufacturer_id'] : null;
	}

	public function getManufacturers($data = array(), $select = '*', $total = false)
	{
		//Select
		if ($total) {
			$select = 'COUNT(*) as total';
		} elseif (empty($select)) {
			$select = '*';
		}

		//From
		$from = DB_PREFIX . "manufacturer m";

		//Where
		$where = "1";

		if (isset($data['name'])) {
			$where .= " AND LCASE(`name`) like '%" . $this->escape(strtolower($data['name'])) . "%'";
		}

		if (isset($data['status'])) {
			$where .= " AND status = " . (int)$data['status'];
		}

		if (isset($data['manufacturer_ids'])) {
			$where .= " AND manufacturer_id IN(" . implode(',', $data['manufacturer_ids']) . ")";
		}

		if (isset($data['store_ids'])) {
			$from .= " JOIN " . DB_PREFIX . "manufacturer_to_store ms ON (ms.manufacturer_id = m.manufacturer_id AND ms.store_id IN (" . implode(',', $this->escape($data['manufacturer_ids'])) . "))";
		}

		//Order and Limit
		if (!$total) {
			if (!empty($data['sort']) && strpos($data['sort'], '__image_sort__') === 0) {
				if (!$this->db->hasColumn('manufacturer', $data['sort'])) {
					$this->extend->enable_image_sorting('manufacturer', str_replace('__image_sort__', '', $data['sort']));
				}
			}

			$order = $this->extractOrder($data);
			$limit = $this->extractLimit($data);
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

	public function getActiveManufacturers($data = array(), $select = '*', $total = false)
	{
		$data['status'] = 1;
		$data['store_ids'] = array(
			option('store_id'),
		);

		return $this->getManufacturers($data, $select, $total);
	}

	public function getManufacturerStores($manufacturer_id)
	{
		$manufacturer_store_data = array();

		$query = $this->query("SELECT * FROM " . DB_PREFIX . "manufacturer_to_store WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		foreach ($query->rows as $result) {
			$manufacturer_store_data[] = $result['store_id'];
		}

		return $manufacturer_store_data;
	}

	public function getTotalManufacturers($data)
	{
		return $this->getManufacturers($data, '', true);
	}
}
