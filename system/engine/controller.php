<?php

abstract class Controller
{
	protected $load;
	protected $children = array();
	public $output;
	public $error = array();

	public function __construct()
	{
		global $registry;
		$this->load = $registry;
	}

	public function __get($key)
	{
		global $registry;
		return $registry->get($key);
	}

	public function __set($key, $value)
	{
		//TODO __set() has been deprecated for AmploCart. DO NOT USE THIS FEATURE.
		trigger_error("__set() is deprecated in AmploCart. This feature has been disabled.");
		exit;
	}

	public function getError()
	{
		return $this->error;
	}

	public function getErrorMsg($delimiter = "\r\n")
	{
		$msg = '';

		array_walk_recursive($this->error, function ($value, $id, &$msg) use ($delimiter) { $msg .= ($msg ? $delimiter : '') . $value; });

		return $msg;
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

		$template = $this->theme->findFile($path);

		if (!$template || !is_file($template)) {
			trigger_error(_l("%s(): Could not resolve template path %s", __METHOD__, $path));
			exit();
		}

		extract($data);

		ob_start();

		include(_ac_mod_file($template));

		$this->output = ob_get_clean();

		return $this->output;
	}
}
