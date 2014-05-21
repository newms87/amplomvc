<?php
class App_Model_Setting_Extension extends Model
{
	public function getExtensions($type)
	{
		return $this->queryRows("SELECT * FROM " . DB_PREFIX . "extension WHERE `type` = '" . $this->escape($type) . "' AND status = '1'");
	}

	public function getInstalled($type)
	{
		return $this->queryColumn("SELECT code FROM " . DB_PREFIX . "extension WHERE `type` = '" . $this->escape($type) . "'");
	}

	public function install($type, $code)
	{
		$ext = array(
			'type' => $type,
		   'code' => $code,
		);

		$this->insert('extension', $ext);
	}

	public function uninstall($type, $code)
	{
		$where = array(
			'type' => $type,
			'code' => $code,
		);

		$this->delete('extension', $where);
	}
}