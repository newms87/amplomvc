<?php

class Theme extends Library
{
	private $theme_hierarchy;
	private $theme;
	private $settings = array();

	public function __construct()
	{
		parent::__construct();

		$admin_theme = option('config_admin_theme', 'admin');
		$theme       = option('site_theme', AMPLO_DEFAULT_THEME);

		if (!is_dir(DIR_THEMES . $admin_theme)) {
			save_option('config_admin_theme', 'admin');
			$admin_theme = 'admin';
		}

		if (!is_dir(DIR_THEMES . $theme)) {
			save_option('site_theme', AMPLO_DEFAULT_THEME);
			$theme = AMPLO_DEFAULT_THEME;
		}

		if (IS_ADMIN) {
			$this->theme = $admin_theme;
		} else {
			$this->theme    = $theme;
			$this->settings = option('theme_settings', array());

			if (!$this->settings) {
				$this->install($this->theme);

				$this->settings = option('theme_settings', array());
			}
		}

		$this->settings += array(
			'parents' => array(),
		);

		$this->theme_hierarchy = array_merge(array($this->theme), $this->settings['parents']);

		//Url Constants
		define('URL_THEME', URL_THEMES . $this->theme . '/');

		//Directory Constants
		define('DIR_THEME', DIR_THEMES . $this->theme . '/');
	}

	public function setTheme($theme)
	{
		$this->theme = $theme;

		$this->theme_hierarchy = $this->getThemeParents($theme);

		array_unshift($this->theme_hierarchy, $theme);
	}

	public function getTheme()
	{
		return $this->theme;
	}

	public function getThemeHierarchy()
	{
		return $this->theme_hierarchy;
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

	public function getFile($path, $theme = '')
	{
		$file = $this->findFile($path, $theme);

		if ($file) {
			return DIR_THEMES . $file;
		}

		return false;
	}

	public function getUrl($path, $theme = '')
	{
		$file = $this->findFile($path, $theme);

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
			if (file_exists(DIR_THEMES . $theme . '/' . $file)) {
				return $theme . '/' . $file;
			}
		}

		//Resolve the current store themes heirachically
		foreach ($this->theme_hierarchy as $theme_node) {
			if (file_exists(DIR_THEMES . $theme_node . '/' . $file)) {
				return $theme_node . '/' . $file;
			}
		}

		//File not found
		return false;
	}

	public function getThemeStyle($sprite_nx = 3, $sprite_prefix = 'si-')
	{
		$theme = $this->theme;

		$cached_theme = 'less/theme.' . $theme;
		$theme_file   = cache($cached_theme, null, true);

		//import sprite sheet
		if ($sprite_nx && option('amplo_sprite_sheet', true)) {
			//Make sure sprite sheet is generated
			$this->getSpriteSheet($sprite_nx, $sprite_prefix);
		}

		if (!is_file($theme_file)) {
			$rel_dir = "app/view/theme/$theme/css/";

			$config_file     = _mod(DIR_SITE . $rel_dir . "config.less");
			$config_basename = basename($config_file);

			$theme_style = '';

			$theme_style .= "@import '@{base-path}{$rel_dir}{$config_basename}';\n\n";

			//import sprite sheet
			if ($sprite_nx && option('amplo_sprite_sheet', true)) {
				if ($this->checkSpriteSheet() || $this->getSpriteSheet($sprite_nx, $sprite_prefix, true)) {
					$theme_style .= "@import '@{base-path}{$rel_dir}_sprite.less';\n";
				}
			}

			$settings = $this->config->loadGroup('theme');

			if (!empty($settings['theme_config_' . $theme])) {
				$theme_style .= $settings['theme_config_' . $theme];

				if (!empty($settings['theme_style_' . $theme])) {
					$theme_style .= "\n\n/* Custom Theme Styles */\n" . $settings['theme_style_' . $theme];
				}
			}

			cache($cached_theme, $theme_style, true);

			//retrieve the absolute path of the cache file
			$theme_file = cache($cached_theme, null, true);
		}

		if ($theme_file) {
			return $this->document->compileLess($theme_file, $theme . '-theme-style');
		}

		return theme_url('css/style.css');
	}

	/**
	 * Check if the sprite sheet needs to be updated. (content added / removed)
	 *
	 * @return bool - False if the sprite sheet is out of date (or does not exist). True if the sprite sheet is valid
	 *              and up to date.
	 */
	public function checkSpriteSheet()
	{
		$css_file = DIR_THEME . 'css/_sprite.less';

		if (!is_file($css_file)) {
			return false;
		}

		$modified = filemtime($css_file);

		$theme_nodes   = $this->theme_hierarchy;
		$theme_nodes[] = '..';

		foreach ($theme_nodes as $theme_node) {
			$dir = DIR_THEMES . $theme_node . '/image/sprite/';

			if (is_dir($dir) && filemtime($dir) > $modified) {
				return false;
			}
		}

		return true;
	}

	//TODO: Implement a version of sprites, where css is generated but sprites are loaded as individual files. (Memory issues on smaller servers)

	public function getSpriteSheet($nx = 3, $prefix = 'si-', $refresh = false)
	{
		$css_file = DIR_THEME . 'css/_sprite.less';

		if ($refresh || !is_file($css_file)) {
			if (!_is_writable(dirname($css_file))) {
				return false;
			}

			$sprites = array();

			$theme_nodes   = $this->theme_hierarchy;
			$theme_nodes[] = '..';

			foreach ($theme_nodes as $theme_node) {
				$dir = DIR_THEMES . $theme_node . '/image/sprite/';

				if (is_dir($dir)) {
					$images = get_files($dir, 'png', FILELIST_STRING);

					foreach ($images as $image) {
						$ref = str_replace($dir, '', $image);

						if (!isset($sprites[$ref])) {
							$sprites[$ref] = $image;
						}
					}
				}
			}

			if (!$sprites || !$this->image->createSprite($css_file, $sprites, $nx, $prefix)) {
				return false;
			}
		}

		return $css_file;
	}

	public function refreshAllSpriteSheets()
	{
		$themes = $this->getThemes();

		foreach ($themes as $theme) {
			$sprite_file = $theme['dir'] . 'css/_sprite.less';

			if (is_file($sprite_file)) {
				unlink($sprite_file);
			}
		}

		return true;
	}

	public function saveTheme($theme, $configs, $stylesheet = null)
	{
		$config = '';

		foreach ($configs as $key => $value) {
			$config .= "@$key: $value;\n";
		}

		$site_theme = array(
			'site_theme_config_' . $theme => $config,
			'site_theme_style_' . $theme  => $stylesheet,
		);

		$this->config->saveGroup('site_theme', $site_theme, false);

		clear_cache('less/site_theme.' . $theme);
	}

	public function loadTheme()
	{
		$theme = option('site_theme');

		if (!$theme) {
			$theme = AMPLO_DEFAULT_THEME;
		}

		$configs = array();

		//Load Theme Configs
		$theme_list = $this->getThemeParents($theme);
		array_unshift($theme_list, $theme);

		foreach ($theme_list as $t) {
			$config_file = DIR_THEMES . $t . '/css/config.less';

			if (is_file($config_file)) {
				$configs += $this->getConfigs($config_file);
			}
		}

		//Load Store Configs
		$settings = $this->config->loadGroup('site_theme');

		if ($settings) {
			if (!empty($settings['site_theme_config_' . $theme])) {
				$theme_configs = $this->parseConfigs($settings['site_theme_config_' . $theme]);

				foreach ($theme_configs as $key => $value) {
					if (isset($configs[$key])) {
						$configs[$key]['value'] = $value;
					}
				}
			}

			$stylesheet = !empty($settings['site_theme_style_' . $theme]) ? $settings['site_theme_style_' . $theme] : '';

		} else {
			$stylesheet = '';
		}

		return array(
			'stylesheet' => $stylesheet,
			'configs'    => $configs,
		);
	}

	public function restore()
	{
		clear_cache('less/theme');

		return $this->config->deleteGroup('theme');
	}

	public function getConfigs($file)
	{
		$configs = array();

		//Prefix Less file with PHP tag for File Comment Directives.
		$directives = get_comment_directives("<?php " . file_get_contents($file));

		$values = $this->parseConfigs($file);

		foreach ($directives as $key => $description) {
			$title = cast_title($key);
			$type  = 'text';

			if (strpos($description, '---') === 0) {
				$type = 'section';
			} elseif (strpos($description, '-')) {
				list($title, $description) = explode('-', $description, 2);

				//Parse the type field (eg: 'Config Title (type)' or '(type)')
				if (preg_match("/\\s*([a-z\\s]*[a-z]?)\\s*\\(([^)]+)\\)/i", $title, $match)) {
					$title = $match[1] ? $match[1] : cast_title($key);
					$type  = $match[2];
				}

				$title = trim($title);
			}

			$configs[$key] = array(
				'key'         => $key,
				'title'       => $title,
				'type'        => $type,
				'description' => $description,
				'value'       => isset($values[$key]) ? $values[$key] : '',
			);
		}

		return $configs;
	}

	public function parseConfigs($file)
	{
		$contents = is_file($file) ? file_get_contents($file) : $file;

		preg_match_all("/(\\@[a-z_-]+):\\s*([^;\r\n]+);/i", $contents, $matches);

		$configs = array();

		for ($i = 0; $i < count($matches[1]); $i++) {
			$key           = str_replace("@", '', $matches[1][$i]);
			$configs[$key] = $matches[2][$i];
		}

		return $configs;
	}

	public function getThemeParents($theme)
	{
		$parents = array();

		while (is_file(DIR_THEMES . $theme . '/setup.php')) {
			$directives = get_comment_directives(DIR_THEMES . $theme . '/setup.php');

			if (!empty($directives['parent'])) {
				//Check for Self parent assignment
				if ($theme === $directives['parent']) {
					break;
				}
				$theme           = $directives['parent'];
				$parents[$theme] = $theme;
			} else {
				break;
			}
		}

		return $parents;
	}

	public function install($theme)
	{
		$settings = array(
			'parents' => $this->getThemeParents($theme),
		);

		return $this->config->save('general', 'theme_settings', $settings);
	}
}
