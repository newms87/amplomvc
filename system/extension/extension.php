<?php
abstract class Extension extends Controller
{
	protected $info;
	protected $settings;
	protected $language;

	public function __construct($registry)
	{
		parent::__construct($registry);

		//Create an independent copy of language for extensions avoid overwriting mistakes!)
		$lang = $registry->get('language');
		$this->language = new Language($registry, $lang->id(), false, false);
		$this->language->data = $lang->data;
	}

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
