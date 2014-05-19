<?php

//TODO: Move this to a block!

class Pagination extends Library
{
	private $template;
	private $default_template = 'block/widget/pagination';

	public $total;
	public $page;
	public $limit;
	public $num_links;
	public $page_url;
	public $attrs = array();

	function __construct()
	{
		parent::__construct();

		$this->init();
	}

	public function init()
	{
		$this->template  = $this->default_template;
		$this->total     = 0;
		$this->page      = 0;
		$this->limit     = 0;
		$this->num_links = 10;
		$this->page_url  = '';
		$this->ajax_url  = '';
		$this->attrs     = array(
			'class' => 'links'
		);
	}

	public function setTemplate($template)
	{
		$this->template = $template;
	}

	public function render()
	{
		if ($this->total < 1) {
			return '';
		}

		if ($this->ajax_url) {
			$this->page_url = $this->ajax_url;
		} else if (!$this->page_url) {
			$this->page_url = site_url($this->route->getPath(), $this->url->getQueryExclude('page'));
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
			$this->limit = isset($_GET['limit']) ? (int)$_GET['limit'] : option('config_admin_limit');
			$this->page_url .= 'limit=' . $this->limit . '&';
		}

		//To avoid divide by zero, we only want 1 page for no limit
		if ($this->limit < 1) {
			$this->limit = $this->total;
		}

		$num_pages = ceil($this->total / $this->limit);

		if ($num_pages > $this->num_links) {
			$num_before = floor(($this->num_links - 1) / 2);
			$num_after  = floor($this->num_links / 2);

			if ($this->page + $num_after >= $num_pages) {
				$start = $num_pages - $this->num_links;
				$end   = $num_pages;
			} elseif ($this->page - $num_before <= 1) {
				$start = 1;
				$end   = $this->num_links;
			} else {
				$start = $this->page - $num_before;
				$end   = $this->page + $num_after;
			}
		} else {
			$start = 1;
			$end   = $num_pages;
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
			'total'     => $this->total,
			'page'      => $this->page,
			'limit'     => $this->limit,
			'url_first' => $this->page_url . 'page=1',
			'url_prev'  => $this->page_url . 'page=' . ($this->page - 1),
			'url_next'  => $this->page_url . 'page=' . ($this->page + 1),
			'url_last'  => $this->page_url . 'page=' . $num_pages,
			'start'     => $start,
			'end'       => $end,
			'attrs'     => $attrs,
			'num_pages' => $num_pages,
			'pages'     => $pages,
		);

		$item_start = (($this->page - 1) * $this->limit) + 1;
		$item_end   = ($this->page * $this->limit > $this->total) ? $this->total : $this->page * $this->limit;

		$insertables = array(
			'start' => $item_start,
			'end'   => $item_end,
			'total' => $this->total,
			'pages' => $num_pages,
		);

		//TODO: Allow Admin panel access to change how this is displayed (separate entries for admin / each store)
		$data['text_pager'] = $this->tool->insertables($insertables, _l("Showing %start% to %end% of %total% (%pages% Pages)"));

		$template = $this->theme->findFile($this->template);

		if (!$template || !is_file($template)) {
			trigger_error(_l("%s(): Could not resolve template path %s", __METHOD__, $this->template));
			exit();
		}

		extract($data);

		ob_start();

		include(_ac_mod_file($template));

		return ob_get_clean();
	}
}
