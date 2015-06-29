<?php

abstract class Controller
{
	//Use this to override the $is_ajax for the controller / template (useful for discluding headers, footers, breadcrumbs, etc.)
	public
		$is_ajax,
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
		if (AMPLO_PROFILE) {
			_profile('RENDER: ' . $path);
		}

		$_template = is_file($path) ? $path : $this->theme->getFile('template/' . $path, $theme);

		if (!$_template) {
			trigger_error(_l("%s(): Could not resolve template path %s", __METHOD__, $path));
			exit();
		}

		//Used for ajax override for templates
		$is_ajax = $this->is_ajax;

		extract($data);

		ob_start();

		include(_mod($_template));

		$this->output = ob_get_clean();

		if (AMPLO_PROFILE) {
			_profile('RENDER COMPLETED: ' . $path);
		}

		return $this->output;
	}
}
