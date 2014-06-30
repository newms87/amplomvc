<?php

abstract class Controller
{
	public $output;
	public $error = array();

	public function __construct() {}

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

	protected function render($path, $data = array())
	{
		//TODO All validation should be done in Model! Remove this after removing all validation methods.
		//Display Error Messages
		if (!isset($data['errors'])) {
			$data['errors'] = array();

			if ($this->error) {
				if (!IS_AJAX) {
					message('warning', $this->error);
				}

				$data['errors'] = $this->error;

				$this->error = array();
			}
		}

		$_template = $this->theme->getFile($path);

		if (!$_template) {
			trigger_error(_l("%s(): Could not resolve template path %s", __METHOD__, $path));
			exit();
		}

		extract($data);

		ob_start();

		include(_ac_mod_file($_template));

		$this->output = ob_get_clean();

		return $this->output;
	}
}
