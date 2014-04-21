<?php

abstract class Controller
{
	protected $registry, $load;
	protected $children = array();
	public $output;
	public $error = array();

	public function __construct($registry)
	{
		$this->registry = $registry;
		$this->load = $registry;
	}

	public function __get($key)
	{
		return $this->registry->get($key);
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

	protected function call($path, $parameters = array())
	{
		if (!is_array($parameters)) {
			$parameters = array($parameters);
		}

		$action = new Action($this->registry, $path, $parameters);

		if ($action->execute()) {
			return $action->getOutput();
		} else {
			trigger_error('Could not load controller ' . $path . '!');
			exit();
		}
	}

	protected function render($path, $data = array())
	{
		//TODO All validation should be done in Model! Remove this after removing all validation methods.
		//Display Error Messages
		$data['errors'] = array();

		if ($this->error) {
			if (!$this->request->isAjax()) {
				$this->message->add('warning', $this->error);
			}

			$data['errors'] = $this->error;

			$this->error = array();
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
