<?php
final class Registry 
{
	private $data = array();
	
	public function get($key)
	{
		if (isset($this->data[$key])) {
			return $this->data[$key];
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
