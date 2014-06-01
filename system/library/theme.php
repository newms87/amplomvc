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

		$admin_theme        = option('config_admin_theme', 'admin');
		$admin_parent_theme = option('config_admin_parent_theme', 'admin');
		$theme              = option('config_theme', 'fluid');
		$parent_theme       = option('config_parent_theme', 'fluid');

		if (!is_dir(DIR_THEMES . $admin_theme)) {
			set_option('config_admin_theme', 'admin');
			$admin_theme = 'admin';
		}

		if (!is_dir(DIR_THEMES . $admin_parent_theme)) {
			set_option('config_admin_parent_theme', 'admin');
			$admin_parent_theme = 'admin';
		}

		if (!is_dir(DIR_THEMES . $theme)) {
			set_option('config_theme', 'fluid');
			$theme = 'fluid';
		}

		if (!is_dir(DIR_THEMES . $parent_theme)) {
			set_option('config_parent_theme', 'fluid');
			$parent_theme = 'fluid';
		}

		if ($this->route->isAdmin()) {
			$this->theme        = $admin_theme;
			$this->parent_theme = $admin_parent_theme;
		} else {
			$this->theme        = $theme;
			$this->parent_theme = $parent_theme;
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

	public function getThemes($filter = array(), $select = '*', $index = null)
	{
		$dir_themes = glob(DIR_THEMES . '*', GLOB_ONLYDIR);

		$themes = array();

		foreach ($dir_themes as $dir) {
			$name = basename($dir);

			$themes[$name] = array(
				'dir' => $dir . '/',
				'name' => $name,
			);
		}

		if ($index === false) {
			return count($themes);
		}

		if (!empty($filter['sort']['name'])) {
			$themes = uasort($themes, function($a, $b) use($filter) {
				if (!empty($filter['order']) && strtoupper($filter['order']) === 'DESC') {
					return $a['name'] > $b['name'];
				}

				return $a['name'] < $b['name'];
			});
		}

		return $themes;
	}

	public function getTotalThemes($filter)
	{
		return $this->getThemes($filter, '', false);
	}

	public function getTemplatesFrom($path, $blank_row = false)
	{
		$themes = $this->getThemes();

		$templates = array();

		foreach ($themes as $theme_dir => $theme) {
			$dir = $theme['dir'] . 'template/' . trim($path, '/') . '/';

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
		$area_files = glob(URL_SITE . 'app/controller/area/*');

		$areas = array();

		foreach ($area_files as $area) {
			$pos         = pathinfo($area, PATHINFO_FILENAME);
			$areas[$pos] = $pos;
		}

		return $areas;
	}

	public function getFile($file, $theme = null, $parent_theme = null)
	{
		$file = $this->findFile($file, $theme, $parent_theme);

		if ($file) {
			return $this->dir_themes . $file;
		}

		return false;
	}

	public function getUrl($file, $theme = null, $parent_theme = null)
	{
		$file = $this->findFile($file, $theme, $parent_theme);

		if ($file) {
			return URL_THEMES . $file;
		}

		return false;
	}

	public function findFile($file, $theme = null, $parent_theme = null)
	{
		$theme = $theme ? $theme : $this->theme;
		$parent_theme = $parent_theme ? $parent_theme : $this->parent_theme;

		//Add tpl extension if no extension specified
		if (!preg_match("/\\.[a-z0-9]+\$/i", $file)) {
			$file .= '.tpl';
		}

		if (file_exists($this->dir_themes . $theme . '/' . $file)) {
			return $theme . '/' . $file;
		} elseif (file_exists($this->dir_themes . $parent_theme . '/' . $file)) {
			return $parent_theme . '/' . $file;
		} elseif (file_exists($this->dir_themes . $theme . '/template/' . $file)) {
			return $theme . '/template/' . $file;
		} elseif (file_exists($this->dir_themes . $parent_theme . '/template/' . $file)) {
			return $parent_theme . '/template/' . $file;
		}

		//File not found
		return false;
	}
}
