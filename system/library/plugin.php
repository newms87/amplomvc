<?php
class Plugin extends Library
{
	private $plugins;
	private $plugin_registry;
	private $installed;

	function __construct()
	{
		parent::__construct();

		global $registry;
		$registry->set('plugin', $this);

		$this->installed = cache('plugin.installed');

		if (is_null($this->installed)) {
			$this->installed = $this->queryColumn("SELECT * FROM " . DB_PREFIX . "plugin WHERE status = 1");

			cache('plugin.installed', $this->installed);
		}

		if (IS_ADMIN) {
			$this->validatePluginModFiles();
		}
	}

	public function isInstalled($name)
	{
		return !in_array($name, $this->installed);
	}

	public function loadPlugin($name)
	{
		if (!isset($this->plugins[$name])) {
			$setup_file = DIR_PLUGIN . $name . '/setup.php';

			if (!is_file($setup_file)) {
				message("warning", _l("The plugin setup file was not found at %s. Please make a setup.php file in the root of the %s plugin directory!", $setup_file, $name));

				return false;
			}

			require_once(_mod(DIR_PLUGIN . 'setup.php'));
			require_once(_mod($setup_file));

			$user_class = preg_replace("/[^A-Z0-9]/i", "", $name) . '_Setup';

			$this->plugins[$name] = new $user_class();
		}

		return $this->plugins[$name];
	}

	/**
	 * Install a plugin
	 *
	 * @param $name - The name of the directory for the plugin to install
	 * @return Boolean - true on success, or false on failure (error message will be set)
	 */

	public function install($name)
	{
		$this->cache->delete('plugin');
		$this->cache->delete('model');

		$plugin = $this->loadPlugin($name);

		if (method_exists($plugin, 'install')) {
			$plugin->install();
		}

		$this->System_Model_Plugin->install($name);

		//New Files
		if (!$this->integrateNewFiles($name)) {
			$this->error['new_files'] = _l("There was a problem while adding new files for %s. The plugin has been uninstalled!", $name);
			$this->uninstall($name);
			return false;
		}

		//File Modifications
		$file_mods = $this->getFileMods($name);

		if ($file_mods === false) {
			$this->error['mod_files'] = _l("There was a problem while applying file modifications for %s. The plugin has been uninstalled!", $name);
			$this->uninstall($name);
			return false;
		}

		$this->mod->addFiles(null, $file_mods);

		if (!$this->mod->apply(true)) {
			$this->error['mod_apply'] = $this->mod->getError();
			$this->uninstall($name);
			return false;
		}

		if (!$this->mod->write()) {
			$this->error['mod_write'] = $this->mod->getError();
			$this->uninstall($name);
			return false;
		}

		return true;
	}

	public function uninstall($name, $keep_data = true)
	{
		$this->cache->delete('plugin');
		$this->cache->delete('model');

		$plugin = $this->loadPlugin($name);

		$data = null;

		if (method_exists($plugin, 'uninstall')) {
			$data = $plugin->uninstall($keep_data);
		}

		//Uninstall the plugin from the system
		$this->System_Model_Plugin->uninstall($name, $data);

		$this->mod->removeDirectory(DIR_PLUGIN . $name);

		if ($this->mod->apply(true)) {
			$this->mod->write();

			message('notify', _l("%s has been uninstalled.", $name));
		} else {
			message('warning', $this->mod->getError());
		}

		return true;
	}

	public function getNewFiles($name)
	{
		$dir = DIR_PLUGIN . $name . '/new_files/';

		return $this->tool->getFiles($dir, false, FILELIST_SPLFILEINFO, '^((?!\.git).)*$');
	}

	public function integrateNewFiles($name)
	{
		$files = $this->getNewFiles($name);

		foreach ($files as $file) {
			if (!$this->activatePluginFile($name, $file)) {
				return false;
			}
		}

		return true;
	}

	public function hasChanges($name)
	{
		$changes = $this->getChanges($name);

		return !empty($changes['new_files']) || !empty($changes['mod_files']);
	}

	public function getChanges($name)
	{
		if (!$this->plugin_registry) {
			$this->loadPluginFileRegistry();
		}

		$changes = array(
			'new_files' => array(),
			'mod_files' => array(),
		);

		$files = $this->getNewFiles($name);

		foreach ($files as $key => $file) {
			$filepath = str_replace('\\', '/', $file->getPathName());
			if (!array_search_key('plugin_file', $filepath, $this->plugin_registry)) {
				$changes['new_files'][] = $filepath;
			}
		}

		$mod_files = $this->getFileMods($name);

		foreach ($mod_files as $mod => $file) {
			if ($this->mod->isRegistered($file)) {
				unset($mod_files[$mod]);
			}
		}

		$changes['mod_files'] = $mod_files;

		return $changes;
	}

	public function integrateChanges($name)
	{
		$changes = $this->getChanges($name);

		if (empty($changes['new_files']) && empty($changes['mod_files'])) {
			message('notify', _l("No Changes Were Made"));
			return false;
		}

		foreach ($changes['new_files'] as $file) {
			$this->activatePluginFile($name, $file);
			message('success', _l("Add New File: %s", $file));
		}

		$this->mod->addFiles(null, $changes['mod_files']);

		if (!empty($changes['mod_files'])) {
			if ($this->mod->apply()) {
				foreach ($changes['mod_files'] as $file) {
					message('notify', _l("Integrate Mod File: %s", $file));
				}

				$this->mod->write();
			} else {
				message('warning', $this->mod->getError());
				message('warning', _l("Failed while integrating the mod file changes!"));
				return false;
			}
		}

		message('success', _l("Successfully Integrated the Changes for %s!", $name));

		return true;
	}

	public function activatePluginFile($name, $file)
	{
		if (!$this->plugin_registry) {
			$this->loadPluginFileRegistry();
		}

		$dir = DIR_PLUGIN . $name . '/new_files/';

		$plugin_file = is_object($file) ? str_replace("\\", "/", $file->getPathName()) : $file;
		$live_file   = str_replace($dir, DIR_SITE, $plugin_file);

		//Live file already exists! This is a possible conflict...
		//If it is not a registered plugin file for this plugin, ask admin what to do.
		if (is_file($live_file) && (!isset($this->plugin_registry[$live_file]) || $this->plugin_registry[$live_file]['name'] !== $name)) {
			//If no request to overwrite the live file
			if ((empty($_GET['force_install']) || $_GET['force_install'] !== $name) && (empty($_GET['overwrite_file']) || $_GET['overwrite_file'] !== $live_file)) {
				$conflicting_plugin = isset($this->plugin_registry[$live_file]) ? $this->plugin_registry[$live_file] : null;

				if ($conflicting_plugin) {
					if ($conflicting_plugin['name'] !== $name) {
						message('warning', _l("There is a conflict with the <strong>%s</strong> plugin for the file %s. Please uninstall <strong>%s</strong> or resolve the conflict.", $conflicting_plugin['name'], $live_file, $conflicting_plugin['name']));
						return false;
					}
				} else {
					$overwrite_file_url = site_url($this->route->getPath(), $this->url->getQuery() . "&name=$name&overwrite_file=" . urlencode($live_file));
					$force_install_url  = site_url($this->route->getPath(), $this->url->getQuery() . "&name=$name&force_install=$name");

					$msg =
						_l("Unable to integrate the file %s for the plugin <strong>%s</strong> because the file %s already exists!", $plugin_file, $name, $live_file) .
						_l(" Either manually remove the file or <a href=\"%s\">overwrite</a> this file with the plugin file.<br /><br />", $overwrite_file_url) .
						_l("To overwrite all files for this plugin installation <a href=\"%s\">click here</a><br />", $force_install_url);

					message("warning", $msg);
					return false;
				}
			}
		}

		//Generate the live file with the contents of the plugin file
		if (!_is_writable(dirname($live_file))) {
			$this->error = _l("%s(): Live File destination was not writable: %s", $live_file);
			return false;
		}

		if (is_file($live_file)) {
			@unlink($live_file);
		}

		if (!symlink($plugin_file, $live_file)) {
			message("warning", "There was an error while copying $plugin_file to $live_file for plugin <strong>$name</strong>.");
			return false;
		}

		$this->gitIgnore($live_file);

		$data = array(
			'name'        => $name,
			'date_added'  => $this->date->now(),
			'live_file'   => $live_file,
			'plugin_file' => $plugin_file,
		);

		$values = '';

		foreach ($data as $key => $value) {
			$values .= ($values ? ',' : '') . "`$key`='" . $this->escape($value) . "'";
		}

		$this->query("DELETE FROM " . DB_PREFIX . "plugin_registry WHERE plugin_file = '" . $this->escape($data['plugin_file']) . "'");
		$this->query("INSERT INTO " . DB_PREFIX . "plugin_registry SET $values");

		$this->cache->delete("plugin");

		return true;
	}

	private function loadPluginFileRegistry()
	{
		$this->plugin_registry = cache('plugin.registry');

		if (!$this->plugin_registry) {
			$query = $this->query("SELECT * FROM " . DB_PREFIX . "plugin_registry");

			$this->plugin_registry = array();

			foreach ($query->rows as &$row) {
				$this->plugin_registry[$row['live_file']] = $row;
			}

			cache('plugin.registry', $this->plugin_registry);
		}
	}

	public function getFileMods($name)
	{
		$dir = DIR_PLUGIN . $name . '/file_mods';

		if (!is_dir($dir)) {
			return array();
		}

		$files = $this->tool->getFiles($dir, false, FILELIST_STRING, '^((?!\.git).)*$');

		$file_mods = array();

		foreach ($files as $file) {
			$file = str_replace('\\', '/', $file); //Fix for Windows

			$rel_file = substr(str_replace($dir, '', $file), 1);

			if (is_file(DIR_SITE . $rel_file)) {
				$file_mods[DIR_SITE . $rel_file] = $file;
				continue;
			}
		}

		return $file_mods;
	}

	public function validatePluginModFiles()
	{
		if ($mod_files = $this->mod->getModFiles(DIR_PLUGIN)) {
			$plugins = array();

			foreach ($mod_files as $mod_file) {
				$plugins[] = preg_replace("/.*?\\/plugin\\/(.*?)\\/.*/", '$1', $mod_file);
			}

			$plugins = array_unique($plugins);

			$valid = true;

			foreach ($plugins as $plugin) {
				if (!$this->isInstalled($plugin)) {
					$valid = false;
					$this->mod->removeDirectory(DIR_PLUGIN . $plugin);
				}
			}

			if (!$valid) {
				$this->mod->apply();
				$this->mod->write(); //no mod->apply validation because we must get rid erroneous plugin files
			}
		}
	}

	public function gitIgnore($file)
	{
		$file = '/' . str_replace(DIR_SITE, '', $file);

		$exclude_file = DIR_SITE . '.git/info/exclude';

		if (is_dir(DIR_SITE . '.git') && _is_writable(DIR_SITE . '.git/info/')) {
			$ignores = explode("\n",file_get_contents($exclude_file));

			foreach ($ignores as $ignore) {
				if ($ignore === $file) {
					return true;
				}
			}

			$ignores[] = $file;
		}

		return file_put_contents($exclude_file, implode("\n", $ignores));
	}
}
