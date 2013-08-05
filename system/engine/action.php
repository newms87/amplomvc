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
		$this->registry = $registry;
		$this->file = null;
		$this->path = $path;
		$this->parameters = $parameters;
		$this->method = 'index';
		
		if (!$classpath) {
			$this->classpath = ($this->config->isAdmin() ? "admin/" : "catalog/") . "controller/";
		} else {
			$this->classpath = rtrim($classpath,'/') . '/';
		}
		
		$parts = explode('/', str_replace('../', '', $this->classpath . $this->path));
		
		$filepath = '';
		
		foreach ($parts as $part) {
			$filepath .= $part;
			
			//Scan directories until we find file requested
			if (is_dir(SITE_DIR . $filepath)) {
				$filepath .= '/';
				$this->class .= $this->tool->formatClassname($part) . '_';
			}
			elseif (is_file(SITE_DIR . $filepath . '.php')) {
				$this->file = SITE_DIR . $filepath . '.php';
				
				$this->class .= $this->tool->formatClassname($part);
			}
			elseif ($this->file) {
				$this->method = $part;
				$this->classpath = str_replace('/'.$part, '', $this->classpath);
				break;
			}
			else {
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
				_require($this->file);
				
				$class = $this->class;
				
				$this->controller = new $class($this->registry);
			} else {
				if (!$this->file) {
					trigger_error("Failed to load controller {$this->class} because the file was not resolved! Please verify {$this->path} is a valid controller." . get_caller(0, 2));
				} else {
					trigger_error("Failed to load controller {$this->class} because the file {$this->file} is missing!");
				}
			}
		}
		
		return $this->controller;
	}
	
	public function execute()
	{
		$controller = $this->getController();
		
		if (is_callable(array($controller, $this->method))) {
			call_user_func_array(array($controller, $this->method), $this->parameters);
			
			$this->output = $controller->output;
			
			return true;
		}
		
		trigger_error("The method $this->method() was not callable in $this->class. Please make sure it is a public method!");
		
		return false;
	}
	
	public function getOutput()
	{
		return $this->output;
	}
}
