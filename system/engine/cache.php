<?php

class Cache
{
	private $expired, $dir;
	private $loaded = array();

	public function __construct($dir = null)
	{
		$this->expired = _time() - CACHE_FILE_EXPIRATION;

		$this->setDir($dir ? $dir : DIR_CACHE . DB_PREFIX);
	}

	public function setDir($dir)
	{
		if (_is_writable($dir, $error)) {
			$this->dir = rtrim($dir, '/') . '/';
			return true;
		}

		trigger_error($error);

		return false;
	}

	public function getLoadedFiles()
	{
		return isset($this->loaded[$this->dir]) ? $this->loaded[$this->dir] : array();
	}

	public function get($key, $get_file = false)
	{
		if (isset($this->loaded[$this->dir][$key])) {
			if ($get_file) {
				return $this->loaded[$this->dir][$key]['file'];
			} elseif (isset($this->loaded[$this->dir][$key]['data'])) {
				return $this->loaded[$this->dir][$key]['data'];
			}
		}

		$file = $this->dir . $key . '.cache';

		if (is_file($file)) {
			if (_filemtime($file) < $this->expired) {
				//Suppress warnings as this will fail under race conditions
				@unlink($file);
				return;
			}

			if ($get_file) {
				return $this->loaded[$this->dir][$key]['file'] = $file;
			} else {
				$str = @file_get_contents($file);
				$data = @unserialize($str);

				//Check for bad data
				if ($data === false && $str !== serialize(false)) {
					unlink($file);
					return null;
				}

				$this->loaded[$this->dir][$key]['data'] = $data;
				$this->loaded[$this->dir][$key]['file'] = $file;
			}

			return $this->loaded[$this->dir][$key]['data'];
		}
	}

	public function set($key, $value, $set_file = false)
	{
		$file = $this->dir . $key . '.cache';

		$this->loaded[$this->dir][$key]['data'] = $value;
		$this->loaded[$this->dir][$key]['file'] = $file;

		if (!$set_file) {
			$value = serialize($value);
		}

		if ($value !== null) {
			//TODO: Fails randomly (very rarely), for unknown reasons (probably race conditions). So lets silently fail as this is not critical.
			@file_put_contents($file, $value);
		}

		return $file;
	}

	public function delete($key)
	{
		if ($key) {
			$files = glob($this->dir . $key . '*.cache');

			if ($files) {
				foreach ($files as $file) {
					//Suppress warnings as this will fail under race conditions
					@unlink($file);
				}
			}

			if (!empty($this->loaded[$this->dir])) {
				foreach (array_keys($this->loaded[$this->dir]) as $lkey) {
					if (strpos($lkey, $key) === 0) {
						unset($this->loaded[$this->dir][$lkey]);
					}
				}
			}
		} else {
			rrmdir($this->dir);
		}
	}
}
