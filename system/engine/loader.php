<?php
final class Loader {
	protected $registry;
	
	public function __construct(&$registry) {
		$this->registry = &$registry;
	}
	
	public function __get($key) {
		return $this->registry->get($key);
	}

	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}
	
	public function library($library) {
		$library = strtolower($library);
		
		$file = DIR_SYSTEM . 'library/' . $library . '.php';
		
		if (file_exists($file)) {
			_require_once($file);
			
			$class = new $library($this->registry);
			
			$this->registry->set($library, $class);
			
			return $class;
			
		} else {
			trigger_error('Could not load library ' . $library . '! ' . get_caller(3), E_USER_WARNING);
			return null;
		}
	}
	
	public function model($model) {
		
		if(strpos($model,'/')){
			$model_name = 'model_' . str_replace('/', '_', $model);
		
			if(is_object($this->$model_name)){
				return $this->$model_name;
			}
			
			$file  = DIR_APPLICATION . 'model/' . $model . '.php';
		}
		else{
			$model_name = $model;
			
			$model_file = substr($model, 6);
			
			$file = DIR_APPLICATION . 'model/';
			
			$occur = 0;
			
			do{
				$model = $model_file;
				
				$occur = strpos($model, '_', $occur+1);
				
				if(!$occur){
					break;
				}
				
				$model[$occur] = '/';
			}
			while(!is_file($file . $model . '.php'));
			
			$file .= $model . '.php';
		}
		
		$class = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', $model);
		
		if (file_exists($file)) {
			_require_once($file);
			
			$model = new $class($this->registry);
			
			$this->registry->set($model_name, $model);
			
			return $model;
		} else {
			list(,$caller) = debug_backtrace(false);
			trigger_error('Error: Could not load model ' . $model . '! In ' . $caller['file'] . ' on line ' . $caller['line'] . ': ' . html_backtrace(5,-1,false));
			exit();
		}
	}
	
	public function database($driver, $hostname, $username, $password, $database, $prefix = NULL, $charset = 'UTF8') {
		$file  = DIR_SYSTEM . 'database/' . $driver . '.php';
		$class = 'Database' . preg_replace('/[^a-zA-Z0-9]/', '', $driver);
		
		if (file_exists($file)) {
			include_once($file);
			
			$this->registry->set(str_replace('/', '_', $driver), new $class());
		} else {
			trigger_error('Error: Could not load database ' . $driver . '!');
			exit();
		}
	}
	
	public function language($language) {
		return $this->language->load($language);
	}
	
	public function plugin_language($name, $language){
		return $this->language->plugin($name, $language);
	}
}
