<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC
 * @link http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYING.txt and LICENSE.txt files in the root directory.
 */

class App_Model_Site extends App_Model_Table
{
	protected $table = 'site', $primary_key = 'site_id';

	public function save($site_id, $site)
	{
		if (!$site_id) {
			return $this->createSite($site);
		}

		if (isset($site['prefix'])) {
			if (!preg_match("/[a-z0-9]+/", $site['prefix'])) {
				$this->error['prefix'] = _l("Prefix is required and must contain only letters, numbers and the '_' character.");

				return false;
			} else {
				$site['prefix'] = rtrim(slug($site['prefix']), '_') . '_';
			}
		}

		clear_cache_all();

		return parent::save($site_id, $site);
	}

	public function remove($site_id)
	{
		if (is_string($site_id) && preg_match("/[^\\d]/", $site_id)) {
			$site = $this->getSiteByName($site_id);
		} else {
			$site = $this->getRecord($site_id);
		}

		if (!$site) {
			$this->error['site'] = _l("The site %s does not exist.", $site_id);

			return false;
		}

		$this->delete('site', $site['site_id']);

		if (!empty($site['prefix']) && $site['prefix'] !== DB_PREFIX) {
			$unique_prefix = $this->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "site WHERE `prefix` = '$site[prefix]'");

			if (!$unique_prefix) {
				$col    = 'Tables_in_' . $this->db->getSchema();
				$tables = $this->queryRows("SHOW TABLES WHERE $col like '$site[prefix]%'");

				foreach ($tables as $table) {
					$this->db->dropTable(current($table));
				}
			}
		}

		clear_cache_all();

		//Reset Tables / Model for current request
		Model::$model     = array();
		$this->db->tables = array();

		return true;
	}

	public function getSiteByName($site_name)
	{
		return $this->queryRow("SELECT * FROM " . DB_PREFIX . "site WHERE `name` = '" . $this->escape($site_name) . "'");
	}

	public function getSiteByPrefix($prefix)
	{
		return $this->queryRow("SELECT * FROM " . DB_PREFIX . "site WHERE `prefix` = '" . $this->escape($prefix) . "'");
	}

	public function createSite($site, $tables = array())
	{
		if (empty($site['prefix']) || !preg_match("/[a-z0-9]+/", $site['prefix'])) {
			$this->error['prefix'] = _l("Prefix is required and must contain only letters, numbers and the '_' character.");

			return false;
		} else {
			$site['prefix'] = rtrim(slug($site['prefix']), '_') . '_';
		}

		if ($this->getSiteByName($site['name'])) {
			$this->error['name'] = _l("A site with the name %s already exists!", $site['name']);

			return false;
		}

		$site_id = $this->insert('site', $site);

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

		//Reset Tables / Model for current request
		Model::$model = array();
		$this->db->updateTables();

		return $site_id;
	}

	public function getLoginGroups()
	{
		$sort = array('name' => 'ASC');

		$filter = array('type' => 'login-group');

		$options = array(
			'cache' => true,
			'index' => 'category_id'
		);

		$login_groups = $this->Model_Category->getRecords($sort, $filter, $options);

		if (!$login_groups) {
			$group = array(
				'type' => 'login-group',
				'name' => 'Default',
			);

			$this->Model_Category->save(null, $group);

			$login_groups = $this->Model_Category->getRecords($sort, $filter, $options);
		}

		return $login_groups;
	}
}
