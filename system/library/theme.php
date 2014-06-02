<?php

class Theme extends Library
{
	private $dir_themes;
	private $theme;
	private $parent_theme;
	private $settings;

	public function __construct()
	{
		parent::__construct();

		$this->dir_themes = DIR_THEMES;

		$admin_theme = option('config_admin_theme', 'admin');
		$theme       = option('config_theme', 'fluid');

		if (!is_dir(DIR_THEMES . $admin_theme)) {
			set_option('config_admin_theme', 'admin');
			$admin_theme = 'admin';
		}

		if (!is_dir(DIR_THEMES . $theme)) {
			set_option('config_theme', 'fluid');
			$theme = 'fluid';
		}

		$this->theme    = $this->route->isAdmin() ? $admin_theme : $theme;
		$this->settings = option('config_theme_settings', array());

		$this->settings += array(
			'parents' => array(),
		);

		//Url Constants
		define('URL_THEME', URL_THEMES . $this->theme . '/');

		//Directory Constants
		define('DIR_THEME', DIR_THEMES . $this->theme . '/');
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
				'dir'  => $dir . '/',
				'name' => $name,
			);
		}

		if ($index === false) {
			return count($themes);
		}

		if (!empty($filter['sort']['name'])) {
			$themes = uasort($themes, function ($a, $b) use ($filter) {
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

	public function getFile($file, $theme = '')
	{
		$file = $this->findFile($file, $theme);

		if ($file) {
			return $this->dir_themes . $file;
		}

		return false;
	}

	public function getUrl($file, $theme = '')
	{
		$file = $this->findFile($file, $theme);

		if ($file) {
			return URL_THEMES . $file;
		}

		return false;
	}

	public function findFile($file, $theme = '')
	{
		$theme = $theme ? $theme : $this->theme;

		//Add tpl extension if no extension specified
		if (!preg_match("/\\.[a-z0-9]+\$/i", $file)) {
			$file .= '.tpl';
		}

		if ($theme) {
			if (file_exists($this->dir_themes . $theme . '/' . $file)) {
				return $theme . '/' . $file;
			} elseif (file_exists($this->dir_themes . $theme . '/template/' . $file)) {
				return $theme . '/template/' . $file;
			}
		}

		foreach ($this->settings['parents'] as $parent) {
			if (file_exists($this->dir_themes . $parent . '/' . $file)) {
				return $parent . '/' . $file;
			} elseif (file_exists($this->dir_themes . $parent . '/template/' . $file)) {
				return $parent . '/template/' . $file;
			}
		}

		//File not found
		return false;
	}

	public function install($store_id, $theme)
	{
		//Resolve Parents
		$parents = array();
		$t = $theme;

		while(is_file(DIR_THEMES . $t . '/setup.php')) {
			$directives = $this->tool->getFileCommentDirectives(DIR_THEMES . $t . '/setup.php');

			if (!empty($directives['parent'])) {
				//Check for Self parent assignment
				if ($t === $directives['parent']) {
					break;
				}
				$t = $directives['parent'];
				$parents[] = $t;
			} else {
				break;
			}
		}

		$settings = array(
			'parents' => $parents,
		);

		return $this->config->save('config', 'config_theme_settings', $settings, $store_id);
	}
}
