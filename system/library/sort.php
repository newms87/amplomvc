<?php

class Sort extends Library
{
	private $sorts;
	private $limit;
	private $sort;
	private $order;
	private $page;
	private $data = array();
	private $sort_template;

	//TODO: Move this to the admin panel
	static $limits = array(
		5   => '5',
		10  => '10',
		20  => '20',
		50  => '50',
		100 => '100',
		0   => 'all'
	);

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
		$sort_url = site_url($this->route->getPath(), $this->url->getQueryExclude('sort', 'order', 'page'));
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

		$template_file = $this->theme->getFile($template);

		if (!$template_file) {
			trigger_error(_l("%s(): Sort template %s was found!", __METHOD__, $template));
			return;
		}

		$sort_url = $this->get_sort_url();

		$sort_select = 'sort=' . $this->sort . '&order=' . $this->order;

		ob_start();

		include($template_file);

		return ob_get_clean();
	}

	public function renderLimits($settings = array())
	{
		$defaults = array(
			'template'   => 'block/widget/limit',
			'limits'     => self::$limits,
			'path'       => $this->route->getPath(),
			'limit_text' => '',
		);

		$settings += $defaults;

		$template_file = $this->theme->getFile($settings['template']);

		if (!$template_file) {
			trigger_error(_l("%s(): Limit template %s was found!", __METHOD__, $template_file));
			return;
		}

		//Set limit for pagination compatibility
		if (empty($_GET['limit']) || $_GET['limit'] !== $this->limit) {
			$_GET['limit'] = $this->limit;
		}

		$settings['limit_url'] = site_url($settings['path'], $this->url->getQueryExclude('limit', 'page') . '&limit=');
		$settings['limit']     = $this->limit;

		extract($settings);

		ob_start();

		include(_ac_mod_file($template_file));

		return ob_get_clean();
	}

	public function getQueryDefaults($sort_default = null, $order_default = 'ASC', $limit_default = null, $page_default = 1)
	{
		if (!$sort_default) {
			$order_default = null;
		}

		if (empty($limit_default) || (int)$limit_default < 1) {
			$limit_default = IS_ADMIN ? option('config_admin_limit') : option('config_catalog_limit');
		}

		$data = array();

		$sort_defaults = array(
			'sort'  => $sort_default,
			'order' => $order_default,
			'page'  => $page_default,
			'limit' => $limit_default,
		);

		foreach ($sort_defaults as $key => $default) {
			$data[$key] = isset($_GET[$key]) ? $_GET[$key] : $default;
		}

		if ($data['limit'] > 0) {
			$data['start'] = ($data['page'] - 1) * $data['limit'];
			$this->limit   = $data['limit'];
		} else {
			$this->limit = false;
		}

		//Set limit for pagination compatibility (and potentially other places)
		if (!isset($_GET['limit'])) {
			$_GET['limit'] = $this->limit;
		}

		$this->sort  = $data['sort'];
		$this->order = $data['order'];
		$this->page  = $data['page'];

		$this->data = $data;

		return $data;
	}
}
