<?php

class App_Model_Site extends App_Model_Table
{
	protected $table = 'store', $primary_key = 'store_id';

	public function getSiteByName($site_name)
	{
		return $this->queryRow("SELECT * FROM " . DB_PREFIX . "store WHERE `name` = '" . $this->escape($site_name) . "'");
	}

	public function createSite($site, $tables = array())
	{
		if ($this->getSiteByName($site['name'])) {
			$this->error['name'] = _l("A site with the name %s already exists!", $site['name']);
			return false;
		}

		$site_id = $this->insert('store', $site);

		if (!$site_id) {
			return false;
		}

		if (!$tables) {
			$tables = array(
				'setting' => DB_PREFIX . 'setting',
			);
		}

		foreach ($tables as $base => $table_name) {
			$this->db->copyTable($table_name, $site['prefix'] . $base);
		}

		clear_cache_all();

		return $site_id;
	}

	public function removeSite($site_name)
	{
		$site = $this->getSiteByName($site_name);

		if (!$site) {
			$this->error['site'] = _l("A site with the name %s does not exist.", $site_name);
			return false;
		}

		$this->delete('store', $site['store_id']);

		if ($site['prefix'] !== DB_PREFIX) {
			$unique_prefix = $this->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "store WHERE `prefix` = '$site[prefix]'");

			if ($unique_prefix <= 1) {
				$col    = 'Tables_in_' . $this->db->getName();
				$tables = $this->queryRows("SHOW TABLES WHERE $col like '$site[prefix]%'");

				foreach ($tables as $table) {
					$this->db->dropTable(current($table));
				}
			}
		}

		clear_cache();

		return true;
	}
}
