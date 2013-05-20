<?php
class Pagination {
	private $registry;
	private $default_template = 'widget/pagination';
	private $template_file;
	
	public $total;
	public $page;
	public $limit;
	public $num_links;
	public $url;
	public $attrs = array();
	
   function __construct($registry){
   	$this->registry = $registry;
		
      $this->init();
   }
   
	public function __get($key){
		return $this->registry->get($key);
	}
	
   public function init(){
   	$this->template_file = $this->default_template;
      $this->total = 0;
      $this->page = 0;
      $this->limit = 0;
      $this->num_links = 10;
      $this->url = '';
      $this->attrs = array(
      	'class' => 'links'
      );
   }
   
	public function render() {
		$this->template->load($this->template_file);
		
		$language = $this->language->fetch('widget/pagination');
		
		//Setup Query to add page=n 
		if(strpos($this->url, '?') === false){
			$this->url .= '?';
		} else {
			$this->url .= '&';
		}
		
		if($this->page){
			$this->page = (int)$this->page > 1 ? (int)$this->page : 1;
		} else {
			$this->page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
		}
		
		if($this->limit){
			$this->limit = (int)$this->limit ? (int)$this->limit : 10;
		} else {
			$this->limit = isset($_GET['limit']) ? (int)$_GET['limit'] : $this->config->get('config_admin_limit');
			$this->url .= 'limit=' . $this->limit . '&';
		}
		
		//To avoid divide by zero, we only want 1 page for no limit
		if($this->limit < 1){
			$this->limit = $this->total;
		}
		
		$num_pages = ceil($this->total / $this->limit);
		
		if ($num_pages > $this->num_links) {
			$num_before = floor(($this->num_links - 1) / 2);
			$num_after = floor($this->num_links / 2);
			
			if($page + $num_after >= $num_pages){
				$start = $num_pages - $this->num_links;
				$end = $num_pages;
			}
			elseif($page - $num_before <= 1){
				$start = 1;
				$end = $this->num_links;
			}
			else{
				$start = $page - $num_before;
				$end = $page + $num_after;
			}
		}
		else{
			$start = 1;
			$end = $num_pages;
		}
		
		$pages = array();
		
		if($num_pages > 1){
			for ($i = $start; $i <= $end; $i++) {
				$pages[$i] = $this->url . 'page=' . $i;
			}
		}
		
		$attrs = '';
		
		if(!empty($this->attrs)){
			foreach($this->attrs as $attr => $value){
				$attrs .= "$attr=\"$value\" ";
			}
		}
		
		$data = array(
			'total'	=> $this->total,
			'page'	=> $this->page,
			'limit'	=> $this->limit,
			'url_first' => $this->url . 'page=1',
			'url_prev' => $this->url . 'page=' . ($this->page - 1),
			'url_next' => $this->url . 'page=' . ($this->page + 1),
			'url_last' => $this->url . 'page=' . $num_pages,
			'start' => $start,
			'end' => $end,
			'attrs' => $attrs,
			'num_pages' => $num_pages,
			'pages' => $pages,
		);
		
		$item_start = (($this->page - 1) * $this->limit) + 1;
		$item_end = ($this->page * $this->limit > $this->total) ? $this->total : $this->page * $this->limit;
		
		$insertables = array(
			'start' => $item_start,
			'end'	 => $item_end,
			'total' => $this->total,
			'pages' => $num_pages,
		);
		
		$language['text_pager'] = $this->tool->insertables($insertables, $language['text_pager']);
		
		$this->template->set_data($data + $language);
		
		return $this->template->render();
	}
}