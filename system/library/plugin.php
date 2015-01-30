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

		if ($this->installed === null) {
			$this->installed = $this->queryRows("SELECT * FROM {$this->t['plugin']} WHERE status = 1", 'name');

			cache('plugin.installed', $this->installed);
		}

		if (IS_ADMIN) {
			$this->validatePluginModFiles();
		}
	}

	public function isInstalled($name)
	{
		return isset($this->installed[$name]);
	}

	public function loadPlugin($name)
	{
		if (!isset($this->plugins[$name])) {
			$plugin_class = 'Plugin_' . _2camel($name) . '_Setup';

			if (!class_exists($plugin_class)) {
				$file = DIR_THEMES . $name . '/setup.php';

				if (!is_file($file)) {
					$this->error['name'] = _l("Plugin %s does not exist!", $name);
				} else {
					$this->error['class_name'] = _l("Plugin Class %s did not exist. Make sure the class name is correct in %s.", $plugin_class, $file);
				}
				return false;
			}

			$this->plugins[$name] = new $plugin_class();
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
		$plugin = $this->loadPlugin($name);

		if (!$plugin) {
			$this->error['name'] = _l("Unable to load plugin %s for installation.", $name);
			return false;
		}

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

		if (method_exists($plugin, 'install')) {
			$plugin->install();
		}

		$this->Model_Plugin->install($name);

		//Run all upgrades
		if (method_exists($plugin, 'upgrade')) {
			$data = $plugin->upgrade('0');
			$this->Model_Plugin->upgrade($name, $data);
		}

		return true;
	}

	public function uninstall($name, $keep_data = true)
	{
		$plugin = $this->loadPlugin($name);

		$data = null;

		if (method_exists($plugin, 'uninstall')) {
			$data = $plugin->uninstall($keep_data);
		}

		//Uninstall the plugin from the system
		$this->Model_Plugin->uninstall($name, $data);

		$this->mod->removeDirectory(DIR_PLUGIN . $name);

		if ($this->mod->apply(true)) {
			$this->mod->write();

			message('notify', _l("%s has been uninstalled.", $name));
		} else {
			message('warning', $this->mod->getError());
		}

		return true;
	}

	public function hasUpgrade($name)
	{
		$directives = $this->getDirectives($name);

		if (isset($directives['version'])) {
			$version = $this->Model_Plugin->getField($name, 'version');

			if (version_compare($version, $directives['version']) === -1) {
				return $directives['version'];
			}
		}

		return false;
	}

	public function upgrade($name)
	{
		$plugin = $this->loadPlugin($name);

		if (!$plugin) {
			return false;
		}

		$data = null;

		$from_version = $this->Model_Plugin->getField($name, 'version');

		if (method_exists($plugin, 'upgrade')) {
			$data = $plugin->upgrade($from_version);
		}

		$version = $this->Model_Plugin->upgrade($name, $data);

		$this->integrateChanges($_GET['name']);

		return $from_version === $version ? true : $version;
	}

	public function getNewFiles($name)
	{
		$dir = DIR_PLUGIN . $name . '/new_files/';

		return get_files($dir, false, FILELIST_SPLFILEINFO, '^((?!\.git).)*$');
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
			$this->error['changes'] = _l("There are no updates for the plugin %s.", $name);
			return false;
		}

		foreach ($changes['new_files'] as $file) {
			$this->activatePluginFile($name, $file);
			message('notify', _l("Add New File: %s", $file));
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

		$this->query("DELETE FROM {$this->t['plugin_registry']} WHERE plugin_file = '" . $this->escape($data['plugin_file']) . "'");
		$this->query("INSERT INTO {$this->t['plugin_registry']} SET $values");

		clear_cache("plugin");

		return true;
	}

	private function loadPluginFileRegistry()
	{
		$this->plugin_registry = cache('plugin.registry');

		if (!$this->plugin_registry) {
			$query = $this->query("SELECT * FROM {$this->t['plugin_registry']}");

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

		$files = get_files($dir, false, FILELIST_STRING, '^((?!\.git).)*$');

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
		if (!is_dir(DIR_SITE . '.git')) {
			return true;
		}

		$file = '/' . str_replace(DIR_SITE, '', $file);

		$exclude_file = DIR_SITE . '.git/info/exclude';

		if (_is_writable(DIR_SITE . '.git/info/')) {
			$ignores = explode("\n", file_get_contents($exclude_file));

			foreach ($ignores as $ignore) {
				if ($ignore === $file) {
					return true;
				}
			}

			$ignores[] = $file;
		}

		return file_put_contents($exclude_file, implode("\n", $ignores));
	}

	public function getDirectives($name)
	{
		$setup_file = DIR_PLUGIN . $name . '/setup.php';

		if (is_file($setup_file)) {
			return get_comment_directives($setup_file);
		}

		return array();
	}
}
