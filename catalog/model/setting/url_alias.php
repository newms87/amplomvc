<?php
class ModelSettingUrlAlias extends Model 
{
	public function setUrlAliasStatus($route, $query, $status)
	{
		$status = $status ? 1 : 0;
		
		$this->query("UPDATE " . DB_PREFIX . "url_alias SET status='$status' WHERE route = '" . $this->db->escape($route) . "' AND query = '" . $this->db->escape($query) . "'");
	}
}
