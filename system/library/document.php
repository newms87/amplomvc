<?php
class Document extends Library
{
	private $title;
	private $description;
	private $keywords;
	private $canonical_link = null;
	private $links = array();
	private $styles = array();
	private $scripts = array();
	
	function __construct($registry)
	{
		parent::__construct($registry);
		
		$this->links = $this->Model_Design_Navigation->getNavigationLinks();
		
		$this->setCanonicalLink($this->url->getSeoUrl());
	}
	
	public function setTitle($title)
	{
		$this->title = $title;
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
	
	public function addLink($group = 'primary', $link_info)
	{
		if (empty($link_info['name'])) {
			trigger_error('Document::addLink(): You must provide a link name! ' . get_caller());
			return;
		}
		
		$defaults = array(
			'name'			=> null,
			'display_name'	=> '',
			'href'			=> '',
			'query'			=> null,
			'title'			=> '',
			'sort_order'	=> null,
			'parent'			=> 0,
			'attrs'			=> array(),
			'target' 		=> '',
		);
		
		$new_link = array();
		
		foreach ($defaults as $key => $default) {
			if (isset($link_info[$key])) {
				$new_link[$key] = $link_info[$key];
			} else {
				$new_link[$key] = $default;
			}
		}
		
		//If group doesn't exist, make a new group
		if (!isset($this->links[$group])) {
			$this->links[$group] = array($new_link);
			return;
		}
		
		//Find the children list for the parent
		if ($new_link['parent']) {
			$stack = array();
			$stack_index = 0;
			
			$curr = &$this->links[$group];
			
			do {
				reset($curr);
				do {
					$node = &$curr[key($curr)];
					
					if (isset($node['href']) && $node['name'] === $new_link['parent']) {
						if (empty($node['children'])) {
							$node['children'] = array($new_link['name'] => $new_link);
							return true;
						}
						
						$child_list = & $node['children'];
						$stack = null;
						break;
					}
					
					if (!empty($node['children'])) {
						$stack[] = &$node['children'];
					}
				} while (next($curr));
				unset($curr);
				
				if (!empty($stack)) {
					$curr = &$stack[$stack_index++];
				}
			} while($stack_index < count($stack));
		}
		else {
			$child_list = &$this->links[$group];
		}
		
		if (isset($child_list)) {
			if (!empty($child_list) && !is_null($new_link['sort_order'])) {
				array_splice($child_list, (int)$new_link['sort_order'], 0, array($new_link['name'] => $new_link));
			} else {
				$child_list[] = $new_link;
			}
			
			return true;
		}
		
		trigger_error("Document::addLink(): Unable to find $new_link[parent] in link group $group! " . get_caller());
		
		return false;
	}
	
	public function getLinks($group = 'primary')
	{
		if (isset($this->links[$group])) {
			return $this->links[$group];
		}
		
		return array();
	}
	
	public function addStyle($href, $rel = 'stylesheet', $media = 'screen')
	{
		$this->styles[md5($href)] = array(
			'href'  => $href,
			'rel'	=> $rel,
			'media' => $media
		);
	}
	
	public function getStyles()
	{
		return $this->styles;
	}
	
	public function addScript($script)
	{
		if (!is_file($script)) {
			if (is_file(SITE_DIR . $script)) {
				$script = SITE_URL . $script;
			}
			elseif ($this->config->isAdmin() && is_file(SITE_DIR . 'admin/view/javascript/' . $script)) {
				$script = SITE_URL . 'admin/view/javascript/' . $script;
			}
			elseif (!$this->config->isAdmin() && is_file(SITE_DIR . 'catalog/view/javascript/' . $script)) {
				$script = SITE_URL . 'catalog/view/javascript/' . $script;
			}
		}
		
		$this->scripts[md5($script)] = $script;
	}
	
	public function getScripts()
	{
		return $this->scripts;
	}
	
	public function &findActiveLink(&$links, $page, &$active_link = null, $highest_match = 0)
	{
		foreach ($links as $key => &$link) {
			if (!preg_match("/^https?:\/\//", $link['href'])) {
				if (!empty($link['is_route'])) {
					$query = isset($link['query']) ? $link['query'] : '';
					$link['href'] = $this->url->link($link['href'], $query);
				} elseif($link['href']) {
					$link['href'] = $this->url->site($link['href']);
				}
			}
			
			$components = parse_url(str_replace('&amp;','&',$link['href']));
			
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
						$active_link = &$link;
					}
				} else {
					$active_link = &$link;
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
			
		} unset($link);
		
		if ($active_link) {
			$active_link['active'] = 'active';
		}
		
		return $active_link;
	}
	
	public function renderLinks($links, $depth = 0)
	{
		if ($depth === 0) {
			$current_page = parse_url($this->url->getSeoUrl());
			
			$current_page['query'] = null;
			parse_str($this->url->getQuery(), $current_page['query']);
			
			if (!$this->findActiveLink($links, $current_page)) {
				//If the link wasn't found, try changing the route to the index function and search for the active link again
				if (preg_match("/^([a-z0-9_]+\/[a-z0-9_]+)\/.*/", $this->url->route())) {
					$current_page['query']['route'] = preg_replace("/^([a-z0-9_]+\/[a-z0-9_]+)\/.*/", "\$1", $this->url->route());
					$this->findActiveLink($links, $current_page);
				}
			}
		}
		
		switch($depth){
			case 0:
				$class = "top_menu";
				break;
			case 1:
				$class = "sub_menu";
				break;
			default:
				$class = "child_menu child_$depth";
				break;
		}
		
		$html = "<ul class=\"link_list $class\">";
			
		$zindex = count($links);
			
		foreach ($links as $link) {
			if (!empty($link['title']) && !isset($link['attrs']['title'])) {
				$link['attrs']['title'] = $link['title'];
			}
			
			if (empty($link['display_name'])) {
				$link['display_name'] = $link['name'];
			}
				
			$children = '';
			
			if (!empty($link['children'])) {
				$children = $this->renderLinks($link['children'], $depth+1);
				if (!empty($link['attrs']['class'])) {
					$link['attrs']['class'] .= ' has_children';
				} else {
					$link['attrs']['class'] = 'has_children';
				}
			}
			
			$href = '';
			if (!empty($link['href'])) {
				$href = "href=\"$link[href]\"";
			}
			
			//Set active class
			if (!empty($link['active'])) {
				if (!empty($link['attrs']['class'])) {
					$link['attrs']['class'] .= ' '.$link['active'];
				} else {
					$link['attrs']['class'] = $link['active'];
				}
			}
			
			//Build attribute list
			$attr_list = '';
			
			if (!empty($link['attrs'])) {
				if (is_string($link['attrs'])) {
					$attr_list .= $link['attrs'];
				}
				else {
					foreach ($link['attrs'] as $key=>$value) {
						$attr_list .= "$key=\"$value\"";
					}
				}
			}
			
			$target = !empty($link['target']) ? "target=\"$link[target]\"" : '';
			
			$html .= "<li $attr_list style=\"z-index:$zindex\"><a $href $target class=\"menu_link\">$link[display_name]</a>$children</li>";
				
			$zindex--;
		}
		
		$html .= "</ul>";
			
		return $html;
	}
}
