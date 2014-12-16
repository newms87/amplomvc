<?php
class App_Model_Setting_UrlAlias extends Model
{
	public function addUrlAlias($data)
	{
		clear_cache('url_alias');

		$data['alias'] = $this->url->format($data['alias']);

		return $this->insert('url_alias', $data);
	}

	public function editUrlAlias($url_alias_id, $data)
	{
		clear_cache('url_alias');

		if (!empty($data['alias'])) {
			$data['alias'] = $this->url->format($data['alias']);
		}

		return $this->update('url_alias', $data, $url_alias_id);
	}

	public function deleteUrlAlias($url_alias_id)
	{
		clear_cache('url_alias');

		return $this->delete('url_alias', $url_alias_id);
	}

	public function getUrlAlias($url_alias_id)
	{
		return $this->queryRow("SELECT * FROM " . DB_PREFIX . "url_alias WHERE url_alias_id = " . (int)$url_alias_id);
	}

	public function getUrlAliasByAlias($alias)
	{
		return $this->queryRow("SELECT * FROM " . DB_PREFIX . "url_alias WHERE alias = " . (int)$url_alias_id);
	}

	public function getUniqueAlias($alias, $path, $query = '')
	{
		$alias = $this->escape($this->url->format($alias));

		$count = $this->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "url_alias WHERE alias like '$alias%' AND !(path = '" . $this->escape($path) . "' AND query = '" . $this->escape($query) . "')");

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
			$where .= " AND LCASE(ua.alias) like '%" . strtolower($this->escape($data['alias'])) . "%'";
		}

		if (!empty($data['path'])) {
			$where .= " AND LCASE(ua.path) like '" . strtolower($this->escape($data['path'])) . "%'";
		}

		if (!empty($data['query'])) {
			$where .= " AND LCASE(ua.query) like '%" . strtolower($this->escape($data['query'])) . "%'";
		}

		//Order By and Limit
		list($order, $limit) = $this->extractOrderLimit($data);

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
