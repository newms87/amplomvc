<?php

abstract class Controller
{
	//Use this to override the $is_ajax for the controller / template (useful for discluding headers, footers, breadcrumbs, etc.)
	public $is_ajax;

	public $output;
	public $error = array();

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
		//TODO All validation should be done in Model! Remove this after removing all validation methods.
		//Display Error Messages
		if (!isset($data['errors'])) {
			$data['errors'] = array();

			if ($this->error) {
				if (!$this->is_ajax) {
					message('warning', $this->error);
				}

				$data['errors'] = $this->error;

				$this->error = array();
			}
		}

		$_template = is_file($path) ? $path : $this->theme->getFile($path, $theme);

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

		return $this->output;
	}
}
