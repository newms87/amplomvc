<?php
abstract class System_Extension_Extension extends Model
{
	static $extension_info = array();

	protected $type;
	protected $code;
	protected $info;
	protected $settings;

	public function __construct()
	{
		parent::__construct();

		$this->loadInfo();
	}

	public function has($code)
	{
		return $this->System_Extension_Model->extensionExists($this->type, $code);
	}

	public function get($code)
	{
		global $registry;
		return $registry->get('System_Extension_' . $this->type . '_' . $this->tool->_2CamelCase($code));
	}

	public function isActive()
	{
		return $this->info['status'];
	}

	public function getActive()
	{
		$active = $this->cache->get('extension.' . $this->type . '.active');

		if (is_null($active)) {
			$filter = array(
				'status' => 1,
			);

			$active = $this->System_Extension_Model->getExtensions($this->type, $filter);

			$this->cache->set('extension.' . $this->type . '.active', $active);
		}

		$extensions = array();

		foreach ($active as $code => $extension) {
			$extensions[$code] = $this->get($code);
		}

		return $extensions;
	}

	public function getCode()
	{
		return $this->info['code'];
	}

	public function info($key = null)
	{
		if ($key) {
			return isset($this->info[$key]) ? $this->info[$key] : null;
		}

		return $this->info;
	}

	private function loadInfo()
	{
		$this->settings = array();
		$matches = null;
		preg_match("/System_Extension_([a-z]+)_?(.*)/i", get_class($this), $matches);

		$this->type = strtolower($matches[1]);

		//Load Information for Payment Extension
		if (!empty($matches[2])) {
			$this->code = $this->tool->camelCase2_($matches[2]);

			$this->info = $this->System_Extension_Model->getExtension($this->type, $this->code);

			if (!$this->info) {
				trigger_error(_l("The extension %s was not installed!", $this->code));
			}
			else {
				$this->settings = $this->info['settings'];
			}
		}
	}

	public function settings($key = null)
	{
		if ($key) {
			return isset($this->settings[$key]) ? $this->settings[$key] : null;
		}

		return $this->settings;
	}
}
