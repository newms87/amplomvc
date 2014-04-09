<?php
final class Registry
{
	public $data = array();

	public function get($key)
	{
		$lcase_key = strtolower($key);

		if (isset($this->data[$lcase_key])) {
			return $this->data[$lcase_key];
		}

		$instance = $this->loadClass($key);

		if ($instance) {
			$this->data[$lcase_key] = $instance;

			return $instance;
		}
	}

	public function set($key, $value)
	{
		$this->data[$key] = $value;
	}

	public function has($key)
	{
		return isset($this->data[$key]);
	}

	public function loadClass($class, $return_instance = true)
	{
		//TODO: Integrate this.... hack to incorporate AWS AutoLoading
		if (strpos($class, '\\') !== false) {
			$file = DIR_RESOURCES . 'aws/' . $class . '.php';
			if (is_file($file)) {
				require_once($file);
				return;
			}
		}

		//Resolve Model
		if (preg_match("/^(Model_|System_|Admin_|Catalog_)/", $class)) {
			if (strpos($class, "Model_") === 0) {
				$class = (defined("IS_ADMIN") ? 'Admin_' : 'Catalog_') . $class;
			}

			$path = explode("_", $class);

			array_walk($path, function (&$e, $index) {
				$e = preg_replace("/([a-z])([A-Z])/", "\$1_\$2", $e);
				$e = strtolower($e);
			});

			$file = DIR_SITE . implode('/', $path) . '.php';
		} //Resolve non-relative from root paths
		else {
			//Check in Library
			$file = DIR_SYSTEM . 'library/' . strtolower($class) . '.php';
		}

		//Check for relative path from root
		if (is_file($file)) {
			require_once(_ac_mod_file($file));


			if ($return_instance) {
				return new $class($this);
			}

			return true;
		}

		trigger_error(_l("Unable to resolve class %s. Failed to load class file.", $class));

		return false;
	}

	public function resource($name)
	{
		if (is_file(DIR_RESOURCES . $name . '.php')) {
			include_once (DIR_RESOURCES . $name . '.php');
		} else {
			include_once (DIR_RESOURCES . $name);
		}
	}
}
