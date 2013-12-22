<?php
class Cache
{
	private $expired;
	private $ignore_list = array();
	private $loaded = array();

	public function __construct()
	{
		$this->expired = _time() - CACHE_FILE_EXPIRATION;

		_is_writable(DIR_CACHE);
	}

	public function get($key)
	{
		if (isset($this->loaded[$key])) {
			return $this->loaded[$key]['data'];
		}

		$file = DIR_CACHE . $key . '.cache';

		if (is_file($file)) {
			if (_filemtime($file) < $this->expired) {
				//Suppress warnings as this will fail under race conditions
				@unlink($file);
				return;
			}

			foreach ($this->ignore_list as $ignore) {
				if (strpos($key, $ignore) === 0) {
					return;
				}
			}

			$this->loaded[$key]['data'] = unserialize(@file_get_contents($file));
			$this->loaded[$key]['file'] = $file;

			return $this->loaded[$key]['data'];
		}
	}

	public function set($key, $value)
	{
		$file = DIR_CACHE . $key . '.cache';

		file_put_contents($file, serialize($value));
	}

	public function delete($key)
	{
		$files = glob(DIR_CACHE . $key . '*.cache');

		if ($files) {
			clearstatcache();

			foreach ($files as $file) {
				//Suppress warnings as this will fail under race conditions
				@unlink($file);
			}
		}

		unset($this->loaded[$key]);
	}

	public function ignore($ignore)
	{
		foreach (explode(',', $ignore) as $key) {
			$key = trim($key);

			if ($key) {
				$this->ignore_list[$key] = $key;
			}
		}
	}
}
