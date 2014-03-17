<?php

abstract class Controller
{
	protected $registry, $load;
	protected $children = array();
	public $output;
	public $view;
	public $data = array();
	public $error = array();

	public function __construct($registry)
	{
		$this->registry = $registry;
		$this->load = $registry;

		$this->view = new View($registry);
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

	//TODO: move this to block plugin!
	protected function getBlock($path, $args = array(), $settings = null)
	{
		$block = 'block/' . $path;

		if (!is_array($args)) {
			trigger_error(_l("%s(): In Controller %s while retrieving block %s. Parameters (%s) passed to must be an array to forward to the block method.", __METHOD__, get_class($this), $block, $args));
			exit();
		}

		if (is_null($settings)) {
			$settings = $this->Model_Block_Block->getBlockSettings($path);
		}

		if ($settings) {
			$settings = $args + $settings;
		} else {
			$settings = $args;
		}

		$action = new Action($this->registry, $block, array('settings' => $settings));

		if ($action->execute()) {
			return $action->getOutput();
		} else {
			trigger_error(_l("%s(): Could not load block %s! The file was missing.", __METHOD__, $block));
		}
	}

	protected function call($child, $parameters = array())
	{
		if (!is_array($parameters)) {
			$parameters = array($parameters);
		}

		$action = new Action($this->registry, $child, $parameters);

		if ($action->execute()) {
			return $action->getOutput();
		} else {
			trigger_error('Could not load controller ' . $child . '!');
			exit();
		}
	}

	protected function render($template = null, $data = array())
	{
		$data += $this->data;

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

			foreach ($this->children as $child) {
				$data[str_replace('/','_',$child)] = '';
			}
		} else {
			//Render Dependencies
			foreach ($this->children as $child) {
				$data[str_replace('/','_',$child)] = $this->call($child);
			}
		}

		$this->output = $this->view->render($template, $data);

		return $this->output;
	}
}
