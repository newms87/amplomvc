<?php

final class Action
{
	private $dir;
	private $file;
	private $path;
	private $class;
	private $classpath;
	private $controller;
	private $method;
	private $parameters = array();
	private $is_valid;
	private $output;

	public function __construct($path, $parameters = array())
	{
		$dir = DIR_SITE . 'app/controller/';

		$parts = explode('/', str_replace('-', '_', $path));

		$file      = '';
		$class     = 'App_Controller';
		$classpath = '';
		$method    = '';
		$args      = array();

		foreach ($parts as $part) {
			if (!$file) {
				if (is_dir($dir . $part)) {
					$dir .= $part . '/';
					$class .= '_' . str_replace('_', '', $part);
					$classpath .= $part . '/';
				} elseif (is_file($dir . $part . '.php')) {
					$file = $dir . $part . '.php';
					$class .= '_' . str_replace('_', '', $part);
					$classpath .= $part;
				} else {
					$method = $part;
					break;
				}

				continue;
			}

			if (!$method) {
				$method = $part;
			} else {
				$args[] = $part;
			}
		}

		if (!$file) {
			if (is_file(rtrim($dir, '/') . '.php')) {
				$file = rtrim($dir, '/') . '.php';
			} else {
				$this->is_valid = false;
				return;
			}
		}

		if (!$method) {
			$method = 'index';
		}

		require_once(_mod($file));

		if (class_exists($class . '_mod', false)) {
			$class .= '_mod';
		}

		$callable = array(
			$class,
			$method
		);

		$this->is_valid = is_callable($callable);

		if (!$this->is_valid) {
			if (!class_exists($class, false)) {
				trigger_error(_l("The class %s does not exist! Make sure you spelled the class name correctly in %s", $class, $file));
			} elseif (method_exists($class, $method)) {
				trigger_error(_l("The method %s() was not callable in %s. Please make sure it is a public method!", $method, $class));
			}
		}

		$this->dir        = $dir;
		$this->file       = $file;
		$this->path       = $path;
		$this->classpath  = $classpath;
		$this->class      = $class;
		$this->method     = $method;
		$this->parameters = $parameters ? $parameters : $args;
	}

	public function __get($key)
	{
		global $registry;
		return $registry->get($key);
	}

	public function isValid()
	{
		return $this->is_valid;
	}

	public function getDir()
	{
		return $this->dir;
	}

	public function getFile()
	{
		return $this->file;
	}

	public function getPath()
	{
		return $this->path;
	}

	public function getClass()
	{
		return $this->class;
	}

	public function getClassPath()
	{
		return $this->classpath;
	}

	public function getMethod()
	{
		return $this->method;
	}

	public function getParameters()
	{
		return $this->parameters;
	}

	public function getController()
	{
		if (!$this->controller) {
			$class            = $this->class;
			$this->controller = new $class();
		}

		return $this->controller;
	}

	public function execute($is_ajax = null)
	{
		global $language_group;

		if ($this->is_valid) {
			$controller = $this->getController();

			if (isset($is_ajax)) {
				$controller->is_ajax = $is_ajax;
			}

			//Set our language group for translations
			$language_group = $this->class;

			$callable = array(
				$controller,
				$this->method,
			);

			$output = call_user_func_array($callable, $this->parameters);

			$this->output = $controller->output ? $controller->output : $output;

			return true;
		}

		return false;
	}

	public function getOutput()
	{
		return $this->output;
	}
}
