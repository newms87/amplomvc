<?php

class Document extends Library
{
	private
		$info = array(),
		$meta = array(),
		$links = array(),
		$styles = array(),
		$scripts = array();

	function __construct()
	{
		parent::__construct();

		$this->links = $this->Model_Navigation->getNavigationGroup(IS_ADMIN ? 'admin' : 'all');

		//In case something happened to the admin navigation, we reset it
		if (IS_ADMIN && empty($this->links['admin']['links'])) {
			$this->Model_Navigation->resetAdminNavigationGroup();
			$this->links = $this->Model_Navigation->getNavigationGroup('admin');
		}

		$this->info['title']          = option('site_title');
		$this->meta['description']    = option('site_meta_description');
		$this->info['canonical_link'] = site_url($this->route->getPath(), $_GET);

		if (defined('AMPLO_PRODUCTION') && AMPLO_PRODUCTION) {
			if (option('minify_js_files') === null) {
				set_option('minify_js_files', true);
			}
		}
	}

	public function info($key = null, $default = null)
	{
		if ($key) {
			return isset($this->info[$key]) ? $this->info[$key] : $default;
		}

		return $this->info;
	}

	public function setInfo($key, $value)
	{
		$this->info[$key] = $value;
	}

	public function &infoRef()
	{
		return $this->info;
	}

	public function meta($key = null, $default = null)
	{
		if ($key) {
			return isset($this->meta[$key]) ? $this->meta[$key] : $default;
		}

		return $this->meta;
	}

	public function setMeta($key, $value)
	{
		$this->meta[$key] = $value;
	}

	public function hasLink($group = 'primary', $link_name)
	{
		if (!empty($this->links[$group])) {
			$result = array_walk_children($this->links[$group]['links'], 'children', function ($link) use ($link_name) {
				if (!empty($link) && $link_name === $link['name']) {
					return false;
				}
			});

			return $result === false;
		}

		return false;
	}

	public function hasLinks($group = 'primary')
	{
		return !empty($this->links[$group]['links']);
	}

	public function addLink($group = 'primary', $link)
	{
		if (empty($link['name'])) {
			$this->error['name'] = _l("You must provide a link name!");

			return false;
		}

		$defaults = array(
			'name'         => null,
			'display_name' => '',
			'path'         => '',
			'query'        => null,
			'title'        => '',
			'class'        => array(),
			'sort_order'   => null,
			'parent_id'    => 0,
			'parent'       => '',
			'target'       => '',
			'children'     => array(),
		);

		$link += $defaults;

		if (!empty($link['path']) || !empty($link['query'])) {
			$link['href'] = site_url($link['path'], $link['query']);
		}

		//If group doesn't exist, make a new group
		if (!isset($this->links[$group])) {
			$this->links[$group] = array(
				'navigation_group_id' => 0,
				'links'               => array(
					$link['name'] => $link,
				),
				'name'                => $group,
			);

			return true;
		}

		//Find the children list for the parent
		if ($link['parent'] || $link['parent_id']) {
			$return = array_walk_children($this->links[$group]['links'], 'children', function (&$l) use ($link) {
				if (empty($l)) {
					return;
				}
				if ($link['parent'] === $l['name']) {
					$l['children'][$link['name']] = $link;

					return false;
				} elseif (!empty($l['navigation_id']) && $link['parent_id'] === $l['navigation_id']) {
					$l['children'][$link['name']] = $link;

					return false;
				}
			});

			//$return === false when link is found
			if ($return !== false) {
				$this->error['parent'] = _l("Unable to locate parent link %s in Link Group %s", $link['parent'], $group);

				return false;
			}
		} else {
			$this->links[$group]['links'][$link['name']] = $link;
		}

		return true;
	}

	public function addLinks($group, $links)
	{
		foreach ($links as $link) {
			$this->addLink($group, $link);

			if (!empty($link['children'])) {
				$this->addLinks($group, $link['children']);
			}
		}
	}

	public function setLinks($group, $links)
	{
		$this->links[$group]['links'] = $links;
	}

	public function removeLink($group, $name, &$links = null)
	{
		if (!$links) {
			if (empty($this->links[$group]['links'])) {
				return false;
			}

			$links = &$this->links[$group]['links'];
		}

		foreach ($links as $key => &$link) {
			if ($link['name'] === $name) {
				unset($links[$key]);

				return true;
			} elseif (!empty($link['children'])) {
				if ($this->removeLink($group, $name, $link['children'])) {
					return true;
				}
			}
		}

		return false;
	}

	public function getLinks($group = 'primary')
	{
		if (isset($this->links[$group])) {
			return $this->links[$group]['links'];
		}

		return array();
	}

	//TODO: Find a functioning compiler for Sass / Scss
	public function compileSass($file, $reference, $syntax = 'scss', $style = 'nested')
	{
		if (!is_file($file)) {
			return null;
		}

		$mtime = filemtime($file);

		$sass_file = DIR_CACHE . 'sass/' . $reference . $mtime . '.css';

		//If file is not in cached, or original file has been modified, regenerate the SASS file
		if (!is_file($sass_file) || filemtime($sass_file) < $mtime) {

			//Cleared cached files for this reference
			$cached_files = glob(DIR_CACHE . 'sass/' . $reference . '*.css');
			foreach ($cached_files as $cache) {
				@unlink($cache);
			}

			//Load PHPSass
			require_once(DIR_RESOURCES . 'css/phpsass/SassParser.php');

			$options = array(
				'style'     => $style,
				'cache'     => false,
				'syntax'    => $syntax,
				'debug'     => false,
				'callbacks' => array(
					'warn'  => function ($message, $context) {
						echo $context . ': ' . $message;
					},
					'debug' => function ($message) {
						echo $message;
					},
				),
			);

			// Execute the compiler.
			$parser = new SassParser($options);
			$css    = $parser->toCss($file);

			//Write cache file
			_is_writable(dirname($sass_file));
			file_put_contents($sass_file, $css);
		}

		//Return the URL for the cache file
		return str_replace(DIR_CACHE, URL_SITE . 'system/cache/', $sass_file);
	}

	public function compileLess($file, $reference, $refresh = false)
	{
		if (!is_file($file)) {
			return null;
		}

		$mtime = _filemtime($file);

		$less_file = DIR_CACHE . 'less/' . $reference . '.' . $mtime . '.css';

		if (!$refresh && is_file($less_file)) {
			//Check Less @imports for modifications
			$dependencies = cache('less.' . $reference);

			if ($dependencies === null) {
				$refresh = true;
			} elseif (!empty($dependencies)) {
				foreach ($dependencies as $d_file) {
					if (!is_file($d_file) || _filemtime($less_file) < _filemtime($d_file)) {
						$refresh = true;
						break;
					}
				}
			}
		} else {
			$refresh = true;
		}

		//If refresh requested or cache is invalid
		if ($refresh) {
			//Cleared cached files for this reference
			$cached_files = glob(DIR_CACHE . 'less/' . $reference . '*.css');

			foreach ($cached_files as $cache) {
				@unlink($cache);
			}

			//Load PHPSass
			require_once(DIR_RESOURCES . 'lessphp/Less.php');

			$options = array(
				'compress'        => option('less_compress', true),
				'import_callback' => function ($a) {
					$value = $a->path->value;

					if (strpos($value, '/') === 0) {
						if (!is_file($file = $_SERVER['DOCUMENT_ROOT'] . $a->path->value)) {
							if (!is_file($file = $file . '.less')) {
								return;
							}
						}
					} else {
						$dirs = array(
							'currentDirectory',
							'entryPath',
							'rootpath',
						);

						$cfi  = $a->path->currentFileInfo;
						$file = false;

						foreach ($dirs as $dir) {
							if (is_file($cfi[$dir] . $value)) {
								$file = $cfi[$dir] . $value;
								break;
							}

							if (is_file($cfi[$dir] . $value . '.less')) {
								$file = $cfi[$dir] . $value . '.less';
								break;
							}
						}

						if (!$file) {
							return;
						}
					}

					if (is_file($file . '.mod')) {
						$file = $file . '.mod';
					}

					return array(
						$file,
						str_replace(DIR_SITE, SITE_BASE, dirname($file)),
					);
				},
			);

			try {
				$parser = new Less_Parser($options);

				$parser->parseFile($file, $reference);

				$parser->parse("@base-path: '" . SITE_BASE . "'; @style-path: '@{base-path}app/view/style/';");

				$css = $parser->getCss();

				$dependencies = $parser->allParsedFiles();

				cache('less.' . $reference, $dependencies);
			} catch (Exception $e) {
				trigger_error($e->getMessage());

				return false;
			}

			//Write cache file
			if (_is_writable(dirname($less_file))) {
				file_put_contents($less_file, $css);
			} else {
				trigger_error(_l("%s(): Failed to write CSS file. Directory was unwritable: %s", __METHOD__, $css));

				return false;
			}
		}

		//Return the URL for the cache file
		return str_replace(DIR_CACHE, URL_SITE . 'system/cache/', $less_file);
	}

	public function compileLessContent($content, $compress = null)
	{
		require_once(DIR_RESOURCES . 'lessphp/Less.php');

		$options = array(
			'compress' => $compress === null ? option('less_compress', true) : $compress,
		);

		try {
			$parser = new Less_Parser($options);

			$parser->parse($content);

			$parser->parse("@basepath: '" . SITE_BASE . "';");

			return trim($parser->getCss());
		} catch (Exception $e) {
			trigger_error($e->getMessage());

			return false;
		}
	}

	public function addStyle($href, $rel = 'stylesheet', $media = 'screen', $priority = 0)
	{
		if (!$href) {
			$this->error['href'] = _l("You must specify a style resource to add.");

			return false;
		}

		$file = str_replace(URL_SITE, DIR_SITE, $href);

		//Check if URL or stream wrapper
		$is_file = strpos($file, '//') === false && is_file($file);

		if ($is_file) {
			$ext = pathinfo($file, PATHINFO_EXTENSION);

			if ($ext === 'less') {
				$href = $this->compileLess($file, slug(str_replace(DIR_SITE, '', $file), '-'));

				if (!$href) {
					return false;
				}
			} else {
				$href = str_replace(DIR_SITE, URL_SITE, $file) . '?v=' . filemtime($file);
			}
		}

		if (!$priority) {
			$priority = 0;

			foreach ($this->styles as $s) {
				if ($s['priority'] < 1) {
					$priority = max($s['priority'], $priority);
				}
			}

			$priority += .01;
		}

		$this->styles[md5($href . $rel . $media)] = array(
			'href'     => $href,
			'rel'      => $rel,
			'media'    => $media,
			'priority' => $priority,
		);

		sort_by($this->styles, 'priority');

		return true;
	}

	public function getStyles()
	{
		return $this->styles;
	}

	public function addScript($script, $priority = 100, $minify = null)
	{
		if (is_url($script)) {
			if ($minify) {
				$script = $this->minifyJsFile($script);
			}
		} elseif (is_file($script)) {
			$filemtime = filemtime($script);

			if ($minify === null ? option('minify_js_files', false) : $minify) {
				$script = $this->minifyJsFile($script);
			}

			$script = str_replace(DIR_SITE, URL_SITE, $script) . '?v=' . $filemtime;
		}

		$this->scripts[(int)$priority][md5($script)] = $script;
	}

	public function localizeScript($script, $priority = 100)
	{
		$this->addScript('local://' . $script, $priority);
	}

	public function minifyJs($content)
	{
		require_once(DIR_RESOURCES . 'js/jshrink/Minifier.php');

		return JShrink\Minifier::minify($content);
	}

	public function minifyJsFile($script)
	{
		$script_file = str_replace(URL_SITE, DIR_SITE, $script);

		if (!is_file($script_file)) {
			return $script;
		}

		$file       = 'js/' . slug($script) . '.js';
		$cache_file = DIR_CACHE . $file;
		$cache_url  = URL_SITE . 'system/cache/' . $file;

		if (!is_file($cache_file) || (_filemtime($cache_file) < _filemtime($script_file))) {
			if (_is_writable(dirname($cache_file))) {
				file_put_contents($cache_file, $this->minifyJs(file_get_contents($script_file)));
			} else {
				return $script;
			}
		}

		return $cache_url;
	}

	public function gzipFile($file, $level = null)
	{
		if (!is_file($file)) {
			return false;
		}

		$gzip_file = $file . '.gz';

		if (!is_file($gzip_file) || (_filemtime($gzip_file) < _filemtime($file))) {
			$g = gzencode(file_get_contents($file), $level !== null ? $level : option('gzip_compression_level', 9));

			if ($g) {
				file_put_contents($gzip_file, $g);
			}
		}

		return $gzip_file;
	}

	/**
	 * Retrieves the scripts requested, sorted by priority
	 * Note: We sort the scripts here as it is assumed this is only called once
	 *
	 * @return array - Each element is a string of the absolute file path to the script
	 */
	public function getScripts()
	{
		global $js_autoload, $_js_vars;

		//Register jQuery plugin autoload files
		foreach ($js_autoload as &$file) {
			$file = (array)$file;

			foreach ($file as &$f) {
				if (is_file(DIR_SITE . $f)) {
					$f .= (strpos($f, '?') ? '&' : '?') . '_=' . filemtime(DIR_SITE . $f);
				}
			}
			unset($f);
		}
		unset($file);

		$_js_vars['al'] = $js_autoload;

		//Sort scripts by priority
		ksort($this->scripts);

		$scripts = array(
			'local' => array(
				'ac' => "\$ac = " . json_encode($_js_vars),
			),
		);

		foreach ($this->scripts as $priority => $script_list) {
			foreach ($script_list as $script) {
				//Separate Localized files
				if (strpos($script, 'local://') === 0) {
					if (is_file($file = substr($script, 6))) {
						ob_start();
						include($file);
						$scripts['local'][] = ob_get_clean();
					}
				} else {
					$scripts['src'][] = $script;
				}
			}
		}

		return $scripts;
	}

	public function setBodyClass($class)
	{
		if (!$class) {
			$this->info['body_class'] = array();
		} else {
			$class                    = is_array($class) ? $class : explode(' ', $class);
			$this->info['body_class'] = array_combine(array_keys($class), $class);
		}
	}

	public function addBodyClass($class)
	{
		$this->info['body_class'][$class] = $class;
	}

	public function getBodyClass()
	{
		return implode(' ', $this->info['body_class']);
	}

	public function &findActiveLink(&$links, $page = null, &$active_link = null, $highest_match = 0)
	{
		if (!$page) {
			$page = array(
				'path'  => $this->route->getPath(),
				'query' => $_GET,
			);
		}

		foreach ($links as $key => &$link) {
			if (isset($link['active']) && $link['active'] === false) {
				unset($links[$key]);
				continue;
			}

			if (!empty($link['url'])) {
				$components = parse_url(str_replace('&amp;', '&', $link['url']));

				if ($page['path'] === $components['path']) {
					if (!empty($components['query'])) {
						$queryVars = null;
						parse_str($components['query'], $queryVars);

						$num_matches = 0;

						foreach ($queryVars as $key => $value) {
							if (isset($page['query'][$key]) && $page['query'][$key] === $value) {
								$num_matches++;
							}
						}

						if ($num_matches >= count($queryVars) && $num_matches >= $highest_match) {
							$highest_match = $num_matches;
							$active_link   = &$link;
						}
					} else {
						$active_link = &$link;
					}
				}
			}

			if (!empty($link['children'])) {
				$active_link = &$this->findActiveLink($link['children'], $page, $active_link, $highest_match);

				if ($active_link) {
					foreach ($link['children'] as $child) {
						if (!empty($child['active'])) {
							$link['active'] = 'active_parent';
						}
					}
				}
			}

		}
		unset($link);

		if ($active_link) {
			$active_link['active'] = 'active';
		}

		return $active_link;
	}

	public function renderLinks($links, $sort = true, $depth = 0)
	{
		if (is_string($links)) {
			$links = $this->getLinks($links);
		}

		if (!$links) {
			return;
		}

		if ($sort) {
			sort_by($links, 'sort_order');
		}

		if ($depth === 0) {
			$this->findActiveLink($links);
		}

		switch ($depth) {
			case 0:
				$class = "top-menu";
				break;
			case 1:
				$class = "sub-menu";
				break;
			default:
				$class = "child-menu child-$depth";
				break;
		}

		$html = '';

		$zindex = count($links);

		foreach ($links as $link) {
			if (!$link) {
				continue;
			}

			if (empty($link['display_name'])) {
				$link['display_name'] = $link['name'];
				$link['name']         = slug($link['name'], '-');
			}

			$attr_fields = array(
				'title',
				'href',
				'target',
				'class',
			);

			foreach ($attr_fields as $field) {
				if (!empty($link[$field]) && !isset($link['#' . $field])) {
					$link['#' . $field] = $link[$field];
				}
			}

			if (empty($link['#class'])) {
				$link['#class'] = '';
			}

			$link['#class'] .= ' link-' . $link['name'];

			$children = '';

			if (!empty($link['children'])) {
				$children = $this->renderLinks($link['children'], $sort, $depth + 1);
				$link['#class'] .= ' has-children';
			}

			//Set active class
			if (!empty($link['active'])) {
				$link['#class'] .= ' ' . $link['active'];
			}

			$link['#class'] = trim($link['#class'] . ' menu-link');

			//Build attribute list
			$attrs    = attrs($link);
			$li_attrs = !empty($link['li']) ? attrs($link['li']) : '';

			$html .= "<li $li_attrs style=\"z-index: " . $zindex . "\"><a $attrs>$link[display_name]</a>$children</li>";

			$zindex--;
		}

		return "<div class=\"link-list $class\"><ul>" . $html . "</ul></div>";
	}
}
