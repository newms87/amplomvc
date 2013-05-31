<?php
final class Loader 
{
	protected $registry;
	
	public function __construct($registry)
	{
		$this->registry = $registry;
	}
	
	public function __get($key)
	{
		return $this->registry->get($key);
	}

	public function __set($key, $value)
	{
		$this->registry->set($key, $value);
	}
	
	public function library($library)
	{
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
	
	public function model($model)
	{
		if (preg_match("/^Model_/", $model)) {
			$model_class = (defined("IS_ADMIN") ? 'Admin_' : 'Catalog_') . $model;
		} else {
			$model_class = $model;
		}
		
		$path = str_replace("_",'/', $model_class);
		
		$file = SITE_DIR . strtolower($path) . '.php';
		
		if (!is_file($file)) {
			$path = preg_replace("/([A-Z]?[a-z])*([A-Z][a-z]*)\$/", '$1_$2', $path);
			
			$file = SITE_DIR . strtolower($path) . '.php';
		}
		
		if (is_file($file)) {
			_require_once($file);
			
			$class = new $model_class($this->registry);
			
			$this->registry->set($model_class, $class);
			
			if ($model_class !== $model) {
				$this->registry->set($model, $class);
			}
			
			return $class;
		} else {
			trigger_error('Could not load model ' . $model . '! ' . get_caller() . html_backtrace(5,-1,false));
			exit();
		}
	}
	
	public function database($driver, $hostname, $username, $password, $database, $prefix = NULL, $charset = 'UTF8')
	{
		$file  = DIR_SYSTEM . 'database/' . $driver . '.php';
		$class = 'Database' . preg_replace('/[^a-zA-Z0-9]/', '', $driver);
		
		if (file_exists($file)) {
			include_once($file);
			
			$this->registry->set(str_replace('/', '_', $driver), new $class ());
		} else {
			trigger_error('Error: Could not load database ' . $driver . '!');
			exit();
		}
	}
	
	public function language($language)
	{
		return $this->language->load($language);
	}
	
	public function plugin_language($name, $language)
	{
		return $this->language->plugin($name, $language);
	}
}
