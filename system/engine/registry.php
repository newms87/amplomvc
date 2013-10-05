<?php
final class Registry
{
	private $data = array();

	public function get($key)
	{
		if (isset($this->data[$key])) {
			return $this->data[$key];
		}
		elseif (strpos($key, "System_Extension_") === 0) {
			require_once(_ac_mod_file(DIR_SYSTEM . "extension/extension_model.php"));
			require_once(_ac_mod_file(DIR_SYSTEM . "extension/extension.php"));

			return $this->data['load']->model($key);
		}
		elseif (preg_match("/^(Model_|System_|Admin_|Catalog_)/", $key)) {
			return $this->data['load']->model($key);
		}
		else {
			return $this->data['load']->library($key);
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
}
