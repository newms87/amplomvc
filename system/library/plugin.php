<?php

class Plugin extends Library
{
	protected
		$plugins,
		$installed;

	public function isInstalled($name)
	{
		$this->installed = cache('plugin.installed');

		if ($this->installed === null) {
			$this->installed = $this->queryRows("SELECT * FROM {$this->t['plugin']} WHERE status = 1", 'name');

			cache('plugin.installed', $this->installed);
		}

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
		$instance = $this->loadPlugin($name);

		if (!$instance) {
			$this->error['name'] = _l("Unable to load plugin %s for installation.", $name);
			return false;
		}

		if (!$this->Model_Plugin->canInstall($name)) {
			$this->error['install'] = _l("The plugin %s cannot be installed.", $name);
			return false;
		}

		$directives = get_comment_directives(DIR_PLUGIN . $name . '/setup.php');

		$plugin = array(
			'name'    => $name,
			'version' => !empty($directives['version']) ? $directives['version'] : '1.0',
			'status'  => 1,
		);

		if (!$this->Model_Plugin->save(null, $plugin)) {
			$this->error = $this->Model_Plugin->getError();
			return false;
		}

		//New Files
		$files = $this->getNewFiles($name);

		foreach ($files as $file) {
			if (!$this->activatePluginFile($name, $file)) {
				$this->error['new_files'] = _l("There was a problem while adding new files for %s. The plugin has been uninstalled!", $name);
				$this->uninstall($name);
				return false;
			}
		}

		if (method_exists($instance, 'install')) {
			$instance->install();
		}

		//Run all upgrades
		if (method_exists($instance, 'upgrade')) {
			$instance->upgrade('0');
		}

		return true;
	}

	public function uninstall($name, $keep_data = true)
	{
		$instance = $this->loadPlugin($name);

		if (method_exists($instance, 'uninstall')) {
			$instance->uninstall($keep_data);
		}

		$plugin_id = $this->Model_Plugin->getPluginId($name);

		//Uninstall the plugin from the system
		$this->Model_Plugin->remove($plugin_id);

		//New Files
		$files = $this->getNewFiles($name);

		foreach ($files as $file) {
			$this->deactivatePluginFile($name, $file);
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
		$instance = $this->loadPlugin($name);

		if (!$instance) {
			return false;
		}

		$plugin_id = $this->Model_Plugin->getPluginId($name);

		if (!$plugin_id) {
			$this->error['install'] = _l("Plugin was not installed");
			return false;
		}

		$plugin = $this->Model_Plugin->getRecord($plugin_id, 'version');

		if (method_exists($instance, 'upgrade')) {
			$instance->upgrade($plugin['version']);
		}

		$directives = get_comment_directives(DIR_PLUGIN . $name . '/setup.php');

		$version = !empty($directives['version']) ? $directives['version'] : '1.0';

		if (!$this->Model_Plugin->save($plugin_id, array('version' => $version))) {
			return false;
		}

		$changes = $this->getChanges($name);

		foreach ($changes as $file) {
			if (!$this->activatePluginFile($name, $file)) {
				$this->error[$name] = _l("Failed updating change for %s", $file);
			}
		}

		return $plugin['version'] === $version ? true : $version;
	}

	public function getNewFiles($name)
	{
		return get_files(DIR_PLUGIN . $name . '/new_files/', false, FILELIST_SPLFILEINFO, '^((?!\.git).)*$');
	}

	public function hasChanges($name)
	{
		$changes = $this->getChanges($name);

		return !empty($changes);
	}

	public function getChanges($name)
	{
		$dir = DIR_PLUGIN . $name . '/new_files/';

		$files = $this->getNewFiles($name);

		foreach ($files as $key => &$file) {
			$file = str_replace('\\', '/', $file->getPathName());

			$live_file = str_replace($dir, DIR_SITE, $file);

			if (is_file($live_file)) {
				$ext = pathinfo($live_file, PATHINFO_EXTENSION);

				if ($ext !== 'mod' || filemtime($live_file) === filemtime($file)) {
					unset($files[$key]);
				}
			}
		}

		return $file;
	}

	public function activatePluginFile($name, $file)
	{
		$dir = DIR_PLUGIN . $name . '/new_files/';

		$plugin_file = is_object($file) ? str_replace("\\", "/", $file->getPathName()) : $file;
		$live_file   = str_replace($dir, DIR_SITE, $plugin_file);

		//Live file already exists! This is a possible conflict...
		//If it is not a registered plugin file for this plugin, ask admin what to do.
		if (is_file($live_file)) {
			//If no request to overwrite the live file
			if (_get('force_install') !== $name && _get('overwrite_file') !== $live_file) {
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

		//Generate the live file with the contents of the plugin file
		if (!_is_writable(dirname($live_file))) {
			$this->error = _l("%s(): Live File destination was not writable: %s", $live_file);
			return false;
		}

		if (is_file($live_file)) {
			@unlink($live_file);
		}

		$ext = pathinfo($plugin_file, PATHINFO_EXTENSION);

		if ($ext === 'mod') {
			if (!$this->mod->apply($plugin_file)) {
				$this->error = $this->mod->getError();
			}
		} elseif (!symlink($plugin_file, $live_file)) {
			$this->error['symlink'] = _l("There was an error while creating the symlink for %s to %s for plugin <strong>%s</strong>.", $plugin_file, $live_file, $name);
		}

		if ($this->error) {
			return false;
		}

		$this->gitIgnore($live_file);

		return true;
	}

	public function deactivatePluginFile($name, $file)
	{
		$dir = DIR_PLUGIN . $name . '/new_files/';

		$plugin_file = is_object($file) ? str_replace("\\", "/", $file->getPathName()) : $file;
		$live_file   = str_replace($dir, DIR_SITE, $plugin_file);

		if (is_file($live_file)) {
			if (filemtime($live_file) !== filemtime($plugin_file)) {
				$this->error[$file] = _l("Either the file has been modified or does not belong to this plugin");
				return false;
			}

			@unlink($live_file);

			$this->gitIgnore($live_file, true);
		}

		return true;
	}

	public function gitIgnore($file, $remove = false)
	{
		if (!is_dir(DIR_SITE . '.git')) {
			return true;
		}

		$file = '/' . str_replace(DIR_SITE, '', $file);

		$exclude_file = DIR_SITE . '.git/info/exclude';

		if (_is_writable(DIR_SITE . '.git/info/')) {
			$ignores = explode("\n", file_get_contents($exclude_file));

			if ($remove) {
				foreach ($ignores as $key => $ignore) {
					if ($ignore === $file) {
						unset($ignores[$key]);
						break;
					}
				}
			} else {
				foreach ($ignores as $key => $ignore) {
					if ($ignore === $file) {
						return true;
					}
				}

				$ignores[] = $file;
			}
		}

		return file_put_contents($exclude_file, implode("\n", $ignores));
	}

	public function getDirectives($name)
	{
		return get_comment_directives(DIR_PLUGIN . $name . '/setup.php');
	}
}
