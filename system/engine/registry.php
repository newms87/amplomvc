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
		$this->data[$key] = $value;
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

		//TODO: Integrate this.... hack to incorporate AWS AutoLoading
		if (strpos($class, '\\') !== false) {
			$file = DIR_RESOURCES . 'aws/' . $class . '.php';
			if (is_file($file)) {
				require_once($file);
				return;
			}
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
				$p = strtolower($this->get('tool')->camelCase2_($p));
			}
			unset($p);

			$file = DIR_SITE . implode('/', $path) . '.php';
		} else {
			$class = str_replace('_', '', $class);
		}

		//Check for relative path from root
		if (is_file($file)) {
			$acmod = _ac_mod_file($file);

			if (pathinfo($acmod, PATHINFO_EXTENSION) === 'acmod') {
				require_once($file);
				$class .= "_acmod";
			}

			require_once($acmod);

			if ($return_instance) {
				return new $class();
			}

			return true;
		}

		trigger_error(_l("Unable to resolve class %s. Failed to load class file %s.", $class, $file));

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
