<?php
final class Action {
	protected $file;
	protected $class;
	protected $class_path;
	protected $method;
	protected $args = array();

	public function __construct($route, $args = array()) {
		
		$path = '';
		
		$parts = explode('/', str_replace('../', '', (string)$route));
		
		foreach ($parts as $part) { 
			$path .= $part;
			
			if (is_dir(DIR_APPLICATION . 'controller/' . $path)) {
				$path .= '/';
				
				array_shift($parts);
				
				continue;
			}
			
			$file = '';
			$file_path = DIR_APPLICATION . 'controller/' . str_replace('../', '', $path) . '.php';
			if(is_file($file_path))
				$file = $file_path;
			
			if ($file) {
				$this->file = $file;
				
				$this->class_path = $path;
				
				$this->class = 'Controller' . preg_replace('/[^a-zA-Z0-9]/', '', $path);

				array_shift($parts);
				
				break;
			}
		}
		
		if ($args) {
			$this->args = $args;
		}
			
		$method = array_shift($parts);
				
		if ($method) {
			$this->method = $method;
		} else {
			$this->method = 'index';
		}
		
	}
	
	public function getFile() {
		return $this->file;
	}
	
	public function getClass() {
		return $this->class;
	}
	
	public function getClassPath(){
		return $this->class_path;  
	}
	
	public function getMethod() {
		return $this->method;
	}
	
	public function getArgs() {
		return $this->args;
	}
}
