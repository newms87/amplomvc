<?php
class ModelSettingExtension extends Model 
{
	public function getExtensions($type)
	{
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "extension WHERE `type` = '" . $this->db->escape($type) . "'");
		return $query->rows;
	}
}