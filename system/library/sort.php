<?php
class Sort extends Library
{
	private $language_data;
	private $sorts;
	private $limits;
	private $limit;
	private $sort;
	private $order;
	private $page;
	private $data = array();
	private $sort_template;
	private $limit_template;
	
	public function __construct($registry)
	{
		parent::__construct($registry);
		
		//TODO: Move this to the admin panel
		$this->limits = array(
			10 => '10',
			20 => '20',
			50 => '50',
			100 => '100',
			0 => 'all'
		);
		
		$this->language_data = $this->language->system_fetch('sort');
	}
	
	public function set_language($key, $value)
	{
		$this->language_data[$key] = $value;
	}
	
	public function getSortData()
	{
		return $this->data;
	}
	
	public function get_sorts()
	{
		return $this->sorts;
	}
	
	public function set_sorts($sorts)
	{
		$this->sorts = $sorts;
	}
	
	public function get_sort_url()
	{
		$sort_url = $this->url->link($this->url->getPath(), $this->url->getQueryExclude('sort', 'order', 'page'));
		$sort_url .= strpos($sort_url, '?') === false ? '?' : '&';
		
		return $sort_url;
	}
	
	public function set_sort_template($template)
	{
		$this->sort_template = $template;
	}
	
	public function render_sort($sorts)
	{
		if (!$this->sort_template) {
			$template = 'block/widget/sort';
		}
		
		$template_file = $this->template->find_file($template);
		
		if (!$template_file) {
			trigger_error("Sort::render_sort(): Sort template $template was found! " . get_caller());
			return;
		}
		
		$sort_url = $this->get_sort_url();
		
		$sort_select = 'sort=' . $this->sort . '&order=' . $this->order;
		
		extract($this->language_data);
		
		ob_start();
		
		include($template_file);
		
		return ob_get_clean();
	}
	
	public function get_limits()
	{
		return $this->limits;
	}
	
	public function set_limits($limits)
	{
		$this->limits = $limits;
	}
	
	public function set_limit_template($template)
	{
		$this->sort_template = $template;
	}
	
	public function render_limit($limits = null)
	{
		if (!$this->limit_template) {
			$template = 'block/widget/limit';
		}
		
		$template_file = $this->template->find_file($template);
		
		if (!$template_file) {
			trigger_error("Sort::render_sort(): Limit template $template was found! " . get_caller());
			return;
		}
		
		$limit_url = $this->url->link($this->url->getPath(), $this->url->getQueryExclude('limit','page') . '&limit=');
		
		$limit = $this->limit;
		
		if (empty($limits)) {
			$limits = $this->limits;
		}
		
		//Set limit for pagination compatibility
		if (empty($_GET['limit']) || $_GET['limit'] !== $limit) {
			$_GET['limit'] = $limit;
		}
		
		extract($this->language_data);
		
		ob_start();
		
		include($template_file);
		
		return ob_get_clean();
	}
	
	public function getQueryDefaults($sort_default = 'sort_order', $order_default = 'ASC', $limit_default = null, $page_default = 1){
		if (empty($limit_default) || (int)$limit_default < 1) {
			$limit_default = $this->config->isAdmin() ? $this->config->get('config_admin_limit') : $this->config->get('config_catalog_limit');
		}
		
		$data = array();
		
		$sort_defaults = array(
			'sort' => $sort_default,
			'order' => $order_default,
			'page' => $page_default,
			'limit' => $limit_default,
		);
		
		foreach ($sort_defaults as $key => $default) {
			$data[$key] = isset($_GET[$key]) ? $_GET[$key] : $default;
		}
		
		if ($data['limit'] > 0) {
			$data['start'] = ($data['page'] - 1) * $data['limit'];
			$this->limit = $data['limit'];
		} else {
			$this->limit = false;
		}
		
		$this->sort = $data['sort'];
		$this->order = $data['order'];
		$this->page = $data['page'];
		
		$this->data = $data;
		
		return $data;
	}
}