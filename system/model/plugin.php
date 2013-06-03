<?php
class System_Model_Plugin extends Model{	
	public function install($name)
	{
		$this->cache->delete('plugin');
		
		$this->delete('plugin', array('name'=>$name));
		
		$plugin = array(
			'name' => $name,
			'status' => 1,
		);
		
		$plugin_id = $this->insert('plugin', $plugin);
		
		return $plugin_id;
	}

	public function uninstall($name)
	{
		$this->cache->delete('plugin');
		
		//remove files from plugin that were registered
		$plugin_entries = $this->query_rows("SELECT * FROM " . DB_PREFIX . "plugin_registry WHERE `name` = '" . $this->db->escape($name) . "'");
		
		foreach ($plugin_entries as $entry) {
			if (is_file($entry['live_file'])) {
				$this->message->add("notify", "removing plugin file $entry[live_file]");
				unlink($entry['live_file']);
			}
		}
		
		$this->delete('plugin_registry', array('name' => $name));
		
		$this->delete('plugin', array('name'=>$name));
	}
}