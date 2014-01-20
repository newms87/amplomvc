<?php
abstract class System_Extension_Extension extends Model
{
	static $extension_info = array();

	private $type;
	private $code;
	protected $info;
	protected $settings;

	public function __construct($registry)
	{
		parent::__construct($registry);

		$this->loadInfo();
	}

	public function has($code)
	{
		return $this->System_Extension_Model->extensionExists($this->type, $code);
	}

	public function get($code)
	{
		return $this->registry->get('System_Extension_' . $this->type . '_' . $this->tool->formatClassname($code));
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
		$matches = null;
		preg_match("/System_Extension_([a-z]+)_?(.*)/i", get_class($this), $matches);

		$this->type = strtolower($matches[1]);

		//Load Information for Payment Extension
		if (!empty($matches[2])) {
			$this->code = strtolower($matches[2]);

			//Load Information for all of this extension type (we'll probably need all of it anyway!)
			if (empty(self::$extension_info[$this->type])) {
				self::$extension_info[$this->type] = $this->cache->get('extension.model.' . $this->type);

				if (empty(self::$extension_info[$this->type])) {
					self::$extension_info[$this->type] = $this->queryRows("SELECT * FROM " . DB_PREFIX . "extension WHERE `type` = '" . $this->escape($this->type) . "' ORDER BY sort_order ASC", 'code');

					$this->cache->set('extension.model.' . $this->type, self::$extension_info[$this->type]);
				}
			}



			//The Code may be in format extcode or ext_code, which will both have class name System_Extension_Type_Extcode
			//(note: in PHP class names are case insensitive)
			foreach (self::$extension_info[$this->type] as $ext_code => $ext_info) {
				if (strtolower(str_replace('_','',$ext_code)) === $this->code) {
					$this->info = $ext_info;
				}
			}

			if (!$this->info) {
				html_dump(self::$extension_info[$this->type], 'ext_info for ' . $this->type . '/' . $this->code);
				trigger_error(_l("The extension %s was not installed!", $this->code));
			}
			else {
				$this->settings = unserialize($this->info['settings']);
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
