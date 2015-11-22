<?php

abstract class Controller
{
	//Use this to override the $is_ajax for the controller / template (useful for discluding headers, footers, breadcrumbs, etc.)
	public
		$is_ajax,
		$method,
		$output;

	public function __construct()
	{
		$this->is_ajax = IS_AJAX;
	}

	public function __get($key)
	{
		global $registry;

		return $registry->get($key);
	}

	public function load($path, $class)
	{
		global $registry;

		return $registry->load($path, $class);
	}

	protected function render($path, $data = array(), $theme = null)
	{
		$_template = is_file($path) ? $path : $this->theme->getFile('template/' . $path, $theme);

		if (!$_template) {
			trigger_error(_l("%s(): Could not resolve template path %s", __METHOD__, $path));
			exit();
		}

		//Used for ajax override for templates
		$data += array(
			'is_ajax' => $this->is_ajax,
		);

		$this->output = render_file($_template, $data);

		return $this->output;
	}
}
