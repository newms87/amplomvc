<?php
class Document {
	private $title;
	private $description;
	private $keywords;
	private $canonical_link = null;
	private $links = array();
	private $styles = array();
	private $scripts = array();
	
	function __construct($registry){
		$this->links = $registry->get('model_design_navigation')->getNavigationLinks();
	}
	
	public function setTitle($title) {
		$this->title = $title;
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	public function setDescription($description) {
		$this->description = $description;
	}
	
	public function getDescription() {
		return $this->description;
	}
	
	public function setKeywords($keywords) {
		$this->keywords = $keywords;
	}
	
	public function getKeywords() {
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
	public function setCanonicalLink($href){
		$this->canonical_link = $href;
	}
	
	public function getCanonicalLink(){
		return $this->canonical_link;
	}
	
	public function addLink($group = 'primary', $link_info) {
		//Declare valid link_info parameters
		$name = null;
		$display_name = '';
		$href = '';
		$query = null;
		$title = '';
		$sort_order = null;
		$parent = 0;
		$attrs = array();
		
		//populate the parameters if they have been set
		extract($link_info, EXTR_IF_EXISTS);
				
		if(!$name){
			trigger_error('Document::addLink(): You must provide a link name! ' . get_caller()); 
			return;
		}
		
		$new_link = array(
			'name' => $name,
			'display_name'=>$display_name,
			'href'=>$href,
			'query' => $query,
			'title' => $title,
			'sort_order'=>$sort_order,
			'attrs'=>$attrs
		);
		
		//If group doesn't exist, make a new group
		if(!isset($this->links[$group])){
			$this->links[$group] = array($new_link);
			return;
		}
		
		//find the parent link list if not top level parent
		if(!$this->insert_link($parent, $this->links[$group], $new_link, $sort_order)){
			trigger_error("Document::addLink(): Unable to find $parent in link group $group! " . get_caller());
			return;
		}
	}
	
	public function addLinks($group, $links){
		if(!is_array($links)){trigger_error("Error Document::addLinks(): \$links was not an array! " . get_caller()); return;}
		
		$this->links[$group] += $links;
	}
	
	public function getLinks($group='primary') {
		if(isset($this->links[$group])){
			return $this->links[$group];
		}
		
		return array();
	}

	private function insert_link($name, &$list, $new_link, $sort_order){
			
		$add_to = null;
		
		if($name){
			$name = strtolower($name);
			
			foreach($list as $key => $l){
				if(strtolower($list[$key]['name']) == $name){
						
					if(empty($list[$key]['children'])){
						$list[$key]['children'] = array();
					}
					
					$add_to = &$list[$key]['children'];
					
					break;
				}
				
				if($list[$key]['children']){
					if($this->insert_link($name, $list[$key]['children'], $new_link, $sort_order)){ 
						return true;
					}
				}
			}
		}
		else{
			$add_to = &$list;
		}
		
		if(is_array($add_to)){
			//sort the link in the specified sort order
			if($sort_order !== null){
				array_splice($add_to, (int)$sort_order, 0, array($new_link));
			}
			else{
				$add_to[] = $new_link;
			}
			
			return true;
		}
		
		return false;
	}
	
	public function addStyle($href, $rel = 'stylesheet', $media = 'screen') {
		$this->styles[md5($href)] = array(
			'href'  => $href,
			'rel'	=> $rel,
			'media' => $media
		);
	}
	
	public function getStyles() {
		return $this->styles;
	}	
	
	public function addScript($script) {
		if(!is_file($script)){
			if(is_file(SITE_DIR . $script)){
				$script = SITE_URL . $script;
			}
			elseif(defined("IS_ADMIN") && is_file(SITE_DIR . 'admin/view/javascript/' . $script)){
				$script = SITE_URL . 'admin/view/javascript/' . $script;
			}
			elseif(!defined("IS_ADMIN") && is_file(SITE_DIR . 'catalog/view/javascript/' . $script)){
				$script = SITE_URL . 'catalog/view/javascript/' . $script;
			}
		}
		
		$this->scripts[md5($script)] = $script;
	}
	
	public function getScripts() {
		return $this->scripts;
	}
}
