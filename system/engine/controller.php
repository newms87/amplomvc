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

	protected function render($path, $data = array())
	{
		//TODO All validation should be done in Model! Remove this after removing all validation methods.
		//Display Error Messages
		if (!isset($data['errors'])) {
			$data['errors'] = array();

			if ($this->error) {
				if (!$this->request->isAjax()) {
					$this->message->add('warning', $this->error);
				}

				$data['errors'] = $this->error;

				$this->error = array();
			}
		}

		//Empty Dependencies and Breadcrumbs if an ajax request
		if ($this->request->isAjax()) {
			$this->breadcrumb->clear();
		}

		$_template = $this->theme->findFile($path);

		if (!$_template || !is_file($_template)) {
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
