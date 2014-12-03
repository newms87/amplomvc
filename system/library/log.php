<?php

class Log
{
	private $file;
	private $store_id;

	public function __construct($name, $store_id = 0)
	{
		$this->file = DIR_LOGS . $name . '.txt';

		if (!_is_writable(dirname($this->file))) {
			trigger_error(_l("Log file directory was not writable: %s", $this->file));
			$this->file = '';
		} elseif (!is_file($this->file)) {
			touch($this->file);
			chmod($this->file, 0755);
		}

		$this->store_id = $store_id;
	}

	public function write($message)
	{
		if ($this->file) {
			$handle = fopen($this->file, 'a+');

			$log = date('Y-m-d G:i:s');
			$log .= "\t" . $_SERVER['REMOTE_ADDR'];
			$log .= "\t" . preg_replace("/\\?.*/", "", $_SERVER['REQUEST_URI']);
			$log .= "\t" . $_SERVER['QUERY_STRING'];
			$log .= "\t" . "Store ID: $this->store_id";
			$log .= "\t" . (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
			$log .= "\t" . str_replace("\n", "__nl__", str_replace("\r", '', $message));

			fwrite($handle, $log . "\r\n");

			fclose($handle);
		}
	}
}
