<?php
class Catalog_Model_Setting_Extension extends Model 
{
	public function getExtensions($type)
	{
		return $this->queryRows("SELECT * FROM " . DB_PREFIX . "extension WHERE `type` = '" . $this->db->escape($type) . "' AND status = '1'");
	}
}