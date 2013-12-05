<?php
class Admin_Model_Setting_UrlAlias extends Model
{
	public function addUrlAlias($data)
	{
		$data['alias'] = $this->url->format($data['alias']);

		if (empty($data['store_id'])) {
			$data['store_id'] = 0;
		}

		return $this->insert('url_alias', $data);
	}

	public function editUrlAlias($url_alias_id, $data)
	{
		if (!empty($data['alias'])) {
			$data['alias'] = $this->url->format($data['alias']);
		}

		$this->update('url_alias', $data, $url_alias_id);
	}

	public function deleteUrlAlias($url_alias_id)
	{
		$this->delete('url_alias', $url_alias_id);
	}

	public function getUrlAlias($url_alias_id)
	{
		return $this->queryRow("SELECT * FROM " . DB_PREFIX . "url_alias WHERE url_alias_id = " . (int)$url_alias_id);
	}

	public function getUrlAliasByAlias($alias)
	{
		return $this->queryRow("SELECT * FROM " . DB_PREFIX . "url_alias WHERE alias = " . (int)$url_alias_id);
	}

	public function getUniqueAlias($alias, $path, $query = '', $store_id = 0)
	{
		$alias = $this->escape($this->url->format($alias));

		$count = $this->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "url_alias WHERE alias like '$alias%' AND !(path = '" . $this->escape($path) . "' AND query = '" . $this->escape($query) . "' AND store_id = " . (int)$store_id . ")");

		if ($count) {
			$alias .= '-' . $count;
		}

		return $alias;
	}

	public function getUrlAliases($data = array(), $select = '', $total = false)
	{
		//Select
		if ($total) {
			$select = "COUNT(*) as total";
		} elseif (empty($select)) {
			$select = '*';
		}

		//From
		$from = DB_PREFIX . "url_alias ua";

		//Where
		$where = "1";

		if (!empty($data['alias'])) {
			$where .= " AND LCASE(ua.alias) like '%" . strtolower($this->db->escape($data['alias'])) . "%'";
		}

		if (!empty($data['path'])) {
			$where .= " AND LCASE(ua.path) like '" . strtolower($this->db->escape($data['path'])) . "%'";
		}

		if (!empty($data['query'])) {
			$where .= " AND LCASE(ua.query) like '%" . strtolower($this->db->escape($data['query'])) . "%'";
		}

		if (isset($data['store_id'])) {
			if ((int)$data['store_id'] === 0) {
				$where .= " AND ua.store_id >= 0";
			} else {
				$where .= " AND ua.store_id = " . (int)$data['store_id'];
			}
		}

		//Order By and Limit
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

	public function getTotalUrlAliases($data = array())
	{
		return $this->getUrlAliases($data, '', true);
	}

}
