<?php
class Admin_Model_Setting_Plugin extends Model
{
	public function getInstalledPlugins()
	{
		$plugins = $this->queryRows("SELECT * FROM " . DB_PREFIX . "plugin GROUP BY name");
		
		$installed = array();
		
		foreach ($plugins as $row) {
			$installed[$row['name']][] = $row;
		}
		
		return $installed;
	}
	
	public function getPluginData($name = false)
	{
		if (!empty($name)) {
			return $this->queryRow("SELECT * FROM " . DB_PREFIX . "plugin WHERE `name` ='" . $this->escape($name) . "'");
		}
		
		$plugins = $this->queryRows("SELECT * FROM " . DB_PREFIX . "plugin ORDER BY `name`");
		
		$plugin_data = array();
		
		foreach ($query->rows as $row) {
			$plugin_data[$row['name']] = $row;
		}
		
		return $plugin_data;
	}
	
	public function updatePlugin($name, $plugin)
	{
		$this->update('plugin', $plugin, array('name' => $name));
	}
	
	public function deletePlugin($name, $plugin_path=null)
	{
		$where = array(
			'name' => $name
		);
		if ($plugin_path) {
			$where['plugin_path'] = $plugin_path;
		}
		
		$this->delete('plugin', $where);
	}
}