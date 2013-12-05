<?php
class Admin_Model_Catalog_Information extends Model
{
	public function addInformation($data)
	{
		$information_id = $this->insert('information', $data);

		if (!empty($data['stores'])) {
			foreach ($data['stores'] as $store_id) {
				$store_data = array(
					'store_id'       => $store_id,
					'information_id' => $information_id,
				);

				$this->insert('information_to_store', $store_data);
			}
		}

		if (!empty($data['information_layout'])) {
			foreach ($data['information_layout'] as $store_id => $layout) {
				if ($layout) {
					$layout['store_id']       = $store_id;
					$layout['information_id'] = $information_id;

					$this->insert('information_to_layout', $layout);
				}
			}
		}

		if (!empty($data['alias'])) {
			$this->url->setAlias($data['alias'], 'information/information', 'information_id=' . (int)$information_id);
		}

		if (!empty($data['translations'])) {
			$this->translation->setTranslations('information', $information_id, $data['translations']);
		}

		$this->cache->delete('information');

		return $information_id;
	}

	public function editInformation($information_id, $data)
	{
		$this->update('information', $data, $information_id);

		$this->delete('information_to_store', array('information_id' => $information_id));

		if (!empty($data['stores'])) {
			foreach ($data['stores'] as $store_id) {
				$store_data = array(
					'store_id'       => $store_id,
					'information_id' => $information_id,
				);

				$this->insert('information_to_store', $store_data);
			}
		}

		$this->delete('information_to_layout', array('information_id' => $information_id));

		if (!empty($data['layouts'])) {
			foreach ($data['layouts'] as $store_id => $layout_id) {
				$layout_data = array(
					'layout_id'      => $layout_id,
					'store_id'       => $store_id,
					'information_id' => $information_id,
				);

				$this->insert('information_to_layout', $layout_data);
			}
		}

		if (!empty($data['alias'])) {
			$this->url->setAlias($data['alias'], 'information/information', 'information_id=' . (int)$information_id);
		} else {
			$this->url->removeAlias('information/information', 'information_id=' . (int)$information_id);
		}

		if (!empty($data['translations'])) {
			$this->translation->setTranslations('information', $information_id, $data['translations']);
		}

		$this->cache->delete('information');
	}

	public function copyInformation($information_id)
	{
		$information = $this->getInformation($information_id);

		unset($information['information_id']);

		$num = $this->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "information WHERE title like '" . $information['title'] . "%'");

		$information['title'] .= " - Copy" . ($num > 1 ? ' (' . $num . ')' : '');

		$information['alias'] = '';

		$information['stores']  = $this->getInformationStores($information_id);
		$information['layouts'] = $this->getInformationLayouts($information_id);

		$information['translations'] = $this->translation->getTranslations('information', $information_id);

		$this->addInformation($information);
	}

	public function deleteInformation($information_id)
	{
		$this->delete('information', $information_id);
		$this->delete('information_to_store', array('information_id' => $information_id));
		$this->delete('information_to_layout', array('information_id' => $information_id));

		$this->url->removeAlias('information/information', 'information_id=' . (int)$information_id);

		$this->translation->deleteTranslation('information', $information_id);

		$this->cache->delete('information');
	}

	public function getInformation($information_id)
	{
		$information = $this->queryRow("SELECT * FROM " . DB_PREFIX . "information WHERE information_id = '" . (int)$information_id . "'");

		$information['alias'] = $this->url->getAlias('information/information', 'information_id=' . $information_id);

		return $information;
	}

	public function getInformations($data = array(), $select = '', $total = false)
	{
		//Select
		if ($total) {
			$select = "COUNT(*) as total";
		} elseif (empty($select)) {
			$select = '*';
		}

		//From
		$from = DB_PREFIX . "information i";

		//Where
		$where = "1";

		if (!empty($data['title'])) {
			$where .= " AND LCASE(title) like '%" . $this->escape(strtolower($data['title'])) . "%'";
		}

		if (!empty($data['status'])) {
			$where .= " AND status = '" . (int)$data['status'] . "'";
		}

		if (isset($data['status'])) {
			$where .= " AND status = '" . (int)$data['status'] . "'";
		}

		if (!empty($data['layouts'])) {
			$from .= " LEFT JOIN " . DB_PREFIX . "information_to_layout i2l ON (i.information_id=i2l.information_id)";

			$where .= " AND i2l.layout_id IN (" . implode(',', $data['layouts']) . ")";
		}

		//Order and Limit
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

	public function getInformationStores($information_id)
	{
		return $this->queryColumn("SELECT store_id FROM " . DB_PREFIX . "information_to_store WHERE information_id = '" . (int)$information_id . "'");
	}

	public function getInformationLayouts($information_id)
	{
		$layout_list = $this->queryRows("SELECT store_id, layout_id FROM " . DB_PREFIX . "information_to_layout WHERE information_id = '" . (int)$information_id . "'");

		$layouts = array();

		foreach ($layout_list as $layout) {
			$layouts[$layout['store_id']] = $layout['layout_id'];
		}

		return $layouts;
	}

	public function getTotalInformations($data = array())
	{
		return $this->getInformations($data, '', true);
	}
}
