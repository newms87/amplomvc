<?php

class App_Model_Plugin extends App_Model_Table
{
	protected $table = 'plugin', $primary_key = 'plugin_id';

	protected $plugins;

	public function save($plugin_id, $plugin)
	{
		if (empty($plugin['name'])) {
			$this->error['name'] = _l("Plugin name is required.");
		}

		if ($this->error) {
			return false;
		}

		if ($plugin_id) {
			$plugin_id = $this->update('plugin', $plugin, $plugin_id);
		} else {
			$this->delete('plugin', array('name' => $plugin['name']));

			$plugin_id = $this->insert('plugin', $plugin);
		}

		clear_cache('plugin');

		return $plugin_id;
	}

	public function remove($plugin_id)
	{
		clear_cache('plugin');

		return $this->delete('plugin', $plugin_id);
	}

	public function getPluginId($name)
	{
		return $this->queryVar("SELECT plugin_id FROM {$this->t['plugin']} WHERE `name` = '" . $this->escape($name) . "'");
	}

	public function getField($name, $field)
	{
		$id = preg_match("/[^\\d]/", $name) ? false : (int)$name;
		return $this->queryVar("SELECT `$field` FROM " . $this->t[$this->table] . " WHERE " . ($id ? "plugin_id = $id" : "`name` = '" . $this->escape($name) . "'"));
	}

	public function getPlugins($data = array(), $total = false)
	{
		if (!$this->plugins) {
			$this->plugins = array();

			$plugin_list = $this->queryRows("SELECT * FROM {$this->t['plugin']}");

			$plugin_dirs = scandir(DIR_PLUGIN);

			if ($plugin_dirs) {
				foreach ($plugin_dirs as $plugin_name) {
					if ($plugin_name === '.' || $plugin_name === '..' || !is_dir(DIR_PLUGIN . $plugin_name)) {
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
					$plugin += get_comment_directives($setup_file);

					$plugin += array_fill_keys(array(
						'title',
						'description',
						'date',
						'author',
						'version',
						'dependencies',
						'link'
					), '');

					if (!empty($plugin['dependencies'])) {
						$plugin['dependencies'] = explode(',', $plugin['dependencies']);
						array_walk($plugin['dependencies'], function (&$value) {
							$value = trim($value);
						});
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
			}
			unset($plugin);
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
				if (!empty($data['date']['start']) && $this->date->isAfter($data['date']['start'], $plugin['date'])) {
					continue;
				}

				if (!empty($data['date']['end']) && $this->date->isBefore($this->date->add($data['date']['end'], '1 day'), $plugin['date'])) {
					continue;
				}
			}

			$plugins[$name] = $plugin;
		}

		if ($total) {
			return count($plugins);
		}

		if (!empty($data['sort'])) {
			usort($plugins, function ($a, $b) use ($data) {
				if ($data['order'] === 'DESC') {
					return $a[$data['sort']] < $b[$data['sort']];
				} else {
					return $a[$data['sort']] > $b[$data['sort']];
				}
			});
		}

		if (!empty($data['limit'])) {
			$plugins = array_slice($plugins, (int)$data['limit'] * ((int)$data['page'] - 1), (int)$data['limit']);
		}

		return $plugins;
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
			return $this->queryRow("SELECT * FROM {$this->t['plugin']} WHERE `name` ='" . $this->escape($name) . "'");
		}

		$plugins = $this->queryRows("SELECT * FROM {$this->t['plugin']} ORDER BY `name`");

		$plugin_data = array();

		foreach ($plugins as $plugin) {
			$plugin_data[$plugin['name']] = $plugin;
		}

		return $plugin_data;
	}

	public function getTotalPlugins($data = array())
	{
		return $this->getPlugins($data, true);
	}

	public function searchPlugins($search = '', $team = 'amplomvc')
	{
		$plugins = cache('repoapi.plugins.' . $team);

		if ($plugins === null) {
			$response = $this->curl->get('https://api.bitbucket.org/2.0/repositories/' . $team, null, Curl::RESPONSE_JSON);

			if (empty($response['values'])) {
				$response = $this->curl->get('https://api.bitbucket.org/2.0/teams/' . $team . '/repositories', null, Curl::RESPONSE_JSON);

				if (empty($response['values'])) {
					return array();
				}
			}

			$plugins = $response['values'];

			foreach ($plugins as $key => &$plugin) {
				$plugin['download'] = $plugin['links']['html']['href'] . '/get/master.zip';
			}
			unset($plugin);

			cache('repoapi.plugins.' . $team, $plugins);
		}

		if ($search) {
			foreach ($plugins as $plugin) {
				if ($plugin['full_name'] === $search) {
					return $plugin;
				}
			}

			return false;
		}

		return $plugins;
	}

	public function downloadPlugin($name)
	{
		$plugin = $this->searchPlugins($name);

		if (!$plugin) {
			$this->error['name'] = _l("Unable to find the plugin %s. Please try downloading a different plugin.", $name);
			return false;
		}

		$zip_file = DIR_PLUGIN . 'plugin-download.zip';

		if (empty($plugin['download'])) {
			$this->error['source'] = _l("Unable to locate the source .zip file. %s", $plugin['description']);
			return false;
		}

		if (!$this->url->download($plugin['download'], $zip_file)) {
			$this->error = $this->url->getError();
			return false;
		}

		if (!$this->csv->extractZip($zip_file, DIR_PLUGIN . 'plugin-download')) {
			$this->error = $this->csv->getError();
			return false;
		}

		$dirs = glob(DIR_PLUGIN . 'plugin-download/*');

		if (count($dirs) === 1) {
			$entry = basename($dirs[0]);

			//Rename to working plugin name
			$setup_file = DIR_PLUGIN . 'plugin-download/' . $entry . '/setup.php';

			if (is_file($setup_file)) {
				$directives = get_comment_directives($setup_file);
			}

			$plugin_name = !empty($directives['name']) ? $directives['name'] : basename($name);

			if (is_file(DIR_PLUGIN . $plugin_name)) {
				$this->error['exists'] = _l("A plugin with the same name %s already exists!", $plugin_name);

			} elseif (!@rename(DIR_PLUGIN . 'plugin-download/' . $entry, DIR_PLUGIN . $plugin_name)) {
				$this->error['rename'] = _l("There was a problem renaming the plugin file. Maybe the plugin already exists?");
			}

			rrmdir(DIR_PLUGIN . 'plugin-download');

			//Cleanup
			unlink($zip_file);

			return $this->error ? false : $plugin_name;
		} else {
			trigger_error("NOT 100% sure how to handle this guy");
			exit;
		}
	}

	public function canInstall($name)
	{
		if (!$this->plugins) {
			$this->getPlugins();
		}

		if (!isset($this->plugins[$name])) {
			return false;
		}

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
		if (!$this->plugins) {
			$this->getPlugins();
		}

		foreach ($this->plugins as $plugin) {
			if ($plugin['installed'] && isset($plugin['dependencies'][$name])) {
				return false;
			}
		}

		return true;
	}
}
