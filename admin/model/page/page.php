<?php
class Admin_Model_Page_Page extends Model
{
	public function addPage($data)
	{
		$page_id = $this->insert('page', $data);

		if (!empty($data['stores'])) {
			foreach ($data['stores'] as $store_id) {
				$store_data = array(
					'page_id'  => $page_id,
					'store_id' => $store_id
				);

				$this->insert('page_store', $store_data);
			}
		}

		if (!empty($data['alias'])) {
			$this->url->setAlias($data['alias'], 'page/page', 'page_id=' . (int)$page_id);
		}

		if (!empty($data['translations'])) {
			$this->translation->setTranslations('page', $page_id, $data['translations']);
		}

		$this->cache->delete('page');
	}

	public function editPage($page_id, $data)
	{
		$this->update('page', $data, $page_id);

		$this->delete('page_store', array('page_id' => $page_id));

		if (!empty($data['stores'])) {
			foreach ($data['stores'] as $store_id) {
				$store_data = array(
					'page_id'  => $page_id,
					'store_id' => $store_id
				);

				$this->insert('page_store', $store_data);
			}
		}

		if (!empty($data['alias'])) {
			$this->url->setAlias($data['alias'], 'page/page', 'page_id=' . (int)$page_id);
		} else {
			$this->url->removeAlias('page/page', 'page_id=' . (int)$page_id);
		}

		if (!empty($data['translations'])) {
			$this->translation->setTranslations('page', $page_id, $data['translations']);
		}

		$this->cache->delete('page');
	}

	public function update_field($page_id, $data)
	{
		$this->update('page', $data, $page_id);
	}

	public function copyPage($page_id)
	{
		$page           = $this->getPage($page_id);
		$page['stores'] = $this->getPageStores($page_id);

		$this->addPage($page);
	}

	public function deletePage($page_id)
	{
		$this->delete('page', $page_id);
		$this->delete('page_store', array('page_id' => $page_id));

		$this->url->removeAlias('page/page', 'page_id=' . $page_id);

		$this->translation->deleteTranslation('page', $page_id);

		$this->cache->delete('page');
	}

	public function getPage($page_id)
	{
		$page = $this->queryRow("SELECT * FROM " . DB_PREFIX . "page WHERE page_id = '" . (int)$page_id . "'");

		$page['content'] = html_entity_decode($page['content']);
		$page['css'] = html_entity_decode($page['css']);

		$page['alias'] = $this->url->getAlias('page/page', 'page_id=' . (int)$page_id);

		//Translations
		$translate_fields = array(
			'title',
			'meta_keywords',
			'meta_description',
			'content',
		);

		$page['translations'] = $this->translation->getTranslations('page', $page_id, $translate_fields);

		return $page;
	}

	public function getPages($data = array(), $select = null, $total = false)
	{
		//Select
		if ($total) {
			$select = 'COUNT(*) as total';
		} elseif (!$select) {
			$select = '*';
		}

		//From
		$from = DB_PREFIX . "page p";

		//Where
		$where = 'WHERE 1';

		if (isset($data['title'])) {
			$where .= " AND p.title like '%" . $this->escape($data['title']) . "%'";
		}

		if (!empty($data['stores'])) {
			$store_ids = is_array($data['stores']) ? $data['stores'] : array($data['stores']);

			$from .= " LEFT JOIN " . DB_PREFIX . "page_store ps ON (p.page_id=ps.page_id)";

			$where .= " AND ps.store_id IN (" . implode(',', $store_ids) . ")";
		}

		if (isset($data['status'])) {
			$where .= " AND p.status = '" . ($data['status'] ? 1 : 0) . "'";
		}

		//Order By & Limit
		if (!$total) {
			$order = $this->extractOrder($data);
			$limit = $this->extractLimit($data);
		} else {
			$order = '';
			$limit = '';
		}

		//The Query
		$sql = "SELECT $select FROM $from $where $order $limit";

		//Execute
		$result = $this->query($sql);

		//Process Results
		if ($total) {
			return $result->row['total'];
		}

		foreach ($result->rows as &$row) {
			$row['content'] = html_entity_decode($row['content']);
			$row['css'] = html_entity_decode($row['css']);
		}
		unset($row);

		return $result->rows;
	}

	public function getPageStores($page_id)
	{
		return $this->queryRows("SELECT * FROM " . DB_PREFIX . "page_store WHERE page_id = '" . (int)$page_id . "'");
	}

	public function getTotalPages($data = array())
	{
		return $this->getPages($data, null, true);
	}
}
