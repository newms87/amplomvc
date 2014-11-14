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

	/**
	 * Get all the breadcrumbs or if $offset is set, get the specific crumb at $offset.
	 *
	 * @param int $offset - If offset > 0, return the breadcrumb at $offset - 1 (eg: $offset = 1, returns the first breadcrumb)
	 *                      If offset <= 0, return the breadcrumb at $offset from the last breadcrumb
	 *                      (eg: $offset = 0, return the last breadcrumb (typically the current page), $offset = -1 returns the previous page (typically))
	 *
	 * @return array|null - Will return an array of all the breadcrumbs, or 1 breadcrumb if $offset is set, or null if a breadcrumb did not exist at the $offset.
	 */
	public function get($offset = null)
	{
		if ($offset !== null) {
			if ($offset <= 0) {
				$offset = (count($this->crumbs)-1) + $offset;
			} else {
				$offset--;
			}

			return isset($this->crumbs[$offset]) ? $this->crumbs[$offset] : null;
		}

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
