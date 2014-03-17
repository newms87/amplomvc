<?php
final class Action
{
	private $registry;
	private $file;
	private $path;
	private $class;
	private $classpath;
	private $controller;
	private $method;
	private $parameters = array();
	private $output;

	public function __construct($registry, $path, $parameters = array(), $classpath = '')
	{
		$this->registry   = $registry;
		$this->file       = null;
		$this->path       = $path;
		$this->parameters = $parameters;
		$this->method     = 'index';

		if (!$classpath) {
			$this->classpath = ($this->config->isAdmin() ? "admin/" : "catalog/") . "controller/";
		} else {
			$this->classpath = rtrim($classpath, '/') . '/';
		}

		$parts = explode('/', str_replace('../', '', $this->classpath . $this->path));

		$filepath = '';

		$count = count($parts);

		for ($i = 0; $i < $count; $i++) {
			$part = $parts[$i];

			$filepath .= $part;

			$is_file = is_file(DIR_SITE . $filepath . '.php');
			$is_dir  = is_dir(DIR_SITE . $filepath);

			$next = ($i < ($count - 1)) ? DIR_SITE . $filepath . '/' . $parts[$i + 1] : '';

			//Scan directories until we find file requested
			//If part is a directory AND either not a file, or a file and the next part is a file or directory, assume the part is a directory
			if ($is_dir &&
				(!$is_file || ($is_file && $is_dir && ($i < ($count - 1)) && (is_file($next . '.php') || is_dir($next))))
			) {
				$filepath .= '/';
				$this->class .= $this->tool->_2CamelCase($part) . '_';
			} elseif ($is_file) {
				$this->file = DIR_SITE . $filepath . '.php';

				$this->class .= $this->tool->_2CamelCase($part);
			} elseif ($this->file) {
				$this->method    = $part;
				$this->classpath = str_replace('/' . $part, '', $this->classpath);
				break;
			} else {
				return false;
			}
		}
	}

	public function __get($key)
	{
		return $this->registry->get($key);
	}

	public function isValid()
	{
		return $this->file ? true : false;
	}

	public function getFile()
	{
		return $this->file;
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
			if (is_file($this->file)) {
				require_once(_ac_mod_file($this->file));

				$class = $this->class;

				$this->controller = new $class($this->registry);
			} else {
				if (!$this->file) {
					trigger_error("Failed to load controller {$this->class} because the file was not resolved! Please verify {$this->path} is a valid controller.");
				} else {
					trigger_error("Failed to load controller {$this->class} because the file {$this->file} is missing!");
				}
			}
		}

		return $this->controller;
	}

	public function execute()
	{
		global $language_group;

		$controller = $this->getController();

		if (is_callable(array(
			$controller,
			$this->method
		))
		) {
			//Set our language group for translations
			$language_group = $this->class;

			call_user_func_array(array(
				$controller,
				$this->method
			), $this->parameters);

			$this->output = $controller->output;

			return true;
		}

		trigger_error(_l("The method %s() was not callable in %s. Please make sure it is a public method!", $this->method, $this->class));

		return false;
	}

	public function getOutput()
	{
		return $this->output;
	}
}
