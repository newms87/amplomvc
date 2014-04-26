<?php

class Theme extends Library
{
	private $dir_themes;
	private $theme;
	private $default_theme = 'default';

	private $settings;

	public function __construct()
	{
		parent::__construct();

		$this->dir_themes = DIR_THEMES;
		$this->theme = $this->config->get('config_theme');

		if (!$this->theme || !is_dir(DIR_THEMES . $this->theme)) {
			$this->theme = $this->default_theme;
		}

		//Url Constants
		define('URL_THEME', URL_THEMES . $this->theme . '/');
		define('URL_THEME_IMAGE', URL_THEME . 'image/');
		define('URL_THEME_JS', URL_THEME . 'js/');

		//Directory Constants
		define('DIR_THEME', DIR_THEMES . $this->theme . '/');
		define('DIR_THEME_IMAGE', DIR_THEME . 'image/');
		define('DIR_THEME_JS', DIR_THEME . 'js/');

		if ($this->config->isAdmin()) {
			$this->settings = $this->loadAdminThemeSettings();
		} else {
			$theme_settings_file = $this->findFile('settings.php');

			$this->getThemeSettings($theme_settings_file);
		}
	}

	public function setTheme($theme)
	{
		$this->theme = $theme;
	}

	public function setThemesDirectory($dir)
	{
		$this->dir_themes = $dir;
	}

	public function getTheme()
	{
		return $this->theme;
	}

	public function getSetting($key)
	{
		if (isset($this->settings[$key])) {
			return $this->settings[$key];
		}

		return null;
	}

	private function getThemeSettings($theme_settings_file, $theme = false)
	{
		if (!$theme) {
			$theme = $this->theme;
		}

		if (is_file($theme_settings_file)) {
			$theme_settings = $this->cache->get('theme_settings.' . $theme);

			if (!$theme_settings || $theme_settings['mod_time'] != filemtime($theme_settings_file)) {

				$_ = array();

				require_once($theme_settings_file);

				$theme_settings = $_;

				$theme_settings['mod_time'] = filemtime($theme_settings_file);

				$this->cache->set('theme_settings.' . $theme, $theme_settings);
			}

			$this->settings = $theme_settings;

			return $this->settings;
		}

		return null;
	}

	private function loadAdminThemeSettings()
	{
		//We get the Themes here to validate the file modified times for caching
		$themes = $this->getThemes();

		$theme_settings_admin = $this->cache->get('theme_settings_admin');

		if (!$theme_settings_admin) {
			$_ = array();

			require_once(DIR_THEMES . $this->theme . '/settings.php');

			$theme_settings_admin = $_;

			//TODO - move this somewhere to make more easily dynamic (if we want to add other settings from the Themes)
			//We must load all the Themes' data for the admin
			$theme_settings_admin['data_positions'] = array();

			foreach ($themes as $theme) {
				$theme_settings_admin['data_positions'] += !empty($theme['settings']['data_positions']) ? $theme['settings']['data_positions'] : array();
			}

			$theme_settings_admin['themes'] = $themes;

			$this->cache->set('theme_settings_admin', $theme_settings_admin);
		}

		return $theme_settings_admin;
	}

	public function getThemes($admin = false)
	{
		if ($admin) {
			$theme_dir = DIR_SITE . 'admin/view/theme/';
		} else {
			$theme_dir = DIR_SITE . 'catalog/view/theme/';
		}

		$themes = $this->cache->get('themes' . ($admin ? '.admin' : ''));

		//invalidate all themes if one of the themes' settings has been updated
		if ($themes) {
			foreach ($themes as $theme) {
				$settings_file = $theme_dir . $theme['name'] . '/settings.php';
				if (is_file($settings_file) && filemtime($settings_file) != $theme['settings']['mod_time']) {
					$themes = false;
					$this->cache->delete('theme');
					break;
				}
			}
		}

		if (!$themes || true) {
			$dir_themes = glob($theme_dir . '*', GLOB_ONLYDIR);

			$themes = array();

			foreach ($dir_themes as $dir) {
				$name = basename($dir);

				$theme_settings_file = $theme_dir . $name . '/settings.php';

				$themes[$name] = array(
					'name'     => $name,
					'settings' => $this->getThemeSettings($theme_settings_file, $name),
				);
			}

			$this->cache->set('themes' . ($admin ? '.admin' : ''), $themes);
		}

		return $themes;
	}

	public function getTemplatesFrom($path, $admin = false, $blank_row = false)
	{
		if ($admin) {
			$root = DIR_SITE . 'admin/view/theme/';
		} else {
			$root = DIR_SITE . 'catalog/view/theme/';
		}

		$themes = $this->getThemes($admin);

		$templates = array();

		foreach ($themes as $theme_dir => $theme) {
			$dir = $root . $theme_dir . '/template/' . trim($path, '/') . '/';

			if (!is_dir($dir)) {
				continue;
			}

			$files = scandir($dir);

			$template_files = array();

			if ($blank_row !== false) {
				$template_files[''] = $blank_row;
			}

			foreach ($files as $file) {
				if (is_file($dir . $file) && preg_match("/\\.tpl$/", $file) > 0) {
					$filename                  = str_replace('.tpl', '', $file);
					$template_files[$filename] = $filename;
				}
			}

			$templates[$theme_dir] = $template_files;
		}

		return $templates;
	}

	public function findFile($file)
	{
		//Add tpl extension if no extension specified
		if (!preg_match("/\\.[a-z0-9]+\$/i", $file)) {
			$file .= '.tpl';
		}

		if (file_exists($this->dir_themes . $this->theme . '/' . $file)) {
			return $this->dir_themes . $this->theme . '/' . $file;
		} elseif (file_exists($this->dir_themes . $this->theme . '/template/' . $file)) {
			return $this->dir_themes . $this->theme . '/template/' . $file;
		} elseif (file_exists($this->dir_themes . $this->default_theme . '/template/' . $file)) {
			return $this->dir_themes . $this->default_theme . '/template/' . $file;
		}

		//File not found
		return false;
	}
}
