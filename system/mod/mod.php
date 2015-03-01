<?php
class System_Mod_Mod {
	protected $error = array();

	public function hasError()
	{
		return !empty($this->error);
	}

	public function getError()
	{
		return $this->error;
	}

	public function clearError()
	{
		$this->error = array();
	}

	public function apply($source, $mod)
	{
		return file_get_contents($mod);
	}
}
