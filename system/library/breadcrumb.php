<?php
class Breadcrumb extends Library
{
	protected $crumbs = array();
	protected $separator;

	function __construct()
	{
		parent::__construct();

		$this->separator = option('config_breadcrumb_separator');
	}

	public function add($text, $href, $separator = '', $position = null)
	{

		if (!$separator) {
			$separator = $this->separator;
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

	public function clear()
	{
		$this->crumbs = array();
	}

	public function prevUrl()
	{
		if (count($this->crumbs) > 1) {
			return $this->crumbs[count($this->crumbs) - 2]['href'];
		}

		return false;
	}

	public function set_separator($separator)
	{
		$this->separator = $separator;
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
