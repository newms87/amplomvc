<?php
class Admin_Model_Setting_Extension extends Model
{
	public function getInstalled($type)
	{
		return $this->queryColumn("SELECT code FROM " . DB_PREFIX . "extension WHERE `type` = '" . $this->escape($type) . "'");
	}

	public function install($type, $code)
	{
		$this->query("INSERT INTO " . DB_PREFIX . "extension SET `type` = '" . $this->escape($type) . "', `code` = '" . $this->escape($code) . "'");
	}

	public function uninstall($type, $code)
	{
		$this->query("DELETE FROM " . DB_PREFIX . "extension WHERE `type` = '" . $this->escape($type) . "' AND `code` = '" . $this->escape($code) . "'");
	}
}