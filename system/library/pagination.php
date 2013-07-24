<?php
class Pagination extends Library
{
	private $default_template = 'block/widget/pagination';
	private $template_file;
	
	public $total;
	public $page;
	public $limit;
	public $num_links;
	public $page_url;
	public $attrs = array();
	
	function __construct($registry)
	{
		parent::__construct($registry);
		
		$this->init();
	}
	
	public function init()
	{
		$this->template_file = $this->default_template;
		$this->total = 0;
		$this->page = 0;
		$this->limit = 0;
		$this->num_links = 10;
		$this->page_url = '';
		$this->attrs = array(
			'class' => 'links'
		);
	}
	
	public function render()
	{
		if ($this->total < 1) {
			return '';
		}
		
		$this->template->load($this->template_file);
		
		$language = $this->language->fetch('block/widget/pagination');
		
		if (!$this->page_url) {
			$this->page_url = $this->url->link($this->url->route(), $this->url->getQueryExclude('page'));
		}
		
		//Setup Query to add page=n
		if (strpos($this->page_url, '?') === false) {
			$this->page_url .= '?';
		} else {
			$this->page_url .= '&';
		}
		
		if ($this->page) {
			$this->page = (int)$this->page > 1 ? (int)$this->page : 1;
		} else {
			$this->page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
		}
		
		if ($this->limit) {
			$this->limit = (int)$this->limit ? (int)$this->limit : 10;
		} else {
			$this->limit = isset($_GET['limit']) ? (int)$_GET['limit'] : $this->config->get('config_admin_limit');
			$this->page_url .= 'limit=' . $this->limit . '&';
		}
		
		//To avoid divide by zero, we only want 1 page for no limit
		if ($this->limit < 1) {
			$this->limit = $this->total;
		}
		
		$num_pages = ceil($this->total / $this->limit);
		
		if ($num_pages > $this->num_links) {
			$num_before = floor(($this->num_links - 1) / 2);
			$num_after = floor($this->num_links / 2);
			
			if ($page + $num_after >= $num_pages) {
				$start = $num_pages - $this->num_links;
				$end = $num_pages;
			}
			elseif ($page - $num_before <= 1) {
				$start = 1;
				$end = $this->num_links;
			}
			else {
				$start = $page - $num_before;
				$end = $page + $num_after;
			}
		}
		else {
			$start = 1;
			$end = $num_pages;
		}
		
		$pages = array();
		
		if ($num_pages > 1) {
			for ($i = $start; $i <= $end; $i++) {
				$pages[$i] = $this->page_url . 'page=' . $i;
			}
		}
		
		$attrs = '';
		
		if (!empty($this->attrs)) {
			foreach ($this->attrs as $attr => $value) {
				$attrs .= "$attr=\"$value\" ";
			}
		}
		
		$data = array(
			'total'	=> $this->total,
			'page'	=> $this->page,
			'limit'	=> $this->limit,
			'url_first' => $this->page_url . 'page=1',
			'url_prev' => $this->page_url . 'page=' . ($this->page - 1),
			'url_next' => $this->page_url . 'page=' . ($this->page + 1),
			'url_last' => $this->page_url . 'page=' . $num_pages,
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
			'end'	=> $item_end,
			'total' => $this->total,
			'pages' => $num_pages,
		);
		
		$language['text_pager'] = $this->tool->insertables($insertables, $language['text_pager']);
		
		$this->template->setData($data + $language);
		
		return $this->template->render();
	}
}