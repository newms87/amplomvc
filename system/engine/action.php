<?php
final class Action 
{
	private $registry;
	protected $file;
	protected $class ;
	protected $class_path;
	protected $method;
	protected $parameters = array();
	private $output;

	public function __construct($registry, $route, $parameters = array())
	{
		$this->registry = $registry;
		$this->class_path = (defined("IS_ADMIN") ? "admin/" : "catalog/") . "controller/";
		$this->file = null;
		$this->class = (defined("IS_ADMIN") ? "Admin_" : "Catalog_") . "Controller_";
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
				$this->class .= ucfirst($part) . '_';
			}
			elseif (is_file(SITE_DIR . $path . '.php')) {
				$this->file = SITE_DIR . $path . '.php';
				
				$class_parts = explode('_', $part);
				
				//capitalize each component of the class name
				array_walk($class_parts, function(&$e, $i){ $e = ucfirst($e); });
				
				$this->class .= implode('', $class_parts);
			}
			else {
				$this->method = $part;
				break;
			}
		}
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
	
	public function execute()
	{
		if (file_exists($this->file)) {
			_require_once($this->file);

			$class = $this->class;
			
			$controller = new $class($this->registry);
			
			if (is_callable(array($controller, $this->method))) {
				call_user_func_array(array($controller, $this->method), $this->parameters);
				
				$this->output = $controller->output;
				
				return true;
			}
		}
		
		return false;
	}
	
	public function getOutput()
	{
		return $this->output;
	}
}
