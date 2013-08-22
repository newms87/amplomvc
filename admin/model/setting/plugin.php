<?php
class Admin_Model_Setting_Plugin extends Model
{
	private $plugins;

	public function getPlugins($data = array(), $total = false)
	{
		if (!$this->plugins) {
			$this->plugins = array();

			$plugin_list = $this->queryRows("SELECT * FROM " . DB_PREFIX . "plugin");

			$plugin_dirs = scandir(DIR_PLUGIN);

			if ($plugin_dirs) {
				foreach ($plugin_dirs as $plugin_name) {
					if ($plugin_name == '.' || $plugin_name == '..') {
						continue;
					}

					$plugin = array_search_key('name', $plugin_name, $plugin_list);

					if (empty($plugin)) {
						$plugin = array(
							'name'      => $plugin_name,
							'installed' => 0,
							'status'    => 0,
						);
					} else {
						$plugin['installed'] = 1;
					}

					$setup_file = DIR_PLUGIN . $plugin['name'] . '/setup.php';
					$plugin += $this->tool->getFileCommentDirectives($setup_file);

					$plugin += array_fill_keys(array('title','description','date','author', 'version', 'dependencies', 'link'), '');

					if (!empty($plugin['dependencies'])) {
						$plugin['dependencies'] = explode(',', $plugin['dependencies']);
						array_walk($plugin['dependencies'], function(&$value){ $value = trim($value);});
					}

					//Add Plugin to list
					$this->plugins[$plugin['name']] = $plugin;
				}
			}

			foreach ($this->plugins as &$plugin) {
				$dependencies = array();

				if (!empty($plugin['dependencies'])) {
					foreach ($plugin['dependencies'] as $depend) {
						$required = array_search_key('name', $depend, $this->plugins);

						$dependencies[$depend] = !empty($required) && $required['installed'];
					}

					$plugin['dependencies'] = $dependencies;
				}
			} unset($plugin);
		}

		$plugins = array();

		//Apply Filters
		$filter_string = array(
			'name',
			'title',
			'author',
			'description',
		);

		foreach ($this->plugins as $name => $plugin) {
			foreach ($filter_string as $str) {
				if (!empty($data[$str])) {
					if (empty($plugin[$str]) || !preg_match("/$data[$str]/i", $plugin[$str])) {
						continue 2;
					}
				}
			}

			if (isset($data['status'])) {
				if ($data['status'] !== $plugin['status']) {
					continue;
				}
			}

			if (!empty($data['date'])) {
				if (!empty($data['date']['start']) && $this->date->isBefore($data['date']['start'], $plugin['date'])) {
					continue;
				}

				if (!empty($data['date']['end']) && $this->date->isAfter($this->date->add($data['date']['end'], '1 day'), $plugin['date'])) {
					continue;
				}
			}

			$plugins[$name] = $plugin;
		}

		if ($total) {
			return count($plugins);
		}

		if (!empty($data['sort'])) {
			usort($plugins, function($a,$b) use($data){
				if ($data['order'] === 'DESC') {
					return $a[$data['sort']] < $b[$data['sort']];
				} else {
					return $a[$data['sort']] > $b[$data['sort']];
				}
			});
		}

		if (!empty($data['limit'])) {
			$plugins = array_slice($plugins, (int)$data['limit'] * ((int)$data['page']-1), (int)$data['limit']);
		}

		return $plugins;
	}

	public function canInstall($name)
	{
		if (!isset($this->plugins[$name])) return false;

		$plugin = $this->plugins[$name];

		if (!empty($plugin['dependencies'])) {
			if (in_array(false, $plugin['dependencies'])) {
				return false;
			}
		}

		return true;
	}

	public function canUninstall($name)
	{
		foreach ($this->plugins as $plugin) {
			if ($plugin['installed'] && isset($plugin['dependencies'][$name])) {
				return false;
			}
		}

		return true;
	}

	public function getDependentsList($name)
	{
		$dependents = array();

		foreach ($this->plugins as $plugin) {
			if (isset($plugin['dependencies'][$name])) {
				$dependents[] = $plugin['name'];
			}
		}

		return $dependents;
	}

	public function getPluginData($name = false)
	{
		if (!empty($name)) {
			return $this->queryRow("SELECT * FROM " . DB_PREFIX . "plugin WHERE `name` ='" . $this->escape($name) . "'");
		}

		$plugins = $this->queryRows("SELECT * FROM " . DB_PREFIX . "plugin ORDER BY `name`");

		$plugin_data = array();

		foreach ($plugins as $plugin) {
			$plugin_data[$plugin['name']] = $plugin;
		}

		return $plugin_data;
	}

	public function updatePlugin($name, $plugin)
	{
		$this->update('plugin', $plugin, array('name' => $name));
	}

	public function deletePlugin($name, $plugin_path = null)
	{
		$where = array(
			'name' => $name
		);
		if ($plugin_path) {
			$where['plugin_path'] = $plugin_path;
		}

		$this->delete('plugin', $where);
	}

	public function getTotalPlugins($data = array())
	{
		return $this->getPlugins($data, true);
	}
}