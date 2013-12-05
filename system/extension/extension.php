<?php
abstract class System_Extension_Extension extends Controller
{
	protected $info;
	protected $settings;

	public function isActive()
	{
		return $this->info['status'];
	}

	public function getCode()
	{
		return $this->info['code'];
	}

	public function info()
	{
		return $this->info;
	}

	public function setInfo($info)
	{
		$this->info = $info;

		if (isset($info['settings'])) {
			$this->settings = $info['settings'];
		}
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
		return $this->settings;
	}
}
