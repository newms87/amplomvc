<?php
class Breadcrumb extends Library
{
	private $crumbs = array();
	private $default_separator;

	function __construct($registry)
	{
		parent::__construct($registry);

		$this->default_separator = $this->config->get('config_breadcrumb_separator');
	}

	public function add($text, $href, $separator = '', $position = null)
	{

		if (!$separator) {
			$separator = $this->default_separator;
		}

		$crumb = array(
			'text'      => $text,
			'href'      => $href,
			'separator' => $separator
		);

		if ($position !== null && !empty($this->crumbs)) {
			array_splice($this->crumbs, $position, 0, array($crumb));
		} else {
			$this->crumbs[] = $crumb;
		}
	}

	public function get()
	{
		return $this->crumbs;
	}

	public function get_prev_url()
	{
		if (count($this->crumbs) > 1) {
			return $this->crumbs[count($this->crumbs) - 2]['href'];
		}

		return false;
	}

	public function set_separator($separator)
	{
		$this->default_separator = $separator;
	}

	public function render()
	{
		$html = "";
		foreach ($this->crumbs as $key => $crumb) {
			$html .= ($key > 0 ? $crumb['separator'] : '') . "<a href=\"$crumb[href]\">" . $this->tool->cleanTitle($crumb['text']) . "</a>";
		}

		return "<div class =\"breadcrumb\">$html</div>";
	}
}