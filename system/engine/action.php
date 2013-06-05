<?php
final class Action 
{
	private $registry;
	private $file;
	private $route;
	private $class;
	private $class_path;
	private $method;
	private $parameters = array();
	private $output;

	public function __construct($registry, $route, $parameters = array())
	{
		$this->registry = $registry;
		$this->route = $route;
		$this->class_path = ($this->config->isAdmin() ? "admin/" : "catalog/") . "controller/";
		$this->file = null;
		$this->class = ($this->config->isAdmin() ? "Admin_" : "Catalog_") . "Controller_";
		$this->method = 'index'; 
		
		if (!empty($parameters)) {
			$this->parameters = $parameters;
		}
		
		$parts = explode('/', str_replace('../', '', (string)$route));
		
		$path = $this->class_path;
		
		foreach ($parts as $part) {
			$path .= $part;
			
			//Scan directories until we find file requested
			if (is_dir(SITE_DIR . $path)) {
				$path .= '/';
				$this->class_path .= $part . '/';
				$this->class .= $this->tool->format_classname($part) . '_';
			}
			elseif (is_file(SITE_DIR . $path . '.php')) {
				$this->file = SITE_DIR . $path . '.php';
				
				$this->class .= $this->tool->format_classname($part);
			}
			else {
				$this->method = $part;
				break;
			}
		}
	}
	
	public function __get($key)
	{
		return $this->registry->get($key);
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
		return $this->class_path;
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
		if (is_file($this->file)) {
			_require_once($this->file);
			
			$class = $this->class;
			
			return new $class($this->registry);
		} else {
			if (!$this->file) {
				trigger_error("Failed to load controller {$this->class} because the file was not resolved! Please verify {$this->route} is a valid controller.<br>" . get_caller() . '<br />' . get_caller(1) . '<br />');
			} else {
				trigger_error("Failed to load controller {$this->class} because the file {$this->file} is missing!");
			}
			exit();
		}
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
