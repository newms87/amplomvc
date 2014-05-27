<?php

class Document extends Library
{
	private $title;
	private $description;
	private $keywords;
	private $canonical_link = null;
	private $links = array();
	private $restricted;
	private $styles = array();
	private $scripts = array();
	private $ac_vars = array();

	function __construct()
	{
		parent::__construct();

		$this->restricted = $this->Model_User_Role->getControllers();
		$this->links = $this->getNavigationLinks();

		$this->setCanonicalLink($this->url->getSeoUrl());

		if ($ac_vars = option('config_ac_vars')) {
			$this->ac_vars += $ac_vars;
		}
	}

	public function setTitle($title)
	{
		$this->title = $this->tool->cleanTitle($title);
	}

	public function getTitle()
	{
		return $this->title;
	}

	public function setDescription($description)
	{
		$this->description = $description;
	}

	public function getDescription()
	{
		return $this->description;
	}

	public function setKeywords($keywords)
	{
		$this->keywords = $keywords;
	}

	public function getKeywords()
	{
		return $this->keywords;
	}

	/**
	 * Canonical Links are used by search engines to determine the most appropriate version of web pages
	 * with identical (or almost, eg: different sort orders, etc.) content.
	 *
	 * When pretty URLs are active, this will allow search results to show your pages with the pretty url version.
	 *
	 * @param $href - the preferred url for the current page.
	 */
	public function setCanonicalLink($href)
	{
		$this->canonical_link = $href;
	}

	public function getCanonicalLink()
	{
		return $this->canonical_link;
	}

	public function hasLinks($group = 'primary')
	{
		return !empty($this->links[$group]);
	}

	public function addLink($group = 'primary', $link_info)
	{
		if (empty($link_info['name'])) {
			trigger_error(_l("%s(): You must provide a link name!"));
			return;
		}

		$defaults = array(
			'name'         => null,
			'display_name' => '',
			'href'         => '',
			'query'        => null,
			'title'        => '',
			'class'        => array(),
			'sort_order'   => null,
			'parent_id'    => 0,
			'parent'       => '',
			'attrs'        => array(),
			'target'       => '',
			'children'     => array(),
		);

		$new_link = $link_info + $defaults;

		//If group doesn't exist, make a new group
		if (!isset($this->links[$group])) {
			$this->links[$group][$new_link['name']] = $new_link;
			return;
		}

		//Find the children list for the parent
		if ($new_link['parent'] || $new_link['parent_id']) {
			$return = array_walk_children($this->links[$group], 'children', function (&$link) use ($new_link) {
				if (empty($link)) {
					return;
				}
				if ($new_link['parent'] === $link['name']) {
					$link['children'][$new_link['name']] = $new_link;
					return false;
				} elseif (!empty($link['navigation_id']) && $new_link['parent_id'] === $link['navigation_id']) {
					$link['children'][$new_link['name']] = $new_link;
					return false;
				}
			});

			//$return === false when link is found
			if ($return !== false) {
				trigger_error(_l("Unable to locate link %s in Link Group %s", $new_link['parent'], $group));
			}
		} else {
			$this->links[$group][] = $new_link;
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
		$this->links[$group] = $links;
	}

	public function getLinks($group = 'primary')
	{
		if (isset($this->links[$group])) {
			return $this->links[$group];
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
			$dependencies = $this->cache->get('less.' . $reference);

			if (is_null($dependencies)) {
				$refresh = true;
			} elseif (!empty($dependencies)) {
				foreach ($dependencies as $d => $d_mtime) {
					if (_filemtime($less_file) < _filemtime($d)) {
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
			require_once(DIR_RESOURCES . 'css/lessphp/lessc.inc.php');

			$less = new lessc();

			$css = $less->compileFile($file);

			$dependencies = $less->allParsedFiles();

			$this->cache->set('less.' . $reference, $dependencies);

			//Write cache file
			if (_is_writable(dirname($less_file))) {
				file_put_contents($less_file, $css);
			} else {
				trigger_error(_l("%s(): Failed to write CSS file. Directory was unwritable: %s", __METHOD__, $css));
			}
		}

		//Return the URL for the cache file
		return str_replace(DIR_CACHE, URL_SITE . 'system/cache/', $less_file);
	}

	public function addStyle($href, $rel = 'stylesheet', $media = 'screen')
	{
		$this->styles[md5($href)] = array(
			'href'  => $href,
			'rel'   => $rel,
			'media' => $media
		);
	}

	public function getStyles()
	{
		return $this->styles;
	}

	public function renderStyles()
	{
		$html = '';

		foreach ($this->styles as $style) {
			$html .= "<link rel=\"$style[rel]\" type=\"text/css\" href=\"$style[href]\" media=\"$style[media]\" />\r\n";
		}

		return $html;
	}

	public function addScript($script, $priority = 100)
	{
		//First check for a URL wrapper, then check if it is a file
		if (strpos($script, '//') === false && !is_file($script)) {
			if (is_file(DIR_SITE . $script)) {
				$script = URL_SITE . $script;
			} elseif (is_file(DIR_RESOURCES . 'js/' . $script)) {
				$script = URL_RESOURCES . 'js/' . $script;
			} elseif (is_file(DIR_SITE . 'app/view/javascript/' . $script)) {
				$script = URL_SITE . 'app/view/javascript/' . $script;
			}
		}

		$this->scripts[(int)$priority][md5($script)] = $script;
	}

	public function localizeScript($script, $priority = 100)
	{
		$this->addScript('local:' . $script, $priority);
	}

	public function localizeVar($var, $value)
	{
		$this->ac_vars[$var] = $value;
	}

	/**
	 * Retrieves the scripts requested, sorted by priority
	 * Note: We sort the scripts here as it is assumed this is only called once
	 *
	 * @return array - Each element is a string of the absolute filepath to the script
	 */
	public function getScripts()
	{
		$scripts = array();

		ksort($this->scripts);

		foreach ($this->scripts as $priority => $script_list) {
			foreach ($script_list as $script) {
				$scripts[] = $script;
			}
		}

		return $scripts;
	}

	public function renderScripts()
	{
		$scripts = $this->getScripts();

		$html = '';

		foreach ($scripts as $script) {
			if (strpos($script, 'local:') === 0) {
				if (is_file($file = substr($script, 6))) {
					$html .= "<script type=\"text/javascript\">\r\n";
					ob_start();
					include($file);
					$html .= ob_get_clean();
					$html .= "\r\n</script>\r\n";
				}
			} else {
				$html .= "<script type=\"text/javascript\" src=\"$script\"></script>\r\n";
			}
		}

		if (!empty($this->ac_vars)) {
			$html .= "<script type=\"text/javascript\">\r\n\$ac = " . json_encode($this->ac_vars) . ";\r\n</script>";
		}

		return $html;
	}

	public function getNavigationLinks()
	{
		$store_id = option('store_id');

		$nav_groups = $this->cache->get("navigation_groups.store.$store_id");

		if (is_null($nav_groups)) {
			$query = "SELECT ng.* FROM " . DB_PREFIX . "navigation_group ng" .
				" LEFT JOIN " . DB_PREFIX . "navigation_store ns ON (ng.navigation_group_id = ns.navigation_group_id)" .
				" WHERE ng.status = 1 AND ns.store_id = " . (int)$store_id;

			$result = $this->queryRows($query);

			$nav_groups = array();

			foreach ($result as &$group) {
				$nav_group_links = $this->getNavigationGroupLinks($group['navigation_group_id']);

				if (empty($nav_group_links)) {
					continue;
				}
				$parent_ref = array();

				foreach ($nav_group_links as $key => &$link) {
					$link['children']                   = array();
					$parent_ref[$link['navigation_id']] = & $link;

					if ($link['parent_id']) {
						$parent_ref[$link['parent_id']]['children'][] = & $link;
						unset($nav_group_links[$key]);
					}
				}
				unset($link);

				$nav_groups[$group['name']] = $nav_group_links;
			}
			unset($group);

			$this->cache->set("navigation_groups.store.$store_id", $nav_groups);
		}

		//Filter Conditional Links And Access Permissions


		//TODO: This leaves null values in group links. Consider changing approach.
		foreach ($nav_groups as &$group) {
			$this->filterLinks($group);
		}
		unset($group);

		return $nav_groups;
	}

	public function filterLinks(&$links)
	{
		foreach ($links as $key => &$link) {
			if (!empty($link['children'])) {
				$this->filterLinks($link['children']);
			}

			//Filter by Conditions
			if (!empty($link['condition']) && !$this->condition->is($link['condition'])) {
				unset($links[$key]);
				continue;
			}

			//Filter restricted paths, current user cannot access
			if ($this->route->isAdmin()) {
				$path = str_replace('admin/', '', $link['href']);

				$check_path = false;

				if ($path) {
					if (in_array($path, $this->restricted)) {
						$check_path = $path;
					} else {
						foreach ($this->restricted as $restricted_path) {
							if (strpos($path, $restricted_path . '/') === 0) {
								$check_path = $restricted_path;
								break;
							}
						}
					}
				}

				if ($check_path && !user_can('access', $check_path)) {
					unset($links[$key]);
					continue;
				}
			}

			//Filter empty non-links
			if (empty($link['href']) && empty($link['children'])) {
				unset($links[$key]);
			}
		}
	}

	public function getNavigationGroupLinks($navigation_group_id)
	{
		return $this->queryRows("SELECT * FROM " . DB_PREFIX . "navigation WHERE status='1' AND navigation_group_id='" . (int)$navigation_group_id . "' ORDER BY parent_id ASC, sort_order ASC");
	}

	public function &findActiveLink(&$links, $page = null, &$active_link = null, $highest_match = 0)
	{
		if (!$page) {
			$page = parse_url($this->url->getSeoUrl());

			$page['query'] = null;
			parse_str($this->url->getQuery(), $page['query']);
		}

		foreach ($links as $key => &$link) {
			if ($link['href'] || $link['query']) {

				if (!preg_match("/^https?:\\/\\//", $link['href'])) {
					$query        = isset($link['query']) ? $link['query'] : '';
					$link['href'] = site_url($link['href'], $query);
				}

				$components = parse_url(str_replace('&amp;', '&', $link['href']));

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
							$active_link   = & $link;
						}
					} else {
						$active_link = & $link;
					}
				}
			}

			if (!empty($link['children'])) {
				$active_link = & $this->findActiveLink($link['children'], $page, $active_link, $highest_match);

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
			usort($links, function ($a, $b) {
				return (int)$a['sort_order'] > (int)$b['sort_order'];
			});
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

		$html = "<ul class=\"link-list $class\">";

		$zindex = count($links);

		foreach ($links as $link) {
			if (!$link) {
				continue;
			}

			if (!empty($link['title']) && !isset($link['attrs']['title'])) {
				$link['attrs']['title'] = $link['title'];
			}

			if (empty($link['display_name'])) {
				$link['display_name'] = $link['name'];
			}

			$link['attrs']['class'] = (!empty($link['attrs']['class']) ? ' ' : '') . 'link-' . $link['name'];

			$children = '';

			if (!empty($link['children'])) {
				$children = $this->renderLinks($link['children'], $sort, $depth + 1);
				$link['attrs']['class'] .= ' has-children';
			}

			$href = '';
			if (!empty($link['href'])) {
				$href = "href=\"$link[href]\"";
			}

			//Set active class
			if (!empty($link['active'])) {
				$link['attrs']['class'] .= ' ' . $link['active'];
			}

			//Build attribute list
			$attr_list = '';

			if (!empty($link['attrs'])) {
				if (is_string($link['attrs'])) {
					$attr_list .= $link['attrs'];
				} else {
					foreach ($link['attrs'] as $key => $value) {
						$attr_list .= "$key=\"$value\"";
					}
				}
			}

			$target = !empty($link['target']) ? "target=\"$link[target]\"" : '';

			$html .= "<li $attr_list style=\"z-index: " . $zindex . "\"><a $href $target class=\"menu-link\">$link[display_name]</a>$children</li>";

			$zindex--;
		}

		$html .= "</ul>";

		return $html;
	}
}
