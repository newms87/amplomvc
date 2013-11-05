<?php
class Log
{
	private $file;
	private $store_id;

	public function __construct($filename, $store_id = 0)
	{
		$this->file = realpath(DIR_LOGS . $filename);

		if (!$this->file) {
			$this->file = DIR_LOGS . basename($filename);
		}

		_is_writable(dirname($this->file));

		if (!is_file($this->file)) {
			touch($this->file);
			chmod($this->file, 0755);
		}

		$this->store_id = $store_id;
	}

	public function write($message)
	{
		$handle = fopen($this->file, 'a+');

		$log = date('Y-m-d G:i:s');
		$log .= "\t" . $_SERVER['REMOTE_ADDR'];
		$log .= "\t" . preg_replace("/\\?.*/", "", $_SERVER['REQUEST_URI']);
		$log .= "\t" . $_SERVER['QUERY_STRING'];
		$log .= "\t" . "Store ID: $this->store_id";
		$log .= "\t" . (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
		$log .= "\t" . str_replace("\n", "__nl__", $message);

		fwrite($handle, $log . "\r\n");

		fclose($handle);
	}
}
