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
			require_once(_ac_mod_file($file));

			$classname = preg_replace("/[^A-Z0-9]/i", '', $library);

			$class = new $classname($this->registry);

			$this->registry->set($library, $class);

			return $class;

		} else {
			trigger_error('Could not load library ' . $library . '!<br>' . get_caller(1, 3), E_USER_WARNING);
			return null;
		}
	}

	public function model($model)
	{
		if (preg_match("/^Model_/", $model)) {
			$model_class = ($this->config->isAdmin() ? 'Admin_' : 'Catalog_') . $model;
		} else {
			$model_class = $model;
		}

		$path = explode("_",$model_class);

		array_walk($path, function(&$e, $index){
			$e = preg_replace("/([a-z])([A-Z])/", "\$1_\$2", $e);
			$e = strtolower($e);
		});

		$file = SITE_DIR . implode('/', $path) . '.php';

		if (is_file($file)) {
			require_once(_ac_mod_file($file));

			$class = new $model_class($this->registry);

			$this->registry->set($model_class, $class);

			if ($model_class !== $model) {
				$this->registry->set($model, $class);
			}

			return $class;
		} else {
			trigger_error('Could not load model ' . $model . " (tried $model_class)!" . get_caller(0, 4) . html_backtrace(5,-1,false));
			exit();
		}
	}

	public function database($driver, $hostname, $username, $password, $database, $prefix = NULL, $charset = 'UTF8')
	{
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

	public function language($language)
	{
		return $this->language->load($language);
	}

	public function plugin_language($name, $language)
	{
		return $this->language->plugin($name, $language);
	}
}
