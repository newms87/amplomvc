<?php

class Log
{
	private $file;

	static $cols = array(
		'date',
		'ip',
		'uri',
		'query',
		'agent',
		'message',
	);

	public function __construct($name)
	{
		$this->file = DIR_LOGS . (defined('SITE_PREFIX') ? SITE_PREFIX : DB_PREFIX) . '/' . $name . '.txt';

		if (!_is_writable(dirname($this->file))) {
			trigger_error(_l("Log file directory was not writable: %s", $this->file));
			$this->file = '';
		} elseif (!is_file($this->file)) {
			touch($this->file);
			chmod($this->file, 0755);
		}
	}

	public function write($message)
	{
		if ($this->file) {
			$handle = fopen($this->file, 'a+');

			$log = date('Y-m-d G:i:s');
			$log .= "\t" . $_SERVER['REMOTE_ADDR'];
			$log .= "\t" . preg_replace("/\\?.*/", "", $_SERVER['REQUEST_URI']);
			$log .= "\t" . $_SERVER['QUERY_STRING'];
			$log .= "\t" . (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
			$log .= "\t" . str_replace("\n", "__nl__", str_replace("\r", '', $message));

			fwrite($handle, $log . "\r\n");

			fclose($handle);
		}
	}
}
