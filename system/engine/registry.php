<?php
final class Registry
{
	protected $data = array();

	public function get($key)
	{
		$lcase_key = strtolower($key);

		if (!isset($this->data[$lcase_key])) {
			$this->data[$lcase_key] = $this->loadClass($key);
		}

		return $this->data[$lcase_key];
	}

	public function set($key, $value)
	{
		$this->data[strtolower($key)] = $value;
	}

	public function has($key)
	{
		return isset($this->data[$key]);
	}

	public function loadClass($class, $return_instance = true)
	{
		//So a child instance may call the registry directly via __get()
		if ($class === 'load') {
			return $this;
		}

		if (class_exists($class, false)) {
			if (class_exists($class . '_mod', false)) {
				$class .= '_mod';
			}

			return $return_instance ? new $class() : true;
		}

		//Check for file in library
		$file = DIR_SYSTEM . 'library/' . strtolower($class) . '.php';

		//If not in library, check in Model
		if (!is_file($file)) {
			if (preg_match("/^model_/i", $class)) {
				$class = 'App_' . $class;
			}

			$path = explode("_", $class);

			foreach ($path as &$p) {
				$p = strtolower(camel2_($p));
			}
			unset($p);

			$file = DIR_SITE . implode('/', $path) . '.php';
		} else {
			$class = str_replace('_', '', $class);
		}

		//Load from Resources
		if (!is_file($file) && is_file(DIR_RESOURCES . $class . '.php')) {
			$file = DIR_RESOURCES . $class . '.php';
		}

		//Check for relative path from root
		if (is_file($file)) {
			$mod = _mod($file);

			if (pathinfo($mod, PATHINFO_EXTENSION) === 'mod') {
				require_once($file);
				$class .= "_mod";
			}

			require_once($mod);

			if ($return_instance) {
				return new $class();
			}

			return true;
		}

		trigger_error(_l("Unable to resolve class %s. Failed to load class file %s.", $class, $file));

		return false;
	}

	public function load($path, $class)
	{
		if (!$this->has($class)) {
			if (!is_file($path)) {
				$path = DIR_SITE . $path . '.php';

				if (!is_file($path)) {
					return null;
				}
			}

			require_once($path);

			$this->set($class, new $class());
		}

		return $this->get($class);
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
