<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC
 * @link http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYING.txt and LICENSE.txt files in the root directory.
 */

final class Registry
{
	protected $data = array();

	public function __get($key)
	{
		return $this->get($key);
	}

	private function formatKey($key)
	{
		return str_replace('app_model_', 'model_', strtolower($key));
	}

	public function get($key, $return_instance = true)
	{
		$lcase_key = $this->formatKey($key);

		if (!isset($this->data[$lcase_key])) {
			if (AMPLO_PROFILE) {
				_profile('loading (' . $key . ')');
			}

			if ($return_instance) {
				$this->data[$lcase_key] = $this->loadClass($key, $return_instance);
			} else {
				return $this->loadClass($key, false);
			}

			if (AMPLO_PROFILE) {
				_profile('loaded (' . $key . ')');
			}
		}

		return $this->data[$lcase_key];
	}

	public function set($key, $value)
	{
		$this->data[$this->formatKey($key)] = $value;
	}

	public function has($key)
	{
		$key = $this->formatKey($key);
		return isset($this->data[$key]);
	}

	protected function loadClass($class, $return_instance = true)
	{
		//So a child instance may call the registry directly via __get()
		if ($class === 'load') {
			return $this;
		}

		$class = str_replace('\\', '/', $class);

		if (class_exists($class, false)) {
			if (class_exists($class . '_ext', false)) {
				$class .= '_ext';
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

		$l_class = strtolower($class);

		//Load from Resources
		if (!is_file($file)) {
			if (is_file(DIR_RESOURCES . $class . '.php')) {
				$file = DIR_RESOURCES . $class . '.php';
			} elseif (is_file(DIR_RESOURCES . $l_class . '.php')) {
				$file = DIR_RESOURCES . $l_class . '.php';
			}
		}

		if (!is_file($file)) {
			trigger_error(_l("Unable to resolve class %s. Failed to load class file %s.", $class, $file));
			return false;
		}

		require_once(_mod($file));

		if (class_exists($class . '_ext', false)) {
			$class .= '_ext';
		}

		//Return new instance
		if ($return_instance) {
			return new $class();
		}

		return true;
	}

	public function load($path, $class = null)
	{
		if (!$class) {
			$class = path2class($path);
		}

		if (!$this->has($class)) {
			if (!is_file($path)) {
				$path = DIR_SITE . $path . '.php';

				if (!is_file($path)) {
					return null;
				}
			}

			if (AMPLO_PROFILE) {
				_profile('loading (' . $class . ')');
			}

			require_once($path);

			$this->set($class, new $class());

			if (AMPLO_PROFILE) {
				_profile('loaded (' . $class . ')');
			}
		}

		return $this->get($class);
	}

	public function resource($name)
	{
		if (is_file(DIR_RESOURCES . $name . '.php')) {
			include_once(DIR_RESOURCES . $name . '.php');
		} else {
			include_once(DIR_RESOURCES . $name);
		}
	}
}
