<?php

class Theme extends Library
{
	private $dir_themes;
	private $theme;
	private $parent_theme;

	public function __construct()
	{
		parent::__construct();

		$this->dir_themes = DIR_THEMES;

		if ($this->config->isAdmin()) {
			$this->theme        = option('config_admin_theme', 'default');
			$this->parent_theme = option('config_admin_parent_theme', 'default');
		} else {
			$this->theme        = option('config_theme', 'fluid');
			$this->parent_theme = option('config_parent_theme', 'fluid');
		}


		if (!$this->theme || !is_dir(DIR_THEMES . $this->theme)) {
			$this->theme = $this->parent_theme;
		}

		//Url Constants
		define('URL_THEME', URL_THEMES . $this->theme . '/');
		define('URL_THEME_PARENT', URL_THEMES . $this->parent_theme . '/');

		//Directory Constants
		define('DIR_THEME', DIR_THEMES . $this->theme . '/');
		define('DIR_THEME_PARENT', DIR_THEMES . $this->parent_theme . '/');
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

	public function getThemes($admin = false)
	{
		if ($admin) {
			$theme_dir = DIR_SITE . 'admin/view/theme/';
		} else {
			$theme_dir = DIR_SITE . 'catalog/view/theme/';
		}

		$themes = $this->cache->get('themes' . ($admin ? '.admin' : ''));

		if (is_null($themes)) {
			$dir_themes = glob($theme_dir . '*', GLOB_ONLYDIR);

			$themes = array();

			foreach ($dir_themes as $dir) {
				$name = basename($dir);

				$themes[$name] = array(
					'name' => $name,
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

	public function getPositions()
	{
		$area_files = glob(URL_SITE . 'catalog/controller/area/*');

		$areas = array();

		foreach ($area_files as $area) {
			$pos         = pathinfo($area, PATHINFO_FILENAME);
			$areas[$pos] = $pos;
		}

		return $areas;
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
		} elseif (file_exists($this->dir_themes . $this->parent_theme . '/template/' . $file)) {
			return $this->dir_themes . $this->parent_theme . '/template/' . $file;
		}

		//File not found
		return false;
	}
}
