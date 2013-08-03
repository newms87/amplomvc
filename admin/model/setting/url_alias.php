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
	
	public function getUrlAliases()
	{
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "url_alias ORDER BY alias");
		
		return $query->rows;
	}
}
