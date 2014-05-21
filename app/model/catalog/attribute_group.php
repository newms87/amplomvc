<?php
class App_Model_Catalog_AttributeGroup extends Model
{
	public function addAttributeGroup($data)
	{
		$attribute_group_id = $this->insert('attribute_group', $data);

		if (!empty($data['attributes'])) {
			foreach ($data['attributes'] as $attribute) {
				$attribute['attribute_group_id'] = $attribute_group_id;

				$attribute_id = $this->insert('attribute', $attribute);

				if (!empty($attribute['translations'])) {
					$this->translation->setTranslations('attribute', $attribute_id, $attribute['translations']);
				}
			}
		}

		if (!empty($data['translations'])) {
			$this->translation->setTranslations('attribute_group', $attribute_group_id, $data['translations']);
		}

		return $attribute_group_id;
	}

	public function editAttributeGroup($attribute_group_id, $data)
	{
		$this->update('attribute_group', $data, $attribute_group_id);

		if (!empty($data['attributes'])) {
			//All current attribute_ids for this group
			$attribute_ids = array();

			foreach ($data['attributes'] as $attribute) {
				$attribute['attribute_group_id'] = $attribute_group_id;

				//Update existing attributes to keep product associations
				$exists = false;

				if ((int)$attribute['attribute_id']) {
					$exists = $this->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "attribute WHERE attribute_id = '" . (int)$attribute['attribute_id'] . "'");
				}

				if ($exists) {
					$attribute_id = $attribute['attribute_id'];

					$this->update('attribute', $attribute, $attribute_id);
				} else {
					$attribute_id = $this->insert('attribute', $attribute);
				}

				if (!empty($attribute['translations'])) {
					$this->translation->setTranslations('attribute', $attribute_id, $attribute['translations']);
				}

				$attribute_ids[] = $attribute_id;
			}

			$this->query("DELETE FROM " . DB_PREFIX . "attribute WHERE attribute_group_id = '" . (int)$attribute_group_id . "' AND attribute_id NOT IN (" . implode(',', $attribute_ids) . ")");
		}

		if (!empty($data['translations'])) {
			$this->translation->setTranslations('attribute_group', $attribute_group_id, $data['translations']);
		}
	}

	public function deleteAttributeGroup($attribute_group_id)
	{
		$this->delete('attribute_group', $attribute_group_id);

		$this->translation->deleteTranslation('attribute_group', $attribute_group_id);

		$attributes = $this->queryRows("SELECT attribute_id FROM " . DB_PREFIX . "attribute WHERE attribute_group_id = '" . (int)$attribute_group_id . "'");

		foreach ($attributes as $attribute) {
			$this->translation->deleteTranslation('attribute', $attribute['attribute_id']);
		}

		$this->delete('attribute', array('attribute_group_id' => $attribute_group_id));
	}

	public function getAttributeGroup($attribute_group_id)
	{
		return $this->queryRow("SELECT * FROM " . DB_PREFIX . "attribute_group WHERE attribute_group_id = '" . (int)$attribute_group_id . "'");
	}

	public function getAttributeGroups($data = array(), $select = '', $total = false)
	{
		//Select
		if ($total) {
			$select = "COUNT(*) as total";
		} elseif (empty($select)) {
			$select = '*';
		}

		//From
		$from = DB_PREFIX . "attribute_group ag";

		//Where
		$where = '1';

		if (empty($data['sort'])) {
			$data['sort'] = 'ag.sort_order';
		}

		if (!empty($data['name'])) {
			$where .= " AND LCASE(ag.name) like '%" . $this->escape(strtolower($data['name'])) . "%'";
		}

		if (!empty($data['attribute_count']) || $data['sort'] === 'attribute_count') {
			$select .= ",(SELECT COUNT(*) FROM " . DB_PREFIX . "attribute a WHERE ag.attribute_group_id = a.attribute_group_id) as attribute_count";

			if (!empty($data['attribute_count']['low'])) {
				$where .= " AND attribute_count >= '" . (int)$data['attribute_count']['low'] . "'";
			}

			if (!empty($data['attribute_count']['high'])) {
				$where .= " AND attribute_count <= '" . (int)$data['attribute_count']['high'] . "'";
			}
		}

		if (!$total) {
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

	public function getAttributes($attribute_group_id)
	{
		$attributes = $this->queryRows("SELECT * FROM " . DB_PREFIX . "attribute WHERE attribute_group_id = '" . (int)$attribute_group_id . "' ORDER BY sort_order");

		return $attributes;
	}

	public function getAttributeProductCount($attribute_id)
	{
		return $this->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "product_attribute WHERE attribute_id = '" . (int)$attribute_id . "'");
	}

	public function getAttributesFilter($data = array())
	{
		//Select
		$select = '*';

		//From
		$from = DB_PREFIX . "attribute a";

		//Where
		$where = '1';

		if (!empty($data['name'])) {
			$where .= " AND LCASE(name) like '%" . $this->escape(strtolower($data['name'])) . "%'";
		}

		//Order and Limit
		$order = $this->extractOrder($data);
		$limit = $this->extractLimit($data);

		$query = "SELECT * FROM $from WHERE $where $order $limit";

		return $this->queryRows($query);
	}

	public function getTotalAttributeGroups($data = array())
	{
		return $this->getAttributeGroups($data, '', true);
	}

	public function hasProductAssociation($attribute_group_id)
	{
		return $this->queryVar(
			"SELECT COUNT(*) as total FROM " . DB_PREFIX . "attribute a" .
			" JOIN " . DB_PREFIX . "product_attribute pa ON (pa.attribute_id=a.attribute_id)" .
			" WHERE a.attribute_group_id = '" . (int)$attribute_group_id . "'"
		);
	}
}
