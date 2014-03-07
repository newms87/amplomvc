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

	public function get($key, $return_file = false)
	{
		if (isset($this->loaded[$key])) {
			if ($return_file) {
				return $this->loaded[$key]['file'];
			} elseif (isset($this->loaded[$key]['data'])) {
				return $this->loaded[$key]['data'];
			}
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

			if ($return_file) {
				return $this->loaded[$key]['file'] = $file;
			} else {
				$this->loaded[$key]['data'] = unserialize(@file_get_contents($file));
				$this->loaded[$key]['file'] = $file;
			}

			return $this->loaded[$key]['data'];
		}
	}

	public function set($key, $value)
	{
		$file = DIR_CACHE . $key . '.cache';

		$value = serialize($value);

		if ($value) {
			//TODO: Fails randomly (very rarely), for unknown reasons (probably race conditions). So lets silently fail as this is not critical.
			@file_put_contents($file, $value);
		}

		return $file;
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
