<?php
abstract class Extension extends Model
{
	private $info;

	public function setInfo($info)
	{
		$this->info = $info;
	}

	public function getInfo($key = null)
	{
		if ($key) {
			return isset($this->info[$key]) ? $this->info[$key] : null;
		}

		return $this->info;
	}

	public function getSettings()
	{
		return $this->info['settings'];
	}
}