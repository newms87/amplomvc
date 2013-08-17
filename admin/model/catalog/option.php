<?php
class Admin_Model_Catalog_Option extends Model
{
	public function addOption($data)
	{
		$option_id = $this->insert('option', $data);

		if (!empty($data['option_value'])) {
			foreach ($data['option_value'] as $option_value) {
				$option_value['option_id'] = $option_id;

				$option_value_id = $this->insert('option_value', $option_value);

				if (!empty($attribute['translations'])) {
					$this->translation->setTranslations('option_value', $option_value_id, $option_value['translations']);
				}
			}
		}

		if (!empty($data['translations'])) {
			$this->translation->setTranslations('option', $option_id, $data['translations']);
		}

		$this->cache->delete('option');

		return $option_id;
	}

	public function editOption($option_id, $data)
	{
		$this->update('option', $data, $option_id);


		if (isset($data['option_value'])) {
			foreach ($data['option_value'] as $option_value) {
				$option_value['option_id'] = $option_id;

				if ($option_value['option_value_id']) {
					$this->update('option_value', $option_value, $option_value['option_value_id']);
				} else {
					$option_value['option_value_id'] = $this->insert('option_value', $option_value);
				}

				if (!empty($attribute['translations'])) {
					$this->translation->setTranslations('option_value', $option_value['option_value_id'], $option_value['translations']);
				}
			}
		}

		$this->cache->delete('option');
	}

	public function deleteOption($option_id)
	{
		$this->delete('option', $option_id);
		$this->delete('option_value', array('option_id' => $option_id));

		$this->cache->delete('option');
	}

	public function getOption($option_id)
	{
		return $this->queryRow("SELECT * FROM `" . DB_PREFIX . "option` o WHERE o.option_id = " . (int)$option_id);
	}

	public function getOptionTranslations($option_id)
	{
		$translate_fields = array(
			'name',
			'display_name',
		);

		return $this->translation->getTranslations('option', $option_id, $translate_fields);
	}

	public function getOptions($data = array(), $select = '', $total = false)
	{
		//Select
		if ($total) {
			$select = "COUNT(*) as total";
		} elseif (empty($select)) {
			$select = '*';
		}

		//From
		$from = DB_PREFIX . "option o";

		//Where
		$where = '1';

		if (empty($data['sort'])) {
			$data['sort'] = 'o.sort_order';
		}

		if (!empty($data['name'])) {
			$where .= " AND LCASE(o.name) like '%" . $this->escape(strtolower($data['name'])) . "%'";
		}

		if (!empty($data['sort_order'])) {
			if (!empty($data['sort_order']['low'])) {
				$where .= " AND o.sort_order >= '" . (int)$data['sort_order']['low'] . "'";
			}

			if (!empty($data['sort_order']['high'])) {
				$where .= " AND o.sort_order <= '" . (int)$data['sort_order']['high'] . "'";
			}
		}

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

	public function getOptionValues($option_id, $data = array(), $select = '', $total = false)
	{
		//Select
		if ($total) {
			$select = "COUNT(*) as total";
		} elseif (empty($select)) {
			$select = '*';
		}

		//From
		$from = DB_PREFIX . "option_value ov";

		//Where
		$where = 'ov.option_id = ' . (int)$option_id;

		if (empty($data['sort'])) {
			$data['sort'] = 'ov.sort_order';
		}

		if (!empty($data['value'])) {
			$where .= " AND LCASE(ov.value) like '%" . $this->escape(strtolower($data['value'])) . "%'";
		}

		if (!empty($data['!option_value_ids'])) {
			$where .= " AND option_value_id NOT IN (" . implode(',', $data['!option_value_ids']) . ")";
		}

		if (!empty($data['sort_order'])) {
			if (!empty($data['sort_order']['low'])) {
				$where .= " AND ov.sort_order >= '" . (int)$data['sort_order']['low'] . "'";
			}

			if (!empty($data['sort_order']['high'])) {
				$where .= " AND ov.sort_order <= '" . (int)$data['sort_order']['high'] . "'";
			}
		}

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

	public function getOptionValueTranslations($option_value_id)
	{
		$translate_fields = array(
			'value',
		);

		return $this->translation->getTranslations('option_value', $option_value_id, $translate_fields);
	}

	public function getTotalOptions($filter = array())
	{
		return $this->getOptions($filter, '', true);
	}

	public function countProducts($option_id)
	{
		$filter = array(
			'options' => array($option_id),
		);

		return $this->Model_Catalog_Product->getTotalProducts($filter);
	}
}
