<?php

class Theme extends Library
{
	private $dir_themes;
	private $theme;
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

		$this->store_themes = array_merge(array($this->theme), $this->settings['parents']);

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

	public function getStoreThemes()
	{
		return $this->store_themes;
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
		//Add tpl extension if no extension specified
		if (!preg_match("/\\.[a-z0-9]+\$/i", $file)) {
			$file .= '.tpl';
		}

		//Resolve specified theme directory
		if ($theme) {
			if (file_exists($this->dir_themes . $theme . '/' . $file)) {
				return $theme . '/' . $file;
			} elseif (file_exists($this->dir_themes . $theme . '/template/' . $file)) {
				return $theme . '/template/' . $file;
			}
		}

		//Resolve the current store themes heirachically
		foreach ($this->store_themes as $store_theme) {
			if (file_exists($this->dir_themes . $store_theme . '/' . $file)) {
				return $store_theme . '/' . $file;
			} elseif (file_exists($this->dir_themes . $store_theme . '/template/' . $file)) {
				return $store_theme . '/template/' . $file;
			}
		}

		//File not found
		return false;
	}

	public function saveThemeConfigs($store_id, $theme, $configs)
	{
		$config_file = DIR_THEMES . $theme . '/css/config.less';

		$less = '';

		foreach ($configs as $config) {

		}
	}

	public function getThemeConfigs($store_id, $theme)
	{
		//Load Theme Configs
		$theme_list = $this->getThemeParents($theme);
		array_unshift($theme_list, $theme);

		$configs = array();

		foreach ($theme_list as $theme) {
			$config_file = DIR_THEMES . $theme . '/css/config.less';

			if (is_file($config_file)) {
				$configs += $this->getConfigs($config_file);
			}
		}

		//Load Store Configs
		$store_config_file = DIR_THEMES . $theme . '/css/config.store.' . $store_id . '.less';

		if (!is_file($store_config_file)) {
			touch($store_config_file);
		}

		$configs += $this->getConfigs($store_config_file);

		return $configs;
	}

	public function getConfigs($file)
	{
		$configs = array();

		//Prefix Less file with PHP tag for File Comment Directives.
		$directives = $this->tool->getCommentDirectives("<?php " . file_get_contents($file));

		$values = $this->parseConfigs($file);

		foreach ($directives as $key => $description) {
			$configs[$key] = array(
				'key'         => $key,
				'description' => $description,
				'value'       => isset($values[$key]) ? $values[$key] : '',
			);
		}

		return $configs;
	}

	public function parseConfigs($file)
	{
		preg_match_all("/(\\@[a-z_-]+):\\s*([^;\r\n]+);/i", file_get_contents($file), $matches);

		$configs = array();

		for ($i = 0; $i < count($matches[1]); $i++) {
			$configs[str_replace("@", '',$matches[1][$i])] = $matches[2][$i];
		}

		return $configs;
	}

	public function getThemeParents($theme)
	{
		$parents = array();

		while (is_file(DIR_THEMES . $theme . '/setup.php')) {
			$directives = $this->tool->getFileCommentDirectives(DIR_THEMES . $theme . '/setup.php');

			if (!empty($directives['parent'])) {
				//Check for Self parent assignment
				if ($theme === $directives['parent']) {
					break;
				}
				$theme     = $directives['parent'];
				$parents[] = $theme;
			} else {
				break;
			}
		}

		return $parents;
	}

	public function install($store_id, $theme)
	{
		$settings = array(
			'parents' => $this->getThemeParents($theme),
		);

		return $this->config->save('config', 'config_theme_settings', $settings, $store_id);
	}
}
